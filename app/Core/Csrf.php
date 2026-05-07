<?php

namespace App\Core;

class Csrf {

    public static function token(): string {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function check(?string $sent): bool {
        $real = $_SESSION['_csrf'] ?? null;
        if (!$real || !$sent) return false;
        return hash_equals($real, $sent);
    }

    public static function field(): string {
        // se usa como <?= Csrf::field() ?\> dentro de los forms
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }
}
