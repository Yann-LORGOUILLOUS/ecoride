<?php declare(strict_types=1);

final class PdoConnection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: '127.0.0.1');
        $db   = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'ecoride');
        $user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
        $pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: '');

        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }
}
