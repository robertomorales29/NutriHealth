<?php
require_once __DIR__ . '/app.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = 'localhost';
        $database = 'sitio_salud';
        $user = 'root';
        $password = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $user, $password, $options);
    }

    return $pdo;
}
