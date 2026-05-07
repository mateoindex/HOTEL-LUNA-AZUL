<?php

namespace App\Core;

/**
 * render simple de vistas con layout
 */
class View {

    public static function render(string $view, array $data = [], string $layout = 'app'): void {
        extract($data, EXTR_SKIP);

        // las vistas se hacen cargo de su layout al final con
        //   $content = ob_get_clean(); require base_path('app/Views/layouts/...');
        // asi que aca solo incluyo la vista. el parametro $layout queda como fallback
        // para vistas que no se autoenvuelven.
        $viewFile = base_path('app/Views/' . str_replace('.', '/', $view) . '.php');
        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vista no existe: {$view}");
        }
        require $viewFile;
    }

    // render sin layout (para fragmentos ajax)
    public static function partial(string $view, array $data = []): string {
        extract($data, EXTR_SKIP);
        ob_start();
        require base_path('app/Views/' . str_replace('.', '/', $view) . '.php');
        return ob_get_clean();
    }
}
