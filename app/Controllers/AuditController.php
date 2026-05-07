<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Services\AuditService;

class AuditController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('audit', 'view');

        $dates = AuditService::availableDates();
        $date  = (string) Request::query('date', $dates[0] ?? today());
        $entries = AuditService::readDay($date);

        // recolecta acciones distintas para el filtro
        $actions = [];
        foreach ($entries as $e) {
            $a = $e['action'] ?? '';
            if ($a !== '' && !in_array($a, $actions, true)) $actions[] = $a;
        }
        sort($actions);

        $filterAction = Request::query('action', '');
        $filterUser   = Request::query('user', '');

        if ($filterAction) {
            $entries = array_values(array_filter($entries, fn($e) => ($e['action'] ?? '') === $filterAction));
        }
        if ($filterUser) {
            $entries = array_values(array_filter($entries, fn($e) => str_contains(strtolower($e['actor']['name'] ?? ''), strtolower($filterUser))));
        }

        $this->view('audit.index', [
            'pageTitle'    => 'Auditoría',
            'dates'        => $dates,
            'date'         => $date,
            'entries'      => $entries,
            'actions'      => $actions,
            'filterAction' => $filterAction,
            'filterUser'   => $filterUser,
        ]);
    }

    public function apiList(): void {
        AuthMiddleware::require();
        Auth::require('audit', 'view');
        $date = (string) Request::query('date', today());
        $this->json(['entries' => AuditService::readDay($date)]);
    }
}
