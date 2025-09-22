<?php
// Idempotent environment validation and SQL runner for Namecheap (cPanel)
// Logs to storage/logs/install.log

date_default_timezone_set('UTC');

function log_line(string $line): void {
    $dir = __DIR__ . '/../storage/logs';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $msg = '['.date('Y-m-d H:i:s').'] '.$line."\n";
    file_put_contents($dir.'/install.log', $msg, FILE_APPEND);
    echo $msg;
}

function load_env(string $path): array {
    $env = [];
    if (!is_file($path)) {
        log_line("WARN: .env not found at $path — continuing with getenv().");
        return $_ENV + $_SERVER;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        if (strlen($val) && ($val[0] === '"' || $val[0] === "'")) {
            $val = trim($val, "\"'");
        }
        $env[$key] = $val;
    }
    return $env;
}

function validate_env(array $env): void {
    $ok = true;
    $host = $env['DB_HOST'] ?? getenv('DB_HOST');
    if ($host !== 'localhost') {
        $ok = false;
        log_line("ERROR: DB_HOST must be 'localhost' on Namecheap. Current: " . var_export($host, true));
    } else {
        log_line("OK: DB_HOST=localhost");
    }

    $db = $env['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $user = $env['DB_USERNAME'] ?? getenv('DB_USERNAME');
    if (!$db || !$user) {
        $ok = false;
        log_line('ERROR: DB_DATABASE or DB_USERNAME missing in environment.');
    } else {
        $dbPrefix = (strpos($db, '_') !== false) ? substr($db, 0, strpos($db, '_')) : null;
        $userPrefix = (strpos($user, '_') !== false) ? substr($user, 0, strpos($user, '_')) : null;
        if ($dbPrefix && $userPrefix && $dbPrefix === $userPrefix) {
            log_line("OK: Database and user share cPanel prefix '$dbPrefix' ");
        } else {
            log_line("WARN: Expected cPanel prefix to match (user: '$user', db: '$db').");
        }
    }

    if (!$ok) {
        log_line('WARN: Env validation reported issues — proceeding anyway.');
    }
}

function pdo_connect(array $env): PDO {
    $host = $env['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
    $db = $env['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $user = $env['DB_USERNAME'] ?? getenv('DB_USERNAME');
    $pass = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
    $charset = $env['DB_CHARSET'] ?? 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        log_line('OK: Connected to MySQL using PDO.');
        return $pdo;
    } catch (Throwable $e) {
        log_line('FATAL: DB connection failed: '.$e->getMessage());
        exit(1);
    }
}

function split_sql_statements(string $sql): array {
    $stmts = [];
    $buffer = '';
    $inString = false; $stringChar = '';
    $len = strlen($sql);
    for ($i=0; $i<$len; $i++) {
        $ch = $sql[$i];
        $next = $i+1 < $len ? $sql[$i+1] : '';
        if (!$inString && $ch === '-' && $next === '-') {
            while ($i < $len && $sql[$i] !== "\n") $i++;
            continue;
        }
        if (!$inString && $ch === '#') { // MySQL comment
            while ($i < $len && $sql[$i] !== "\n") $i++;
            continue;
        }
        if ($ch === '"' || $ch === "'") {
            if ($inString && $ch === $stringChar) {
                $inString = false; $stringChar = '';
            } elseif (!$inString) {
                $inString = true; $stringChar = $ch;
            }
            $buffer .= $ch; continue;
        }
        if (!$inString && $ch === ';') {
            $trim = trim($buffer);
            if ($trim !== '') $stmts[] = $trim;
            $buffer = '';
        } else {
            $buffer .= $ch;
        }
    }
    $trim = trim($buffer);
    if ($trim !== '') $stmts[] = $trim;
    return $stmts;
}

function run_sql_file(PDO $pdo, string $path): void {
    if (!is_file($path)) {
        log_line("SKIP: SQL file not found: $path");
        return;
    }
    log_line("RUN: $path");
    $sql = file_get_contents($path);
    $stmts = split_sql_statements($sql);
    $ok = 0; $fail = 0;
    // relax FK checks to allow out-of-order idempotent creation
    try { $pdo->exec('SET FOREIGN_KEY_CHECKS=0'); } catch (\Throwable $e) { /* ignore */ }
    foreach ($stmts as $stmt) {
        try {
            $pdo->exec($stmt);
            $ok++;
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            $ignorable = false;
            $lc = strtolower($msg);
            foreach (['already exists','duplicate entry','duplicate column','errno 1061','errno 1062','errno 1060','errno 1091'] as $needle) {
                if (str_contains($lc, $needle)) { $ignorable = true; break; }
            }
            if ($ignorable) {
                log_line('NOTE: Ignored idempotent error: '.$msg);
            } else {
                log_line('ERROR: '.$msg.' | SQL: '.substr($stmt,0,200));
                $fail++;
            }
        }
    }
    try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $e) { /* ignore */ }
    log_line("DONE: $path (executed=$ok, errors=$fail)");
}

// --- helpers for fallback decision ---
function has_migrations(PDO $pdo): bool {
    try {
        $rs = $pdo->query("SHOW TABLES LIKE 'migrations'");
        if ($rs && $rs->fetch()) return true;
    } catch (\Throwable $e) { /* ignore */ }
    return false;
}

// --- main ---
log_line('--- Setup start ---');
$root = realpath(__DIR__.'/..');
$env = load_env($root.'/.env');
validate_env($env);
$pdo = pdo_connect($env);

$args = $argv;
array_shift($args);
$fallback = in_array('--fallback', $args, true);

$schemaFiles = [
    $root.'/database/mysql/initial_schema.sql',
    $root.'/database/mysql/attendance_additions.sql',
    $root.'/docs/DATABASE-SCHEMA.sql',
];
$alwaysFiles = [
    $root.'/database/mysql/utility_tables.sql',
    $root.'/database/mysql/rbac_seed.sql',
];

$shouldRunSchema = true;
if ($fallback && has_migrations($pdo)) {
    log_line('Migrations table detected; skipping schema SQL (fallback mode).');
    $shouldRunSchema = false;
}

if ($shouldRunSchema) {
    foreach ($schemaFiles as $f) run_sql_file($pdo, $f);
}
foreach ($alwaysFiles as $f) run_sql_file($pdo, $f);

log_line('--- Setup completed ---');
exit(0);
