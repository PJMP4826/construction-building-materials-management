<?php

use Dotenv\Dotenv;

require __DIR__ . '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();

use Infrastructure\Database\DatabaseConnection;

$conexion = DatabaseConnection::Conectar();

 if ($conexion) {
     echo "Conexion exitosa";
 } else {
     echo "Error en la conexion";
 }