<?php
require __DIR__.'/setup.php';
// setup.php already validates env and logs. To avoid re-running SQL here, we only call validation.

function main(): void {
    $root = realpath(__DIR__.'/..');
    $env = (function($root){
        $rf = new ReflectionFunction('load_env');
        return load_env($root.'/.env');
    })($root);
    validate_env($env);
}

main();

