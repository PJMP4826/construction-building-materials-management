<?php

use App\Command\CrearMaterialCommand;
use App\Dispacher\Bus;
use App\Handlers\Command\CrearMaterialCommandHandler;
use App\Query\ListarMaterialesQuery;
use Dotenv\Dotenv;

require __DIR__ . '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Infrastructure\Database\DatabaseConnection;
use Infrastructure\Http\Router;
use Api\Controllers\MaterialesController;
use App\Handlers\Query\ListarMaterialesHandler;
use Infrastructure\Read\Repository\MaterialReadRepository;
use Infrastructure\Write\Repository\MaterialWriteRepository;

//$conexion = DatabaseConnection::Conectar();

$router = new Router();

$router->get('/api/get-materials', function () {
    $respository = new MaterialReadRepository(DatabaseConnection::Conectar());
    $handler = new ListarMaterialesHandler($respository);

    $bus = new Bus();
    $bus->register(ListarMaterialesQuery::class, $handler);

    $controller = new MaterialesController($bus);
    $controller->getMaterials();
});

$router->post('/api/create-materials', function () {
    $db = DatabaseConnection::Conectar();

    $readRepository = new MaterialReadRepository($db);
    $writeRepository = new MaterialWriteRepository($db);

    //Command Handlers
    $listarMaterialHandler = new ListarMaterialesHandler($readRepository);
    $createMaterialHandler = new CrearMaterialCommandHandler(
        $readRepository,
        $writeRepository
    );

    $bus = new Bus();

    //query handlers
    $bus->register(ListarMaterialesQuery::class, $listarMaterialHandler);
    $bus->register(CrearMaterialCommand::class, $createMaterialHandler);


    $controller = new MaterialesController($bus);
    $controller->getMaterials();
});

$router->run();