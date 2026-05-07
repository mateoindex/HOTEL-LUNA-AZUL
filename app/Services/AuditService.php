<?php

namespace App\Services;

use App\Core\Auth;
use App\Core\Request;

/**
 * capa "no relacional": un archivo JSON por dia en /storage/audit/YYYY-MM-DD.json
 * cada llamada hace lock exclusivo + append
 */
class AuditService {

    public static function log(string $action, array $entity = [], array $data = []): void {
        try {
            $dir = base_path('storage/audit');
            if (!is_dir($dir)) @mkdir($dir, 0775, true);

            $file = $dir . DIRECTORY_SEPARATOR . date('Y-m-d') . '.json';

            $u = Auth::user();
            $entry = [
                'id'     => self::ulid(),
                'ts'     => date('c'),
                'actor'  => $u ? [
                    'id'   => (int) $u['id'],
                    'name' => $u['full_name'],
                    'role' => $u['role_name'],
                ] : ['id' => 0, 'name' => 'invitado', 'role' => 'guest'],
                'action' => $action,
                'entity' => $entity,
                'ip'     => Request::ip(),
                'ua'     => Request::ua(),
                'data'   => $data,
            ];

            // lock + append seguro
            $fp = fopen($file, 'c+');
            if (!$fp) return;
            try {
                if (flock($fp, LOCK_EX)) {
                    $existing = stream_get_contents($fp);
                    $list = $existing ? json_decode($existing, true) : [];
                    if (!is_array($list)) $list = [];
                    $list[] = $entry;
                    rewind($fp);
                    ftruncate($fp, 0);
                    fwrite($fp, json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    fflush($fp);
                    flock($fp, LOCK_UN);
                }
            } finally {
                fclose($fp);
            }
        } catch (\Throwable $e) {
            // auditoria no debe romper la app
        }
    }

    public static function readDay(string $date): array {
        $file = base_path('storage/audit') . DIRECTORY_SEPARATOR . $date . '.json';
        if (!is_file($file)) return [];
        $j = json_decode((string) file_get_contents($file), true);
        if (!is_array($j)) return [];
        // mas reciente primero
        return array_reverse($j);
    }

    public static function availableDates(): array {
        $dir = base_path('storage/audit');
        if (!is_dir($dir)) return [];
        $out = [];
        foreach (glob($dir . '/*.json') as $f) {
            $out[] = basename($f, '.json');
        }
        rsort($out);
        return $out;
    }

    // pseudo-ulid corto, sirve para identificar entradas
    private static function ulid(): string {
        return strtoupper(bin2hex(random_bytes(8)));
    }
}
