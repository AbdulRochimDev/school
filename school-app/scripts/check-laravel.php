<?php
// Quick Laravel installation validator for this repo structure
date_default_timezone_set('UTC');

$root = realpath(__DIR__.'/..');
$log = $root.'/storage/logs/install.log';
@mkdir(dirname($log), 0777, true);

function log_line($msg){ global $log; $line='['.date('Y-m-d H:i:s').'] '.$msg."\n"; file_put_contents($log, $line, FILE_APPEND); echo $line; }

$checks = [
    'artisan' => is_file($root.'/artisan'),
    'vendor' => is_file($root.'/vendor/autoload.php'),
    'bootstrap' => is_file($root.'/bootstrap/app.php'),
    'public' => is_file($root.'/public/index.php'),
    'config' => is_dir($root.'/config'),
];

$ok = true;
foreach ($checks as $name => $exists) {
    log_line(($exists ? 'OK' : 'MISSING').": $name");
    if (!$exists) $ok = false;
}

if (!$ok) {
    log_line('Laravel core not fully present. Follow GUIDE-LARAVEL-NAMECHEAP.md to install a fresh Laravel and overlay this code:');
    log_line('- composer create-project laravel/laravel:^10 school');
    log_line('- Copy this repo folders into that Laravel app: app/, routes/, database/, scripts/, docs/, env/, GUIDE-LARAVEL-NAMECHEAP.md');
    log_line('- Register providers (AuthServiceProvider, EventServiceProvider) in config/app.php if not auto-discovered');
    exit(2);
}

log_line('Laravel installation appears OK.');
exit(0);

