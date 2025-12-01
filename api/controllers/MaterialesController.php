<?php

namespace Api\Controllers;

use App\DTO\MaterialDto;
use App\Handlers\Query\ListarMaterialesHandler;
use App\Query\ListarMaterialesQuery;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

class MaterialesController
{
    private readonly ListarMaterialesHandler $handler;

    public function __construct(ListarMaterialesHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getMaterials(): Response
    {
        try {
            $request = new Request();

            //params
            $activeParam = $request->query('active');
            $searchParam = $request->query('search');
            $page = (int) $request->query('page') ?: 1;
            $limit = (int) $request->query('limit') ?: 10;

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

            $result = $this->handler->handle($query);

            $data = [];

            /**
             * @var MaterialDto[] $materials
             */
            $materials = $result['data'];

            foreach ($materials as $row) {
                $data[] = $row->toArray();
            }

            $result['data'] = $data;

            if(empty($result['data'])){
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
}