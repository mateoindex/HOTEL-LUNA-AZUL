<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\PdfService;

class ReportController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('reports', 'view');
        $this->view('reports.index', ['pageTitle' => 'Reportes']);
    }

    public function reservationVoucher(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'view');

        $path = PdfService::generateVoucher((int) $id);
        if (!$path) {
            http_response_code(500);
            echo 'No se pudo generar el voucher. Verifique que Python y reportlab estén instalados.';
            return;
        }

        $r = Reservation::find((int) $id);
        AuditService::log('report.generate', ['type' => 'reservation', 'id' => (int) $id, 'code' => $r['code']], [
            'kind' => 'voucher',
            'file' => basename($path),
        ]);

        $this->streamPdf($path);
    }

    public function occupancy(): void {
        AuthMiddleware::require();
        Auth::require('reports', 'view');

        $start = (string) Request::query('start', date('Y-m-01'));
        $days  = (int) Request::query('days', 30);
        $days  = max(7, min(90, $days));

        $path = PdfService::generateOccupancy($start, $days);
        if (!$path) {
            http_response_code(500);
            echo 'No se pudo generar el reporte. Verifique Python y reportlab.';
            return;
        }
        AuditService::log('report.generate', ['type' => 'system', 'id' => 0], [
            'kind'  => 'occupancy',
            'start' => $start, 'days' => $days,
            'file'  => basename($path),
        ]);
        $this->streamPdf($path);
    }

    public function guestsList(): void {
        AuthMiddleware::require();
        Auth::require('reports', 'view');

        // PDF simple de ocupacion del mes (sin gráfica de huéspedes complejo;
        // este endpoint queda como placeholder mostrando la lista basica)
        $path = PdfService::generateOccupancy(date('Y-m-01'), 30, 'Listado mensual');
        if (!$path) {
            http_response_code(500);
            echo 'No se pudo generar el reporte.';
            return;
        }
        AuditService::log('report.generate', ['type' => 'system', 'id' => 0], ['kind' => 'guests_list']);
        $this->streamPdf($path);
    }

    private function streamPdf(string $path): void {
        $name = basename($path);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $name . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, max-age=0');
        readfile($path);
        exit;
    }
}
