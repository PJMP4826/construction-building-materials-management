<?php

use Dotenv\Dotenv;

require __DIR__ . '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Infrastructure\Database\DatabaseConnection;
use Infrastructure\Http\Router;
use Api\Controllers\MaterialesController;
use App\Handlers\Query\ListarMaterialesHandler;
use Infrastructure\Read\Repository\MaterialReadRepository;

//$conexion = DatabaseConnection::Conectar();

$router = new Router();

$router->get('/api/get-materials', function () {
    $respository = new MaterialReadRepository(DatabaseConnection::Conectar());
    $handler = new ListarMaterialesHandler($respository);
    $controller = new MaterialesController($handler);
    $controller->getMaterials();
});

$router->run();