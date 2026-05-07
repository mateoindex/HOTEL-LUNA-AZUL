<?php

namespace App\Services;

use App\Core\Database;

/**
 * logica anti-doble-reserva
 * dos defensas:
 *  - hasOverlap: rechaza al guardar
 *  - listAvailable: solo retorna habitaciones libres para el form
 */
class AvailabilityService {

    /**
     * busca solapamiento para una habitacion en un rango.
     * ignoreId = id de reserva a ignorar (para edicion).
     */
    public static function hasOverlap(int $roomId, string $checkIn, string $checkOut, int $ignoreId = 0): bool {
        $row = Database::fetch(
            "SELECT id FROM reservations
             WHERE room_id = ?
               AND id != ?
               AND status IN ('reservada','en_curso')
               AND check_in < ?
               AND check_out > ?
             LIMIT 1",
            [$roomId, $ignoreId, $checkOut, $checkIn]
        );
        return $row !== null;
    }

    /**
     * lista habitaciones disponibles para un rango.
     * filtra: status disponible + no solapadas con reservas vivas.
     */
    public static function listAvailable(string $checkIn, string $checkOut, int $ignoreId = 0): array {
        if (strtotime($checkOut) <= strtotime($checkIn)) return [];

        return Database::fetchAll(
            "SELECT r.*
             FROM rooms r
             WHERE r.status = 'disponible'
               AND r.id NOT IN (
                   SELECT res.room_id FROM reservations res
                   WHERE res.id != ?
                     AND res.status IN ('reservada','en_curso')
                     AND res.check_in < ?
                     AND res.check_out > ?
               )
             ORDER BY r.floor, r.code",
            [$ignoreId, $checkOut, $checkIn]
        );
    }
}
