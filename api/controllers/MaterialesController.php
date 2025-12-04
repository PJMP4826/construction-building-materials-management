<?php

namespace Api\Controllers;

use App\Command\CrearMaterialCommand;
use App\Dispacher\Bus;
use App\DTO\MaterialDto;
use App\Handlers\Query\ListarMaterialesHandler;
use App\Interfaces\ICommandHandler;
use App\Interfaces\IQueryHandler;
use App\Query\ListarMaterialesQuery;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

class MaterialesController
{
    private readonly Bus $bus;

    public function __construct(
        Bus $bus
    )
    {
        $this->bus = $bus;
    }

    public function getMaterials(): Response
    {
        try {
            $request = new Request();

            //params
            $activeParam = $request->query('active');
            $searchParam = $request->query('search');
            $page = (int)$request->query('page') ?: 1;
            $limit = (int)$request->query('limit') ?: 10;

            $active = null;
            if ($activeParam !== null) {
                $active = filter_var($activeParam, FILTER_VALIDATE_BOOLEAN);
            }

            $query = new ListarMaterialesQuery(
                active: $active,
                searchTerm: $searchParam,
                page: $page,
                limit: $limit
            );

            $result = $this->bus->dispatch($query);

            $data = [];

            /**
             * @var MaterialDto[] $materials
             */
            $materials = $result['data'];

            foreach ($materials as $row) {
                $data[] = $row->toArray();
            }

            $result['data'] = $data;

            if (empty($result['data'])) {
                return Response::json([
                    'success' => false,
                    'message' => 'not found',
                    'status_code' => 404
                ], 404);
            }

            return Response::json([
                'success' => true,
                'data' => $result,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function createMaterial()
    {
        try {
            $request = new Request();

            $data = $request->input();

            $active = null;
            if ($data['active'] !== null) {
                $active = filter_var($data['active'], FILTER_VALIDATE_BOOLEAN);
            }

            $command = new CrearMaterialCommand(
                name: $data['name'],
                description: $data['description'],
                unit: $data['unit'],
                unitPrice: $data['unit_price'],
                stock: $data['stock'],
                active: $active
            );

            $result = $this->bus->dispatch($command);

            return Response::json([
                'success' => $result['success'],
                'data' => [
                    'id' => $result['id'],
                    'material' => $result['material']
                ],
                'status_code' => 200
            ]);

        } catch (\InvalidArgumentException $e) {
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 400
            ], 400);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}