<?php

namespace Api\Controllers;

use App\Command\CrearSolicitudCommand;
use App\Dispacher\Bus;
use App\DTO\SolicitudDto;
use App\Query\ListarSolicitudesQuery;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

class SolicitudesController
{
    private readonly Bus $bus;

    public function __construct(
        Bus $bus
    )
    {
        $this->bus = $bus;
    }

    public function getSolicitudes(): Response
    {
        try {
            $request = new Request();

            //params
            $statusParam = $request->query('status');
            $materialIdParam = $request->query('material_id');
            $courierIdParam = $request->query('courier_id');
            $searchParam = $request->query('search');
            $page = (int)$request->query('page') ?: 1;
            $limit = (int)$request->query('limit') ?: 10;

            $materialId = null;
            if ($materialIdParam !== null) {
                $materialId = (int)$materialIdParam;
            }

            $courierId = null;
            if ($courierIdParam !== null) {
                $courierId = (int)$courierIdParam;
            }

            $query = new ListarSolicitudesQuery(
                status: $statusParam,
                materialId: $materialId,
                courierId: $courierId,
                searchTerm: $searchParam,
                page: $page,
                limit: $limit
            );

            //handler
            $result = $this->bus->dispatch($query);

            $data = [];

            /**
             * @var SolicitudDto[] $solicitudes
             */
            $solicitudes = $result['data'];

            foreach ($solicitudes as $row) {
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

    public function createSolicitud()
    {
        try {
            $request = new Request();

            $data = $request->input();

            $command = new CrearSolicitudCommand(
                materialId: (int)$data['material_id'],
                quantity: (int)$data['quantity'],
                deliveryAddress: $data['delivery_address'],
                requiredAt: $data['required_at']
            );

            //handler
            $result = $this->bus->dispatch($command);

            // convertir el objeto Solicitud a DTO y luego a array
            /** @var \Domain\Entities\Solicitud $solicitud */
            $solicitud = $result['solicitud'];
            $solicitudDto = SolicitudDto::fromSolicitud($solicitud);
            
            return Response::json([
                'success' => $result['success'],
                'data' => [
                    'id' => $result['id'],
                    'solicitud' => $solicitudDto->toArray()
                ],
                'status_code' => 200
            ]);

        } catch (\InvalidArgumentException $e) {
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 400
            ], 400);

        } catch (\DomainException $e) {
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 409
            ], 409);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}

