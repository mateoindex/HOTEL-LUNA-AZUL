<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * conexion PDO singleton
 * usa prepared statements siempre, nada de concatenar SQL
 */
class Database {

    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo !== null) return self::$pdo;

        $cfg = config('database');
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']
        );
        try {
            self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // en debug muestra, en prod loggear
            if (config('app.debug')) {
                throw $e;
            }
            http_response_code(500);
            exit('Error de conexion a la base de datos');
        }
        return self::$pdo;
    }

    // helpers cortos que usan los modelos
    public static function query(string $sql, array $params = []): \PDOStatement {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $sql, array $params = []): int {
        self::query($sql, $params);
        return (int) self::pdo()->lastInsertId();
    }
}
