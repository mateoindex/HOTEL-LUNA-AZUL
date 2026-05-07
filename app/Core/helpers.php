<?php

// helpers globales - se cargan via composer "files"

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string {
        static $loaded = null;
        if ($loaded === null) {
            $loaded = [];
            $envFile = dirname(__DIR__, 2) . '/.env';
            if (is_file($envFile)) {
                foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#') continue;
                    [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
                    $v = trim($v, " \t\"'");
                    $loaded[trim($k)] = $v;
                }
            }
        }
        return $loaded[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    function config(string $path, $default = null) {
        static $cache = [];
        $parts = explode('.', $path);
        $file = array_shift($parts);
        if (!isset($cache[$file])) {
            $f = dirname(__DIR__, 2) . "/config/{$file}.php";
            $cache[$file] = is_file($f) ? require $f : [];
        }
        $val = $cache[$file];
        foreach ($parts as $p) {
            if (!is_array($val) || !array_key_exists($p, $val)) return $default;
            $val = $val[$p];
        }
        return $val;
    }
}

if (!function_exists('e')) {
    // escape para output html
    function e($v): string {
        return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('base_path')) {
    function base_path(string $sub = ''): string {
        $root = dirname(__DIR__, 2);
        return $sub === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim($sub, '/\\');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $base = rtrim((string) config('app.url'), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to): void {
        header('Location: ' . $to);
        exit;
    }
}

if (!function_exists('old')) {
    // recupera valores del form despues de un error
    function old(string $key, $default = '') {
        $data = $_SESSION['_old'] ?? [];
        return $data[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $val = null) {
        if ($val === null) {
            $v = $_SESSION['_flash'][$key] ?? null;
            if (isset($_SESSION['_flash'][$key])) unset($_SESSION['_flash'][$key]);
            return $v;
        }
        $_SESSION['_flash'][$key] = $val;
    }
}

if (!function_exists('money')) {
    // formato pesos colombianos sin centavos
    function money($value): string {
        return '$' . number_format((float) $value, 0, ',', '.');
    }
}

if (!function_exists('date_es')) {
    function date_es(?string $date, string $format = 'd \d\e M, Y'): string {
        if (!$date) return '';
        $months = ['Jan'=>'ene','Feb'=>'feb','Mar'=>'mar','Apr'=>'abr','May'=>'may','Jun'=>'jun','Jul'=>'jul','Aug'=>'ago','Sep'=>'sep','Oct'=>'oct','Nov'=>'nov','Dec'=>'dic'];
        $ts = strtotime($date);
        if (!$ts) return $date;
        return strtr(date($format, $ts), $months);
    }
}

if (!function_exists('today')) {
    function today(): string {
        return date('Y-m-d');
    }
}
