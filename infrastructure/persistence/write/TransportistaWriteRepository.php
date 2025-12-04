<?php

namespace Infrastructure\Write\Repository;

use Domain\Interfaces\IWriteRepository;
use Domain\Entities\Transportist;

class TransportistaWriteRepository implements IWriteRepository
{

    private \PDO $pdo;

    public function __construct(\PDO $db)
    {
        $this->pdo = $db;
    }


    public function save(object $entity): void
    {
        if (!$entity instanceof Transportist) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Transportist");
        }


        $sql = "INSERT INTO couriers (name, email, delivery_zone, available, average_rating, delivery_count, created_at, updated_at)
                VALUES (:name, :email, :delivery_zone, :available, :average_rating, :delivery_count, :created_at, :updated_at)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':name' => $entity->getName(),
                ':email' => $entity->getEmail(),
                ':delivery_zone' => $entity->getDeliveryArea(),
                ':available' => $entity->isAvailable() ? 1 : 0,
                ':average_rating' => $entity->getRatingAverage(),
                ':delivery_count' => $entity->getDeliveryCounts(),
                ':created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
                ':updated_at' => $entity->getUpdateAt()->format('Y-m-d H:i:s')
            ]);

            $id = $this->pdo->lastInsertId();
            $entity->setId((int)$id);

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al guardar transportista: " . $e->getMessage());
        }
    }


    public function update(object $entity): void
    {
        if (!$entity instanceof Transportist) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Transportist");
        }

        if ($entity->getId() === null) {
            throw new \InvalidArgumentException("El transportista debe tener un ID para actualizarse");
        }


        $sql = "UPDATE couriers 
                SET name = :name,
                    email = :email,
                    delivery_zone = :delivery_zone,
                    available = :available,
                    average_rating = :average_rating,
                    delivery_count = :delivery_count,
                    updated_at = :updated_at
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $entity->getId(),
                ':name' => $entity->getName(),
                ':email' => $entity->getEmail(),
                ':delivery_zone' => $entity->getDeliveryArea(),
                ':available' => $entity->isAvailable() ? 1 : 0,
                ':average_rating' => $entity->getRatingAverage(),
                ':delivery_count' => $entity->getDeliveryCounts(),
                ':updated_at' => $entity->getUpdateAt()->format('Y-m-d H:i:s')
            ]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Transportista no encontrado con ID: " . $entity->getId());
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al actualizar transportista: " . $e->getMessage());
        }
    }

    public function delete(mixed $id): bool
    {
        $sql = "DELETE FROM couriers WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Transportista no encontrado con ID: {$id}");
            }

            return true;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al eliminar transportista: " . $e->getMessage());
        }
    }

    public function findById(mixed $id): ?object
    {
        $sql = "SELECT * FROM couriers WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $transportist = new Transportist(
                name: $row['name'],
                email: $row['email'],
                deliveryArea: $row['delivery_zone'],
                available: (bool)$row['available'],
                ratingAverage: (float)$row['average_rating'],
                deliveryCounts: (int)$row['delivery_count']
            );

            $transportist->setId((int)$row['id']);
            $transportist->setCreatedAt(new \DateTime($row['created_at']));
            $transportist->setUpdateAt(new \DateTime($row['updated_at']));

            return $transportist;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar transportista: " . $e->getMessage());
        }
    }
}

