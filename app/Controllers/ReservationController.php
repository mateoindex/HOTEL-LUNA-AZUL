<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\AuditService;
use App\Services\AvailabilityService;

class ReservationController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'view');

        // por defecto del mes actual, estados activos
        $f = [
            'from'    => Request::query('from')    ?: date('Y-m-01'),
            'to'      => Request::query('to')      ?: date('Y-m-t'),
            'status'  => Request::query('status'),
            'room_id' => Request::query('room_id'),
        ];
        $rows = Reservation::listFiltered($f);
        $rooms = Room::all();

        $this->view('reservations.index', [
            'pageTitle' => 'Reservas',
            'rows'      => $rows,
            'f'         => $f,
            'rooms'     => $rooms,
        ]);
    }

    public function create(): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'create');
        $this->view('reservations.create', [
            'pageTitle' => 'Nueva reserva',
            'r'         => null,
            'guest'     => null,
        ]);
    }

    public function store(): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'create');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('guest_id',   'required|int', 'Huésped')
            ->check('room_id',    'required|int', 'Habitación')
            ->check('check_in',   'required|date', 'Fecha de entrada')
            ->check('check_out',  'required|date', 'Fecha de salida')
            ->check('adults',     'required|int', 'Adultos')
            ->check('total_amount', 'required|numeric', 'Total');

        if (!$v->fails() && strtotime($data['check_out']) <= strtotime($data['check_in'])) {
            $v->errors()['check_out'][] = 'La salida debe ser posterior a la entrada.';
        }

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/reservations/create');
            return;
        }

        // segunda defensa: anti-doble-reserva backend
        if (AvailabilityService::hasOverlap((int) $data['room_id'], $data['check_in'], $data['check_out'], 0)) {
            $this->withFlash('bad', 'Esta habitación ya tiene una reserva que se cruza con esas fechas.');
            $this->withOld($data);
            $this->redirect('/reservations/create');
            return;
        }

        $data['created_by'] = Auth::id();
        $id = Reservation::create($data);
        $created = Reservation::find($id);

        AuditService::log('reservation.create', ['type' => 'reservation', 'id' => $id, 'code' => $created['code']], [
            'guest_id'  => (int) $data['guest_id'],
            'room_id'   => (int) $data['room_id'],
            'check_in'  => $data['check_in'],
            'check_out' => $data['check_out'],
            'total'     => (float) $data['total_amount'],
        ]);

        $this->withFlash('ok', 'Reserva ' . $created['code'] . ' creada.');
        $this->redirect('/reservations/' . $id);
    }

    public function show(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'view');
        $r = Reservation::find((int) $id);
        if (!$r) { http_response_code(404); $this->view('errors.404'); return; }

        $this->view('reservations.show', ['pageTitle' => $r['code'], 'r' => $r]);
    }

    public function edit(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'edit');
        $r = Reservation::find((int) $id);
        if (!$r) { http_response_code(404); $this->view('errors.404'); return; }

        // huesped completo para mostrar nombre en autocomplete
        $guest = Guest::find((int) $r['guest_id']);
        $this->view('reservations.edit', ['pageTitle' => 'Editar ' . $r['code'], 'r' => $r, 'guest' => $guest]);
    }

    public function update(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'edit');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('guest_id',   'required|int', 'Huésped')
            ->check('room_id',    'required|int', 'Habitación')
            ->check('check_in',   'required|date', 'Fecha de entrada')
            ->check('check_out',  'required|date', 'Fecha de salida')
            ->check('adults',     'required|int', 'Adultos')
            ->check('total_amount', 'required|numeric', 'Total');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/reservations/' . $id . '/edit');
            return;
        }

        if (AvailabilityService::hasOverlap((int) $data['room_id'], $data['check_in'], $data['check_out'], (int) $id)) {
            $this->withFlash('bad', 'Esta habitación ya tiene otra reserva que se cruza con esas fechas.');
            $this->redirect('/reservations/' . $id . '/edit');
            return;
        }

        Reservation::update((int) $id, $data);
        AuditService::log('reservation.update', ['type' => 'reservation', 'id' => (int) $id], [
            'check_in'  => $data['check_in'],
            'check_out' => $data['check_out'],
            'total'     => (float) $data['total_amount'],
        ]);
        $this->withFlash('ok', 'Reserva actualizada.');
        $this->redirect('/reservations/' . $id);
    }

    public function cancel(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'edit');
        $this->ensureCsrf();
        Reservation::setStatus((int) $id, 'cancelada');
        AuditService::log('reservation.cancel', ['type' => 'reservation', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Reserva cancelada.');
        $this->redirect('/reservations/' . $id);
    }

    public function checkIn(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'edit');
        $this->ensureCsrf();
        Reservation::setStatus((int) $id, 'en_curso');
        AuditService::log('reservation.check_in', ['type' => 'reservation', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Entrada registrada.');
        $this->redirect('/reservations/' . $id);
    }

    public function checkOut(string $id): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'edit');
        $this->ensureCsrf();
        Reservation::setStatus((int) $id, 'finalizada');
        AuditService::log('reservation.check_out', ['type' => 'reservation', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Salida registrada.');
        $this->redirect('/reservations/' . $id);
    }

    public function apiAvailability(): void {
        AuthMiddleware::require();
        Auth::require('reservations', 'view');
        $from   = (string) Request::query('from', '');
        $to     = (string) Request::query('to', '');
        $ignore = (int) Request::query('ignore', 0);

        if (!$from || !$to || !strtotime($from) || !strtotime($to)) {
            $this->json(['rooms' => []]);
            return;
        }

        $rooms = AvailabilityService::listAvailable($from, $to, $ignore);
        $this->json(['rooms' => $rooms, 'from' => $from, 'to' => $to]);
    }
}
