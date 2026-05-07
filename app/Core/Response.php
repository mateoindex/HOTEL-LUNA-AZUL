<?php

namespace App\Core;

class Response {

    public static function json($data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function notFound(): void {
        http_response_code(404);
        require base_path('app/Views/errors/404.php');
        exit;
    }

    public static function forbidden(): void {
        http_response_code(403);
        require base_path('app/Views/errors/403.php');
        exit;
    }

    public static function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($ref);
    }
}
