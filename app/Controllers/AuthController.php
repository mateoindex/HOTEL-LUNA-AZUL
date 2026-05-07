<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Services\AuditService;

class AuthController extends Controller {

    public function showLogin(): void {
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }
        $error = flash('login_error');
        $this->view('auth.login', ['error' => $error, 'pageTitle' => 'Ingreso'], 'guest');
    }

    public function login(): void {
        $this->ensureCsrf();

        $email = trim((string) Request::input('email', ''));
        $password = (string) Request::input('password', '');

        $this->withOld(['email' => $email]);

        if ($email === '' || $password === '') {
            $this->withFlash('login_error', 'Ingrese correo y contraseña.');
            $this->redirect('/login');
            return;
        }

        // bloqueo simple por intentos
        if (!$this->withinAttemptLimit($email)) {
            AuditService::log('auth.login.blocked', ['type' => 'user', 'id' => 0], ['email' => $email]);
            $this->withFlash('login_error', 'Demasiados intentos. Intente en unos minutos.');
            $this->redirect('/login');
            return;
        }

        if (!Auth::attempt($email, $password)) {
            $this->bumpAttempt($email);
            AuditService::log('auth.login.failed', ['type' => 'user', 'id' => 0], ['email' => $email]);
            $this->withFlash('login_error', 'Credenciales no válidas.');
            $this->redirect('/login');
            return;
        }

        $this->resetAttempts($email);
        AuditService::log('auth.login.success', ['type' => 'user', 'id' => Auth::id()], []);
        $this->redirect('/dashboard');
    }

    public function logout(): void {
        $this->ensureCsrf();
        AuditService::log('auth.logout', ['type' => 'user', 'id' => Auth::id() ?? 0], []);
        Auth::logout();
        $this->redirect('/login');
    }

    // intentos en sesion (no apunta a alta concurrencia, alcanza para esta app)
    private function withinAttemptLimit(string $email): bool {
        $key = 'attempts_' . md5($email);
        $now = time();
        $row = $_SESSION[$key] ?? ['count' => 0, 'first' => $now];
        if ($now - $row['first'] > 600) {
            $row = ['count' => 0, 'first' => $now];
        }
        return $row['count'] < 5;
    }

    private function bumpAttempt(string $email): void {
        $key = 'attempts_' . md5($email);
        $now = time();
        $row = $_SESSION[$key] ?? ['count' => 0, 'first' => $now];
        if ($now - $row['first'] > 600) $row = ['count' => 0, 'first' => $now];
        $row['count']++;
        $_SESSION[$key] = $row;
    }

    private function resetAttempts(string $email): void {
        unset($_SESSION['attempts_' . md5($email)]);
    }
}
