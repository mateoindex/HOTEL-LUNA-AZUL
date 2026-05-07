<?php

// autoload PSR-4 manual: si el evaluador no corrio composer dump-autoload, igual funciona
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $rel  = substr($class, strlen($prefix));
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $rel) . '.php';
    if (is_file($path)) require $path;
});
