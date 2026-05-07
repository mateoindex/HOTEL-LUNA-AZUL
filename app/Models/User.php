<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class User extends Model {

    public static function all(): array {
        return Database::fetchAll(
            'SELECT u.*, r.name AS role_name, r.display_name AS role_display
             FROM users u JOIN roles r ON r.id = u.role_id
             ORDER BY u.is_active DESC, u.full_name'
        );
    }

    public static function find(int $id): ?array {
        return Database::fetch(
            'SELECT u.*, r.name AS role_name, r.display_name AS role_display
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.id = ?',
            [$id]
        );
    }

    public static function findByEmail(string $email): ?array {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO users (role_id, full_name, email, password_hash, is_active)
             VALUES (?, ?, ?, ?, 1)',
            [$d['role_id'], $d['full_name'], $d['email'], password_hash($d['password'], PASSWORD_BCRYPT)]
        );
    }

    public static function update(int $id, array $d): void {
        if (!empty($d['password'])) {
            Database::query(
                'UPDATE users SET role_id=?, full_name=?, email=?, password_hash=? WHERE id=?',
                [$d['role_id'], $d['full_name'], $d['email'], password_hash($d['password'], PASSWORD_BCRYPT), $id]
            );
        } else {
            Database::query(
                'UPDATE users SET role_id=?, full_name=?, email=? WHERE id=?',
                [$d['role_id'], $d['full_name'], $d['email'], $id]
            );
        }
    }

    public static function deactivate(int $id): void {
        Database::query('UPDATE users SET is_active = 0 WHERE id = ?', [$id]);
    }

    public static function roles(): array {
        return Database::fetchAll('SELECT * FROM roles ORDER BY id');
    }
}
