<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Models\Guest;
use App\Services\AuditService;

class GuestController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('guests', 'view');

        $page   = max(1, (int) Request::query('page', 1));
        $search = (string) Request::query('q', '');
        $data   = Guest::paginate($page, 20, $search);

        $this->view('guests.index', [
            'pageTitle' => 'Huéspedes',
            'rows'      => $data['rows'],
            'total'     => $data['total'],
            'page'      => $page,
            'pages'     => $data['pages'],
            'search'    => $search,
        ]);
    }

    public function create(): void {
        AuthMiddleware::require();
        Auth::require('guests', 'create');
        $this->view('guests.create', ['pageTitle' => 'Nuevo huésped']);
    }

    public function store(): void {
        AuthMiddleware::require();
        Auth::require('guests', 'create');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('document_type', 'required|in:CC,CE,PAS', 'Tipo de documento')
            ->check('document_number', 'required|max:30', 'Número de documento')
            ->check('first_name', 'required|max:80', 'Nombres')
            ->check('last_name', 'required|max:80', 'Apellidos')
            ->check('email', 'email|max:120', 'Correo');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/guests/create');
            return;
        }

        try {
            $id = Guest::create($data);
        } catch (\PDOException $e) {
            $this->withFlash('bad', 'Ya existe un huésped con ese número de documento.');
            $this->withOld($data);
            $this->redirect('/guests/create');
            return;
        }

        AuditService::log('guest.create', ['type' => 'guest', 'id' => $id], [
            'document' => $data['document_number'],
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
        ]);
        $this->withFlash('ok', 'Huésped creado.');
        $this->redirect('/guests/' . $id);
    }

    public function show(string $id): void {
        AuthMiddleware::require();
        Auth::require('guests', 'view');
        $guest = Guest::find((int) $id);
        if (!$guest) { http_response_code(404); $this->view('errors.404'); return; }

        $reservations = Guest::reservations((int) $id);

        $this->view('guests.show', [
            'pageTitle'    => 'Huésped',
            'g'            => $guest,
            'reservations' => $reservations,
        ]);
    }

    public function edit(string $id): void {
        AuthMiddleware::require();
        Auth::require('guests', 'edit');
        $guest = Guest::find((int) $id);
        if (!$guest) { http_response_code(404); $this->view('errors.404'); return; }
        $this->view('guests.edit', ['pageTitle' => 'Editar huésped', 'g' => $guest]);
    }

    public function update(string $id): void {
        AuthMiddleware::require();
        Auth::require('guests', 'edit');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('document_type', 'required|in:CC,CE,PAS', 'Tipo de documento')
            ->check('document_number', 'required|max:30', 'Número de documento')
            ->check('first_name', 'required|max:80', 'Nombres')
            ->check('last_name', 'required|max:80', 'Apellidos')
            ->check('email', 'email|max:120', 'Correo');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld($data);
            $this->redirect('/guests/' . $id . '/edit');
            return;
        }

        try {
            Guest::update((int) $id, $data);
        } catch (\PDOException $e) {
            $this->withFlash('bad', 'Otro huésped ya usa ese documento.');
            $this->redirect('/guests/' . $id . '/edit');
            return;
        }

        AuditService::log('guest.update', ['type' => 'guest', 'id' => (int) $id], [
            'name' => $data['first_name'] . ' ' . $data['last_name'],
        ]);
        $this->withFlash('ok', 'Huésped actualizado.');
        $this->redirect('/guests/' . $id);
    }

    public function destroy(string $id): void {
        AuthMiddleware::require();
        Auth::require('guests', 'delete');
        $this->ensureCsrf();

        if (Guest::hasReservations((int) $id)) {
            $this->withFlash('bad', 'No se puede eliminar: el huésped tiene reservas asociadas.');
            $this->redirect('/guests/' . $id);
            return;
        }
        Guest::delete((int) $id);
        AuditService::log('guest.delete', ['type' => 'guest', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Huésped eliminado.');
        $this->redirect('/guests');
    }

    public function apiSearch(): void {
        AuthMiddleware::require();
        Auth::require('guests', 'view');
        $q = trim((string) Request::query('q', ''));
        $rows = $q !== '' ? Guest::search($q, 8) : [];
        $this->json(['guests' => $rows]);
    }
}
