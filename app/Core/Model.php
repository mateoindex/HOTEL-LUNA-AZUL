<?php

namespace App\Core;

abstract class Model {
    // los modelos heredan acceso conveniente a la DB
    protected static function db(): \PDO {
        return Database::pdo();
    }
}
