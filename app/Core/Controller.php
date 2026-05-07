<?php

namespace App\Core;

/**
 * controller base, render y redirects con flash
 */
abstract class Controller {

    protected function view(string $view, array $data = [], string $layout = 'app'): void {
        View::render($view, $data, $layout);
    }

    protected function json($data, int $code = 200): void {
        Response::json($data, $code);
    }

    protected function redirect(string $to): void {
        redirect($to);
    }

    protected function back(): void {
        Response::back();
    }

    protected function withFlash(string $type, string $msg): void {
        flash($type, $msg);
    }

    protected function withOld(array $input): void {
        $_SESSION['_old'] = $input;
    }

    protected function withErrors(array $errors): void {
        $_SESSION['_errors'] = $errors;
    }

    protected function ensureCsrf(): void {
        if (!Csrf::check(Request::input('_token'))) {
            http_response_code(419);
            exit('CSRF inválido. Recargue la página.');
        }
    }
}
