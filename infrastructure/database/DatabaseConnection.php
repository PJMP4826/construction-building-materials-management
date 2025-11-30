<?php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $conn = null;

    public static function Conectar(): ?PDO
    {
        if (self::$conn === null) {
            try {

                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $username = $_ENV['DB_USERNAME'];
                $password = $_ENV['DB_PASSWORD'];

                self::$conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password");
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Conexion fallida: " . $e->getMessage();
                return null;
            }
        }
        return self::$conn;
    }
}