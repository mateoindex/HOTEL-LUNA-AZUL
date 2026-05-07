<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Models\Room;
use App\Services\AuditService;

class RoomController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'view');

        $rooms = Room::all();
        $today = today();
        $occ = Room::occupancyMap($today);

        $this->view('rooms.index', [
            'pageTitle' => 'Habitaciones',
            'rooms'     => $rooms,
            'occ'       => $occ,
        ]);
    }

    public function create(): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'create');
        $this->view('rooms.create', ['pageTitle' => 'Nueva habitación']);
    }

    public function store(): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'create');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('code', 'required|max:10', 'Código')
            ->check('type', 'required|in:estandar,superior,suite', 'Tipo')
            ->check('capacity', 'required|int', 'Capacidad')
            ->check('price_per_night', 'required|numeric', 'Precio')
            ->check('floor', 'required|int', 'Piso')
            ->check('status', 'required|in:disponible,mantenimiento,fuera_servicio', 'Estado');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/rooms/create');
            return;
        }

        try {
            $id = Room::create($data);
        } catch (\PDOException $e) {
            $this->withFlash('bad', 'Ya existe una habitación con ese código.');
            $this->withOld($data);
            $this->redirect('/rooms/create');
            return;
        }
        AuditService::log('room.create', ['type' => 'room', 'id' => $id], ['code' => $data['code']]);
        $this->withFlash('ok', 'Habitación creada.');
        $this->redirect('/rooms');
    }

    public function edit(string $id): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'edit');
        $room = Room::find((int) $id);
        if (!$room) { http_response_code(404); $this->view('errors.404'); return; }
        $this->view('rooms.edit', ['pageTitle' => 'Editar habitación', 'r' => $room]);
    }

    public function update(string $id): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'edit');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('code', 'required|max:10', 'Código')
            ->check('type', 'required|in:estandar,superior,suite', 'Tipo')
            ->check('capacity', 'required|int', 'Capacidad')
            ->check('price_per_night', 'required|numeric', 'Precio')
            ->check('floor', 'required|int', 'Piso')
            ->check('status', 'required|in:disponible,mantenimiento,fuera_servicio', 'Estado');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/rooms/' . $id . '/edit');
            return;
        }

        $before = Room::find((int) $id);
        Room::update((int) $id, $data);

        $action = ($before && $before['status'] !== $data['status']) ? 'room.status_change' : 'room.update';
        AuditService::log($action, ['type' => 'room', 'id' => (int) $id], [
            'code'   => $data['code'],
            'before' => $before['status'] ?? null,
            'after'  => $data['status'],
        ]);
        $this->withFlash('ok', 'Habitación actualizada.');
        $this->redirect('/rooms');
    }

    public function destroy(string $id): void {
        AuthMiddleware::require();
        Auth::require('rooms', 'delete');
        $this->ensureCsrf();
        if (Room::hasReservations((int) $id)) {
            $this->withFlash('bad', 'No se puede eliminar: la habitación tiene reservas asociadas.');
            $this->redirect('/rooms');
            return;
        }
        Room::delete((int) $id);
        AuditService::log('room.delete', ['type' => 'room', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Habitación eliminada.');
        $this->redirect('/rooms');
    }
}
