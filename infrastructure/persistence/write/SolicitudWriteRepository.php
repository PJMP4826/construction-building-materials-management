<?php

namespace Infrastructure\Write\Repository;

use Domain\Interfaces\IWriteRepository;
use Domain\Entities\Solicitud;

class SolicitudWriteRepository implements IWriteRepository
{

    private \PDO $pdo;

    public function __construct(\PDO $db)
    {
        $this->pdo = $db;
    }


    public function save(object $entity): void
    {
        if (!$entity instanceof Solicitud) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Solicitud");
        }

        // Mapear estados de entidad a BD
        $statusMap = [
            'PENDIENTE' => 'PENDING',
            'ASIGNADA' => 'ASSIGNED',
            'ENTREGADA' => 'DELIVERED',
            'CANCELLED' => 'CANCELLED'
        ];
        
        $status = $statusMap[$entity->getEstado()] ?? $entity->getEstado();

        $sql = "INSERT INTO requests (material_id, quantity, delivery_address, required_at, status, courier_id, assigned_at, delivered_at, created_at, updated_at)
                VALUES (:material_id, :quantity, :delivery_address, :required_at, :status, :courier_id, :assigned_at, :delivered_at, :created_at, :updated_at)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $assignedAt = null;
            if ($entity->getTransportistaId() !== null && $entity->getEstado() === 'ASIGNADA') {
                $assignedAt = date('Y-m-d H:i:s');
            }

            $stmt->execute([
                ':material_id' => $entity->getMaterialId(),
                ':quantity' => $entity->getCantidad(),
                ':delivery_address' => $entity->getDireccionEntrega(),
                ':required_at' => $entity->getFechaRequerida(),
                ':status' => $status,
                ':courier_id' => $entity->getTransportistaId(),
                ':assigned_at' => $assignedAt,
                ':delivered_at' => $entity->getFechaEntrega(),
                ':created_at' => date('Y-m-d H:i:s'),
                ':updated_at' => date('Y-m-d H:i:s')
            ]);

            $id = $this->pdo->lastInsertId();
            $entity->setId((int)$id);

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al guardar solicitud: " . $e->getMessage());
        }
    }


    public function update(object $entity): void
    {
        if (!$entity instanceof Solicitud) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Solicitud");
        }

        if ($entity->getId() === null) {
            throw new \InvalidArgumentException("La solicitud debe tener un ID para actualizarse");
        }

        // Mapear estados de entidad a BD
        $statusMap = [
            'PENDIENTE' => 'PENDING',
            'ASIGNADA' => 'ASSIGNED',
            'ENTREGADA' => 'DELIVERED',
            'CANCELLED' => 'CANCELLED'
        ];
        
        $status = $statusMap[$entity->getEstado()] ?? $entity->getEstado();

        $sql = "UPDATE requests 
                SET material_id = :material_id,
                    quantity = :quantity,
                    delivery_address = :delivery_address,
                    required_at = :required_at,
                    status = :status,
                    courier_id = :courier_id,
                    assigned_at = :assigned_at,
                    delivered_at = :delivered_at,
                    updated_at = :updated_at
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            $assignedAt = null;
            if ($entity->getTransportistaId() !== null && $entity->getEstado() === 'ASIGNADA') {
                $assignedAt = date('Y-m-d H:i:s');
            }

            $stmt->execute([
                ':id' => $entity->getId(),
                ':material_id' => $entity->getMaterialId(),
                ':quantity' => $entity->getCantidad(),
                ':delivery_address' => $entity->getDireccionEntrega(),
                ':required_at' => $entity->getFechaRequerida(),
                ':status' => $status,
                ':courier_id' => $entity->getTransportistaId(),
                ':assigned_at' => $assignedAt,
                ':delivered_at' => $entity->getFechaEntrega(),
                ':updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Solicitud no encontrada con ID: " . $entity->getId());
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al actualizar solicitud: " . $e->getMessage());
        }
    }

    public function delete(mixed $id): bool
    {
        $sql = "DELETE FROM requests WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Solicitud no encontrada con ID: {$id}");
            }

            return true;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al eliminar solicitud: " . $e->getMessage());
        }
    }

    public function findById(mixed $id): ?object
    {
        $sql = "SELECT * FROM requests WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            // Mapear estados de BD a entidad
            $statusMap = [
                'PENDING' => 'PENDIENTE',
                'ASSIGNED' => 'ASIGNADA',
                'DELIVERED' => 'ENTREGADA',
                'CANCELLED' => 'CANCELLED'
            ];
            
            $estado = $statusMap[$row['status']] ?? $row['status'];

            $solicitud = new Solicitud(
                materialId: (int)$row['material_id'],
                cantidad: (int)$row['quantity'],
                direccionEntrega: $row['delivery_address'],
                fechaRequerida: $row['required_at'],
                estado: $estado
            );

            $solicitud->setId((int)$row['id']);
            
            if ($row['courier_id']) {
                $solicitud->setTransportistaId((int)$row['courier_id']);
            }
            
            if ($row['delivered_at']) {
                $solicitud->setFechaEntrega($row['delivered_at']);
            }

            return $solicitud;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar solicitud: " . $e->getMessage());
        }
    }
}

