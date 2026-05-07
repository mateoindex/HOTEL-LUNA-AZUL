<?php

namespace App\Services;

use App\Models\Reservation;

class PdfService {

    /**
     * genera el voucher de una reserva via Python (reportlab)
     * retorna ruta absoluta al PDF
     */
    public static function generateVoucher(int $reservationId): ?string {
        $r = Reservation::find($reservationId);
        if (!$r) return null;

        $payload = [
            'code'           => $r['code'],
            'first_name'     => $r['first_name'],
            'last_name'      => $r['last_name'],
            'document_type'  => $r['document_type'],
            'document_number'=> $r['document_number'],
            'phone'          => $r['phone'],
            'guest_email'    => $r['guest_email'],
            'room_code'      => $r['room_code'],
            'room_type'      => $r['room_type'],
            'capacity'       => (int) $r['capacity'],
            'floor'          => (int) $r['floor'],
            'price_per_night'=> (float) $r['price_per_night'],
            'check_in'       => $r['check_in'],
            'check_out'      => $r['check_out'],
            'nights'         => (int) $r['nights'],
            'adults'         => (int) $r['adults'],
            'children'       => (int) $r['children'],
            'total_amount'   => (float) $r['total_amount'],
            'status'         => $r['status'],
            'notes'          => $r['notes'],
            'created_by_name'=> $r['created_by_name'],
        ];

        $reportsDir = base_path('storage/reports');
        if (!is_dir($reportsDir)) @mkdir($reportsDir, 0775, true);

        $jsonFile = tempnam(sys_get_temp_dir(), 'luna_');
        file_put_contents($jsonFile, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $outFile = $reportsDir . DIRECTORY_SEPARATOR . 'voucher-' . $r['code'] . '.pdf';
        $script = base_path('python/generate_reservation_pdf.py');

        self::runPython($script, $jsonFile, $outFile);
        @unlink($jsonFile);

        return is_file($outFile) ? $outFile : null;
    }

    public static function generateOccupancy(string $startDate, int $days = 30, ?string $title = null): ?string {
        $week = Reservation::weeklyOccupancy($startDate, $days);

        $payload = [
            'days'     => $week,
            'title'    => $title ?: ('Ocupación · ' . date_es($startDate)),
            'subtitle' => 'Hotel Luna Azul · ' . $days . ' días desde ' . date_es($startDate),
        ];

        $reportsDir = base_path('storage/reports');
        if (!is_dir($reportsDir)) @mkdir($reportsDir, 0775, true);

        $jsonFile = tempnam(sys_get_temp_dir(), 'luna_');
        file_put_contents($jsonFile, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $outFile = $reportsDir . DIRECTORY_SEPARATOR . 'ocupacion-' . $startDate . '.pdf';
        $script = base_path('python/generate_occupancy_report.py');

        self::runPython($script, $jsonFile, $outFile);
        @unlink($jsonFile);

        return is_file($outFile) ? $outFile : null;
    }

    private static function runPython(string $script, string $jsonFile, string $outFile): void {
        $py = config('app.python_bin');

        // si la ruta del config no existe, intenta el fallback del venv
        if (!is_file($py)) {
            $py = base_path('python/venv/Scripts/python.exe');
        }
        if (!is_file($py)) return; // python no disponible

        $cmd = sprintf(
            '"%s" "%s" --data-file "%s" --out "%s" 2>&1',
            $py, $script, $jsonFile, $outFile
        );
        @shell_exec($cmd);
    }
}
