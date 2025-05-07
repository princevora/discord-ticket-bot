<?php

namespace Sqlite;

use PDO;

class Connection
{
    /**
     * @return bool|PDO
     */
    public static function getPDO(): bool|PDO
    {
        try {
            $pdo = new PDO('sqlite:' . $_ENV['SQLITE_PATH']);

            return $pdo;
        } catch (\Throwable $th) {
            return false;
        }
    }
}