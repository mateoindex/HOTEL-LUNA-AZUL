<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Room extends Model {

    public static function all(): array {
        return Database::fetchAll('SELECT * FROM rooms ORDER BY floor, code');
    }

    public static function find(int $id): ?array {
        return Database::fetch('SELECT * FROM rooms WHERE id = ?', [$id]);
    }

    public static function findByCode(string $code): ?array {
        return Database::fetch('SELECT * FROM rooms WHERE code = ?', [$code]);
    }

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO rooms (code, type, capacity, price_per_night, floor, status, description)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $d['code'], $d['type'], (int) $d['capacity'], (float) $d['price_per_night'],
                (int) $d['floor'], $d['status'] ?: 'disponible', $d['description'] ?: null,
            ]
        );
    }

    public static function update(int $id, array $d): void {
        Database::query(
            'UPDATE rooms SET code=?, type=?, capacity=?, price_per_night=?, floor=?, status=?, description=? WHERE id=?',
            [
                $d['code'], $d['type'], (int) $d['capacity'], (float) $d['price_per_night'],
                (int) $d['floor'], $d['status'] ?: 'disponible', $d['description'] ?: null,
                $id,
            ]
        );
    }

    public static function delete(int $id): void {
        Database::query('DELETE FROM rooms WHERE id = ?', [$id]);
    }

    public static function hasReservations(int $id): bool {
        $row = Database::fetch('SELECT COUNT(*) c FROM reservations WHERE room_id = ?', [$id]);
        return ((int) $row['c']) > 0;
    }

    /**
     * estado de ocupacion en una fecha dada (default hoy)
     * retorna mapa room_id => bool ocupada
     */
    public static function occupancyMap(string $date): array {
        $rows = Database::fetchAll(
            "SELECT room_id FROM reservations
             WHERE status IN ('reservada','en_curso')
               AND check_in <= ? AND check_out > ?",
            [$date, $date]
        );
        $map = [];
        foreach ($rows as $r) $map[(int) $r['room_id']] = true;
        return $map;
    }
}
