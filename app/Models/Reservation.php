<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Reservation extends Model {

    public static function find(int $id): ?array {
        return Database::fetch(
            'SELECT r.*,
                    g.first_name, g.last_name, g.document_type, g.document_number, g.phone, g.email AS guest_email,
                    ro.code AS room_code, ro.type AS room_type, ro.capacity, ro.price_per_night, ro.floor,
                    u.full_name AS created_by_name
             FROM reservations r
             JOIN guests g ON g.id = r.guest_id
             JOIN rooms  ro ON ro.id = r.room_id
             JOIN users  u  ON u.id  = r.created_by
             WHERE r.id = ?',
            [$id]
        );
    }

    public static function listFiltered(array $f): array {
        $sql = 'SELECT r.*,
                       g.first_name, g.last_name, g.document_number,
                       ro.code AS room_code, ro.type AS room_type
                FROM reservations r
                JOIN guests g ON g.id = r.guest_id
                JOIN rooms ro ON ro.id = r.room_id
                WHERE 1=1 ';
        $p = [];
        if (!empty($f['from'])) {
            $sql .= ' AND r.check_in >= ?';
            $p[] = $f['from'];
        }
        if (!empty($f['to'])) {
            $sql .= ' AND r.check_out <= ?';
            $p[] = $f['to'];
        }
        if (!empty($f['status'])) {
            $sql .= ' AND r.status = ?';
            $p[] = $f['status'];
        }
        if (!empty($f['room_id'])) {
            $sql .= ' AND r.room_id = ?';
            $p[] = (int) $f['room_id'];
        }
        $sql .= ' ORDER BY r.check_in DESC, r.id DESC LIMIT 200';
        return Database::fetchAll($sql, $p);
    }

    public static function create(array $d): int {
        // genera codigo LA-YYYY-NNNN
        $code = self::nextCode();
        return Database::insert(
            'INSERT INTO reservations (code, guest_id, room_id, created_by, check_in, check_out, adults, children, total_amount, status, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $code, (int) $d['guest_id'], (int) $d['room_id'], (int) $d['created_by'],
                $d['check_in'], $d['check_out'],
                (int) ($d['adults'] ?? 1), (int) ($d['children'] ?? 0),
                (float) $d['total_amount'],
                $d['status'] ?? 'reservada',
                $d['notes'] ?: null,
            ]
        );
    }

    public static function update(int $id, array $d): void {
        Database::query(
            'UPDATE reservations
             SET guest_id=?, room_id=?, check_in=?, check_out=?, adults=?, children=?, total_amount=?, notes=?
             WHERE id=?',
            [
                (int) $d['guest_id'], (int) $d['room_id'],
                $d['check_in'], $d['check_out'],
                (int) ($d['adults'] ?? 1), (int) ($d['children'] ?? 0),
                (float) $d['total_amount'],
                $d['notes'] ?: null,
                $id,
            ]
        );
    }

    public static function setStatus(int $id, string $status): void {
        Database::query('UPDATE reservations SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function nextCode(): string {
        $year = date('Y');
        $row = Database::fetch(
            "SELECT code FROM reservations WHERE code LIKE ? ORDER BY id DESC LIMIT 1",
            ["LA-{$year}-%"]
        );
        $n = 0;
        if ($row && preg_match('/-(\d+)$/', $row['code'], $m)) {
            $n = (int) $m[1];
        }
        return sprintf('LA-%s-%04d', $year, $n + 1);
    }

    public static function arrivalsToday(): array {
        return Database::fetchAll(
            "SELECT r.*, g.first_name, g.last_name, ro.code AS room_code
             FROM reservations r
             JOIN guests g ON g.id = r.guest_id
             JOIN rooms ro ON ro.id = r.room_id
             WHERE r.check_in = CURDATE() AND r.status IN ('reservada','en_curso')
             ORDER BY ro.code"
        );
    }

    public static function departuresToday(): array {
        return Database::fetchAll(
            "SELECT r.*, g.first_name, g.last_name, ro.code AS room_code
             FROM reservations r
             JOIN guests g ON g.id = r.guest_id
             JOIN rooms ro ON ro.id = r.room_id
             WHERE r.check_out = CURDATE() AND r.status IN ('reservada','en_curso')
             ORDER BY ro.code"
        );
    }

    public static function activeCount(): int {
        $r = Database::fetch("SELECT COUNT(*) c FROM reservations WHERE status IN ('reservada','en_curso')");
        return (int) $r['c'];
    }

    public static function occupancyToday(): float {
        $total = (int) Database::fetch('SELECT COUNT(*) c FROM rooms WHERE status != "fuera_servicio"')['c'];
        if ($total === 0) return 0.0;
        $busy = (int) Database::fetch(
            "SELECT COUNT(DISTINCT room_id) c FROM reservations
             WHERE status IN ('reservada','en_curso')
               AND check_in <= CURDATE() AND check_out > CURDATE()"
        )['c'];
        return round(($busy / $total) * 100, 1);
    }

    /**
     * ocupacion para un rango de N dias (para grafico semanal)
     */
    public static function weeklyOccupancy(string $startDate, int $days = 7): array {
        $totalRooms = (int) Database::fetch('SELECT COUNT(*) c FROM rooms WHERE status != "fuera_servicio"')['c'];
        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $d = date('Y-m-d', strtotime($startDate . " +{$i} day"));
            $busy = (int) Database::fetch(
                "SELECT COUNT(DISTINCT room_id) c FROM reservations
                 WHERE status IN ('reservada','en_curso','finalizada')
                   AND check_in <= ? AND check_out > ?",
                [$d, $d]
            )['c'];
            $pct = $totalRooms > 0 ? round(($busy / $totalRooms) * 100) : 0;
            $out[] = ['date' => $d, 'pct' => $pct, 'busy' => $busy];
        }
        return $out;
    }
}
