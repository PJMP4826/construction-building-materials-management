<?php

use App\Command\CrearMaterialCommand;
use App\Command\CrearTransportistaCommand;
use App\Command\CrearSolicitudCommand;
use App\Dispacher\Bus;
use App\Handlers\Command\CrearMaterialCommandHandler;
use App\Handlers\Command\CrearTransportistaCommandHandler;
use App\Handlers\Command\CrearSolicitudCommandHandler;
use App\Query\ListarMaterialesQuery;
use App\Query\ListarTransportistasQuery;
use App\Query\ListarSolicitudesQuery;
use Dotenv\Dotenv;

require __DIR__ . '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Infrastructure\Database\DatabaseConnection;
use Infrastructure\Http\Router;
use Api\Controllers\MaterialesController;
use Api\Controllers\TransportistasController;
use Api\Controllers\SolicitudesController;
use App\Handlers\Query\ListarMaterialesHandler;
use App\Handlers\Query\ListarTransportistasHandler;
use App\Handlers\Query\ListarSolicitudesHandler;
use Infrastructure\Read\Repository\MaterialReadRepository;
use Infrastructure\Read\Repository\TransportistaReadRepository;
use Infrastructure\Read\Repository\SolicitudReadRepository;
use Infrastructure\Write\Repository\MaterialWriteRepository;
use Infrastructure\Write\Repository\TransportistaWriteRepository;
use Infrastructure\Write\Repository\SolicitudWriteRepository;

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
    $controller->createMaterial();
});

$router->get('/api/get-transportistas', function () {
    $respository = new TransportistaReadRepository(DatabaseConnection::Conectar());
    $handler = new ListarTransportistasHandler($respository);

    $bus = new Bus();
    $bus->register(ListarTransportistasQuery::class, $handler);

    $controller = new TransportistasController($bus);
    $controller->getTransportistas();
});

$router->post('/api/create-transportistas', function () {
    $db = DatabaseConnection::Conectar();

    $readRepository = new TransportistaReadRepository($db);
    $writeRepository = new TransportistaWriteRepository($db);

    //Command Handlers
    $listarTransportistaHandler = new ListarTransportistasHandler($readRepository);
    $createTransportistaHandler = new CrearTransportistaCommandHandler(
        $readRepository,
        $writeRepository
    );

    $bus = new Bus();

    //query handlers
    $bus->register(ListarTransportistasQuery::class, $listarTransportistaHandler);
    $bus->register(CrearTransportistaCommand::class, $createTransportistaHandler);


    $controller = new TransportistasController($bus);
    $controller->createTransportista();
});

$router->get('/api/get-solicitudes', function () {
    $respository = new SolicitudReadRepository(DatabaseConnection::Conectar());
    $handler = new ListarSolicitudesHandler($respository);

    $bus = new Bus();
    $bus->register(ListarSolicitudesQuery::class, $handler);

    $controller = new SolicitudesController($bus);
    $controller->getSolicitudes();
});

$router->post('/api/create-solicitudes', function () {
    $db = DatabaseConnection::Conectar();

    $readRepository = new SolicitudReadRepository($db);
    $writeRepository = new SolicitudWriteRepository($db);
    $materialReadRepository = new MaterialReadRepository($db);

    //Command Handlers
    $listarSolicitudHandler = new ListarSolicitudesHandler($readRepository);
    $createSolicitudHandler = new CrearSolicitudCommandHandler(
        $readRepository,
        $writeRepository,
        $materialReadRepository
    );

    $bus = new Bus();

    //query handlers
    $bus->register(ListarSolicitudesQuery::class, $listarSolicitudHandler);
    $bus->register(CrearSolicitudCommand::class, $createSolicitudHandler);


    $controller = new SolicitudesController($bus);
    $controller->createSolicitud();
});

$router->run();