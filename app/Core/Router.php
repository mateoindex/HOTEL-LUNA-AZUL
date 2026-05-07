<?php

namespace App\Core;

/**
 * router minimo: array de rutas, regex sobre {param}
 * uso: Router::get('/path', [Ctrl::class, 'method']);
 */
class Router {

    private static array $routes = [];

    public static function get(string $path, array $action): void  { self::add('GET', $path, $action); }
    public static function post(string $path, array $action): void { self::add('POST', $path, $action); }

    private static function add(string $method, string $path, array $action): void {
        // /reservations/{id} -> regex
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);
        self::$routes[] = [
            'method' => $method,
            'path'   => $path,
            'regex'  => '#^' . $regex . '$#',
            'action' => $action,
        ];
    }

    public static function dispatch(): void {
        $method = Request::method();
        $path   = Request::path();

        foreach (self::$routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['regex'], $path, $m)) {
                $params = [];
                foreach ($m as $k => $v) {
                    if (!is_int($k)) $params[$k] = $v;
                }
                [$class, $fn] = $r['action'];
                $instance = new $class();
                call_user_func_array([$instance, $fn], array_values($params));
                return;
            }
        }
        Response::notFound();
    }
}
