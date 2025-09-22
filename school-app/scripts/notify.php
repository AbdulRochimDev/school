<?php
// Minimal notifier: logs and optional DB insert into `notifications` table if exists
date_default_timezone_set('UTC');
function log_line(string $line): void {
    $dir = __DIR__ . '/../storage/logs';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $msg = '['.date('Y-m-d H:i:s').'] '.$line."\n";
    file_put_contents($dir.'/install.log', $msg, FILE_APPEND);
    echo $msg;
}

$type = $argv[1] ?? 'info';
$message = $argv[2] ?? '';
log_line("NOTIFY: $type - $message");

// Try to insert into notifications table if env present
require_once __DIR__.'/setup.php';
$env = load_env(realpath(__DIR__.'/..').'/.env');
try {
    $pdo = pdo_connect($env);
    // Check table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt && $stmt->fetch()) {
        $userId = 1; // default admin user if exists
        $dataJson = json_encode(['message'=>$message,'type'=>$type]);
        $pdo->prepare("INSERT INTO notifications (user_id, type, data, read_at) VALUES (?,?,?,NULL)")
            ->execute([$userId, $type, $dataJson]);
    }
} catch (\Throwable $e) {
    // ignore
}

