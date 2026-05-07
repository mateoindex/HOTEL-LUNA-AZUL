<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Guest extends Model {

    public static function paginate(int $page = 1, int $perPage = 20, string $search = ''): array {
        $offset = max(0, ($page - 1) * $perPage);
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = 'WHERE first_name LIKE ? OR last_name LIKE ? OR document_number LIKE ? OR email LIKE ?';
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }
        $total = (int) Database::fetch("SELECT COUNT(*) c FROM guests {$where}", $params)['c'];
        $rows = Database::fetchAll(
            "SELECT * FROM guests {$where} ORDER BY last_name, first_name LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return [
            'rows'  => $rows,
            'total' => $total,
            'page'  => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public static function find(int $id): ?array {
        return Database::fetch('SELECT * FROM guests WHERE id = ?', [$id]);
    }

    public static function search(string $q, int $limit = 8): array {
        $like = '%' . $q . '%';
        return Database::fetchAll(
            'SELECT id, document_type, document_number, first_name, last_name
             FROM guests
             WHERE first_name LIKE ? OR last_name LIKE ? OR document_number LIKE ?
             ORDER BY last_name, first_name LIMIT ' . (int) $limit,
            [$like, $like, $like]
        );
    }

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO guests (document_type, document_number, first_name, last_name, email, phone, country, city, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $d['document_type'], $d['document_number'], $d['first_name'], $d['last_name'],
                $d['email'] ?: null, $d['phone'] ?: null,
                $d['country'] ?: 'Colombia', $d['city'] ?: null,
                $d['notes'] ?: null,
            ]
        );
    }

    public static function update(int $id, array $d): void {
        Database::query(
            'UPDATE guests SET document_type=?, document_number=?, first_name=?, last_name=?,
             email=?, phone=?, country=?, city=?, notes=? WHERE id=?',
            [
                $d['document_type'], $d['document_number'], $d['first_name'], $d['last_name'],
                $d['email'] ?: null, $d['phone'] ?: null,
                $d['country'] ?: 'Colombia', $d['city'] ?: null,
                $d['notes'] ?: null,
                $id,
            ]
        );
    }

    public static function delete(int $id): void {
        Database::query('DELETE FROM guests WHERE id = ?', [$id]);
    }

    public static function reservations(int $guestId): array {
        return Database::fetchAll(
            'SELECT r.*, ro.code AS room_code, ro.type AS room_type
             FROM reservations r JOIN rooms ro ON ro.id = r.room_id
             WHERE r.guest_id = ?
             ORDER BY r.check_in DESC',
            [$guestId]
        );
    }

    public static function hasReservations(int $id): bool {
        $row = Database::fetch('SELECT COUNT(*) c FROM reservations WHERE guest_id = ?', [$id]);
        return ((int) $row['c']) > 0;
    }
}
