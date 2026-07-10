<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Thin wrapper that lazily builds and shares a single PDO connection.
 *
 * Replaces the old `global $connection` pattern: models call
 * `Database::connection()` instead of reaching for a global.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Return the shared PDO connection, building it on first use from
     * config/database.php.
     */
    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['database']
        );

        try {
            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Database connection failed: ' . $e->getMessage());
        }

        return self::$connection;
    }

    /** Allow tests to inject a connection (e.g. an in-memory SQLite PDO). */
    public static function swap(PDO $pdo): void
    {
        self::$connection = $pdo;
    }
}
