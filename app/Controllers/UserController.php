<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Models\User;
use App\Services\AuditService;

class UserController extends Controller {

    public function index(): void {
        AuthMiddleware::require();
        Auth::require('users', 'view');
        $rows = User::all();
        $this->view('users.index', ['pageTitle' => 'Usuarios', 'rows' => $rows]);
    }

    public function create(): void {
        AuthMiddleware::require();
        Auth::require('users', 'create');
        $this->view('users.create', ['pageTitle' => 'Nuevo usuario', 'roles' => User::roles()]);
    }

    public function store(): void {
        AuthMiddleware::require();
        Auth::require('users', 'create');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('full_name', 'required|max:120', 'Nombre completo')
            ->check('email', 'required|email|max:120', 'Correo')
            ->check('password', 'required|min:6', 'Contraseña')
            ->check('role_id', 'required|int', 'Rol');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->withOld(array_diff_key($data, ['password' => 1]));
            $this->redirect('/users/create');
            return;
        }

        try {
            $id = User::create($data);
        } catch (\PDOException $e) {
            $this->withFlash('bad', 'Ya existe un usuario con ese correo.');
            $this->redirect('/users/create');
            return;
        }
        AuditService::log('user.create', ['type' => 'user', 'id' => $id], ['email' => $data['email']]);
        $this->withFlash('ok', 'Usuario creado.');
        $this->redirect('/users');
    }

    public function edit(string $id): void {
        AuthMiddleware::require();
        Auth::require('users', 'edit');
        $u = User::find((int) $id);
        if (!$u) { http_response_code(404); $this->view('errors.404'); return; }
        $this->view('users.edit', ['pageTitle' => 'Editar usuario', 'u' => $u, 'roles' => User::roles()]);
    }

    public function update(string $id): void {
        AuthMiddleware::require();
        Auth::require('users', 'edit');
        $this->ensureCsrf();

        $data = Request::all();
        $v = Validator::make($data)
            ->check('full_name', 'required|max:120', 'Nombre completo')
            ->check('email', 'required|email|max:120', 'Correo')
            ->check('role_id', 'required|int', 'Rol');

        if ($v->fails()) {
            $this->withErrors($v->firstErrors());
            $this->redirect('/users/' . $id . '/edit');
            return;
        }

        try {
            User::update((int) $id, $data);
        } catch (\PDOException $e) {
            $this->withFlash('bad', 'Otro usuario ya usa ese correo.');
            $this->redirect('/users/' . $id . '/edit');
            return;
        }
        AuditService::log('user.update', ['type' => 'user', 'id' => (int) $id], ['email' => $data['email']]);
        $this->withFlash('ok', 'Usuario actualizado.');
        $this->redirect('/users');
    }

    public function deactivate(string $id): void {
        AuthMiddleware::require();
        Auth::require('users', 'edit');
        $this->ensureCsrf();

        if ((int) $id === Auth::id()) {
            $this->withFlash('bad', 'No puede desactivarse a sí mismo.');
            $this->redirect('/users');
            return;
        }
        User::deactivate((int) $id);
        AuditService::log('user.deactivate', ['type' => 'user', 'id' => (int) $id], []);
        $this->withFlash('ok', 'Usuario desactivado.');
        $this->redirect('/users');
    }
}
