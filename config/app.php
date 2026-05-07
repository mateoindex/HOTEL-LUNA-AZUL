<?php

return [
    'name'    => env('APP_NAME', 'Hotel Luna Azul'),
    'env'     => env('APP_ENV', 'local'),
    'debug'   => env('APP_DEBUG', 'false') === 'true',
    'url'     => env('APP_URL', 'http://hotel-luna-azul.test'),
    'tz'      => env('TZ', 'America/Bogota'),

    'session_name' => env('SESSION_NAME', 'luna_sess'),

    // ruta al python del venv, se invoca desde PdfService
    'python_bin' => env('PYTHON_BIN', 'python'),

    // rutas absolutas, se setean en index.php
    'paths' => [
        'root'    => '',
        'app'     => '',
        'views'   => '',
        'storage' => '',
        'public'  => '',
    ],
];
