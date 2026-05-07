<?php

namespace App\Core;

class Request {

    public static function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $p = parse_url($uri, PHP_URL_PATH) ?: '/';
        // quita slash final excepto raiz
        $p = rtrim($p, '/');
        return $p === '' ? '/' : $p;
    }

    public static function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array {
        return array_merge($_GET, $_POST);
    }

    public static function isAjax(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public static function ip(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function ua(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public static function query(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}
