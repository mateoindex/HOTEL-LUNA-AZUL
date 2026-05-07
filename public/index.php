<?php

// front controller unico

require __DIR__ . '/../app/Core/Autoload.php';
require __DIR__ . '/../app/Core/helpers.php';

// si compuser dump-autoload corrio, usalo (tiene optimizado)
$composer = __DIR__ . '/../vendor/autoload.php';
if (is_file($composer)) require $composer;

date_default_timezone_set(config('app.tz', 'America/Bogota'));

// errores en debug
if (config('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

App\Core\Session::start();

// cargar definicion de rutas
require __DIR__ . '/../config/routes.php';

App\Core\Router::dispatch();
