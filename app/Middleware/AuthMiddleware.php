<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware {
    public static function require(): void {
        if (!Auth::check()) {
            redirect('/login');
        }
    }
}
