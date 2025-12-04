<?php

namespace Api\Controllers;

use App\Command\CrearTransportistaCommand;
use App\Dispacher\Bus;
use App\DTO\TransportistaDto;
use App\Query\ListarTransportistasQuery;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Infrastructure\Http\Validator;

class TransportistasController
{
    private readonly Bus $bus;

    public function __construct(
        Bus $bus
    )
    {
        $this->bus = $bus;
    }

    public function getTransportistas(): Response
    {
        try {
            $request = new Request();

            //params
            $availableParam = $request->query('available');
            $searchParam = $request->query('search');
            $deliveryZoneParam = $request->query('delivery_zone');
            $page = (int)$request->query('page') ?: 1;
            $limit = (int)$request->query('limit') ?: 10;

            $available = null;
            if ($availableParam !== null) {
                $available = filter_var($availableParam, FILTER_VALIDATE_BOOLEAN);
            }

            $query = new ListarTransportistasQuery(
                available: $available,
                searchTerm: $searchParam,
                deliveryZone: $deliveryZoneParam,
                page: $page,
                limit: $limit
            );

            //handler
            $result = $this->bus->dispatch($query);

            $data = [];

            /**
             * @var TransportistaDto[] $transportistas
             */
            $transportistas = $result['data'];

            foreach ($transportistas as $row) {
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
                'data' => $result['data'],
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

    public function createTransportista()
    {
        try {
            $request = new Request();

            $data = $request->input();

            $rules = [
                'name' => ['required', 'string'],
                'email' => ['required', 'string'],
                'delivery_zone' => ['required', 'string'],
                'available' => ['required']
            ];

            $validator = new Validator($data, $rules);

            if ($validator->fails()) {
                return Response::json([
                    'success' => false,
                    'errors' => $validator->errores(),
                    'status_code' => 422
                ], 422);
            }

            $available = true;
            if (isset($data['available']) && $data['available'] !== null) {
                $available = filter_var($data['available'], FILTER_VALIDATE_BOOLEAN);
            }

            $command = new CrearTransportistaCommand(
                name: $data['name'],
                email: $data['email'] ?? null,
                deliveryZone: $data['delivery_zone'],
                available: $available
            );

            //handler
            $result = $this->bus->dispatch($command);

            // Convertir el objeto Transportist a DTO y luego a array
            /** @var \Domain\Entities\Transportist $transportist */
            $transportist = $result['transportist'];
            $transportistDto = TransportistaDto::fromTransportist($transportist);
            
            return Response::json([
                'success' => $result['success'],
                'data' => [
                    'id' => $result['id'],
                    'transportist' => $transportistDto->toArray()
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
            // Capturar DomainException para transportistas duplicados
            return Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 409  // 409 Conflict es más apropiado para recursos duplicados
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

