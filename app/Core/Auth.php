<?php

namespace App\Core;

use App\Models\User;

class Auth {

    private static ?array $cached = null;

    public static function attempt(string $email, string $password): bool {
        $user = Database::fetch(
            'SELECT u.*, r.name AS role_name, r.display_name AS role_display
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.email = ? AND u.is_active = 1 LIMIT 1',
            [$email]
        );
        if (!$user) return false;
        if (!password_verify($password, $user['password_hash'])) return false;

        Session::regenerate();
        $_SESSION['user_id'] = $user['id'];
        self::$cached = $user;

        // ultimo login
        Database::query('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
        return true;
    }

    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        if (self::$cached !== null) return self::$cached;
        $u = Database::fetch(
            'SELECT u.*, r.name AS role_name, r.display_name AS role_display
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? LIMIT 1',
            [$_SESSION['user_id']]
        );
        self::$cached = $u;
        return $u;
    }

    public static function id(): ?int {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    public static function logout(): void {
        Session::destroy();
        self::$cached = null;
    }

    /**
     * permiso por modulo + accion
     * action: view | create | edit | delete
     */
    public static function can(string $module, string $action): bool {
        $u = self::user();
        if (!$u) return false;
        $col = 'can_' . $action;
        $row = Database::fetch(
            "SELECT {$col} AS allowed FROM permissions WHERE role_id = ? AND module = ? LIMIT 1",
            [$u['role_id'], $module]
        );
        return $row && (int) $row['allowed'] === 1;
    }

    public static function require(string $module, string $action): void {
        if (!self::can($module, $action)) {
            http_response_code(403);
            require base_path('app/Views/errors/403.php');
            exit;
        }
    }

    public static function isRole(string $role): bool {
        $u = self::user();
        return $u && $u['role_name'] === $role;
    }
}
