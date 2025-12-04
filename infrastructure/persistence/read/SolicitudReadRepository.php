<?php

namespace Infrastructure\Read\Repository;

use Domain\Entities\Solicitud;
use Domain\Interfaces\IReadRepository;

class SolicitudReadRepository implements IReadRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(mixed $id): ?object
    {
        try {
            $sql = "SELECT r.id, r.material_id, r.quantity, r.delivery_address, 
                           r.required_at, r.status, r.courier_id, r.assigned_at, 
                           r.delivered_at, r.created_at, r.updated_at,
                           m.name as material_name,
                           c.name as courier_name
                    FROM requests r
                    LEFT JOIN materials m ON r.material_id = m.id
                    LEFT JOIN couriers c ON r.courier_id = c.id
                    WHERE r.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                // Mapear estados de BD a entidad
                $statusMap = [
                    'PENDING' => 'PENDIENTE',
                    'ASSIGNED' => 'ASIGNADA',
                    'DELIVERED' => 'ENTREGADA',
                    'CANCELLED' => 'CANCELLED'
                ];
                
                $estado = $statusMap[$result['status']] ?? $result['status'];
                
                $solicitud = new Solicitud(
                    materialId: (int)$result['material_id'],
                    cantidad: (int)$result['quantity'],
                    direccionEntrega: $result['delivery_address'],
                    fechaRequerida: $result['required_at'],
                    estado: $estado
                );

                $solicitud->setId((int)$result['id']);
                
                if ($result['courier_id']) {
                    $solicitud->setTransportistaId((int)$result['courier_id']);
                }
                
                if ($result['delivered_at']) {
                    $solicitud->setFechaEntrega($result['delivered_at']);
                }

                return $solicitud;
            }

            return null;

        } catch (\PDOException $e) {
            throw new \Exception("Error al cargar la solicitud: " . $e->getMessage());
        }
    }

    public function findAll(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        try {
            $sql = "SELECT r.id, r.material_id, r.quantity, r.delivery_address, 
                           r.required_at, r.status, r.courier_id, r.assigned_at, 
                           r.delivered_at, r.created_at, r.updated_at,
                           m.name as material_name,
                           c.name as courier_name
                    FROM requests r
                    LEFT JOIN materials m ON r.material_id = m.id
                    LEFT JOIN couriers c ON r.courier_id = c.id
                    WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'status' => function ($value) {
                    return ['sql' => " AND r.status = :status", 'param' => strtoupper($value)];
                },
                'material_id' => function ($value) {
                    return ['sql' => " AND r.material_id = :material_id", 'param' => (int)$value];
                },
                'courier_id' => function ($value) {
                    return ['sql' => " AND r.courier_id = :courier_id", 'param' => (int)$value];
                },
                'search' => function ($value) {
                    return ['sql' => " AND (r.delivery_address ILIKE :search OR m.name ILIKE :search)", 'param' => '%' . $value . '%'];
                }
            ];

            foreach ($filters as $key => $value) {
                if (isset($allowedFilters[$key])) {
                    $rule = $allowedFilters[$key]($value);
                    $sql .= $rule['sql'];
                    $params[$key] = $rule['param'];
                }
            }

            $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            throw new \Exception("Error fetching solicitudes: " . $e->getMessage(), 0, $e);
        }
    }

    public function count(array $filters = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM requests r WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'status' => function ($value) {
                    return ['sql' => " AND r.status = :status", 'param' => strtoupper($value)];
                },
                'material_id' => function ($value) {
                    return ['sql' => " AND r.material_id = :material_id", 'param' => (int)$value];
                },
                'courier_id' => function ($value) {
                    return ['sql' => " AND r.courier_id = :courier_id", 'param' => (int)$value];
                },
                'search' => function ($value) {
                    return ['sql' => " AND (r.delivery_address ILIKE :search)", 'param' => '%' . $value . '%'];
                }
            ];

            foreach ($filters as $key => $value) {
                if (isset($allowedFilters[$key])) {
                    $rule = $allowedFilters[$key]($value);
                    $sql .= $rule['sql'];
                    $params[$key] = $rule['param'];
                }
            }

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();

            return (int) $stmt->fetchColumn();

        } catch (\PDOException $e) {
            throw new \Exception("Error counting solicitudes: " . $e->getMessage(), 0, $e);
        }
    }
}

