<?php

namespace Infrastructure\Write\Repository;

use Brick\Math\BigDecimal;
use Domain\Interfaces\IWriteRepository;
use Domain\Entities\Material;

class MaterialWriteRepository implements IWriteRepository
{

    private \PDO $pdo;

    public function __construct(\PDO $db)
    {
        $this->pdo = $db;
    }


    public function save(object $entity): void
    {
        if (!$entity instanceof Material) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Material");
        }


        $sql = "INSERT INTO materials (name, description, unit, unit_price, stock, active, created_at, updated_at)
                VALUES (:name, :description, :unit, :unit_price, :stock, :active, :created_at, :updated_at)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':name' => $entity->getName(),
                ':description' => $entity->getDescription(),
                ':unit' => $entity->getUnit()->value,
                ':unit_price' => $entity->getUnitPrice()->__toString(),
                ':stock' => $entity->getStock(),
                ':active' => $entity->isActive() ? 1 : 0,
                ':created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
                ':updated_at' => $entity->getUpdateAt()->format('Y-m-d H:i:s')
            ]);

            $id = $this->pdo->lastInsertId();
            $entity->setId((int)$id);

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al guardar material: " . $e->getMessage());
        }
    }


    public function update(object $entity): void
    {
        if (!$entity instanceof Material) {
            throw new \InvalidArgumentException("La entidad debe ser de tipo Material");
        }

        if ($entity->getId() === null) {
            throw new \InvalidArgumentException("El material debe tener un ID para actualizarse");
        }


        $sql = "UPDATE materials 
                SET name = :name,
                    description = :description,
                    unit = :unit,
                    unit_price = :unit_price,
                    stock = :stock,
                    active = :active,
                    updated_at = :updated_at
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $entity->getId(),
                ':name' => $entity->getName(),
                ':description' => $entity->getDescription(),
                ':unit' => $entity->getUnit(),
                ':unit_price' => $entity->getUnitPrice()->__toString(),
                ':stock' => $entity->getStock(),
                ':active' => $entity->isActive() ? 1 : 0,
                ':updated_at' => $entity->getUpdateAt()->format('Y-m-d H:i:s')
            ]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Material no encontrado con ID: " . $entity->getId());
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al actualizar material: " . $e->getMessage());
        }
    }

    public function delete(mixed $id): bool
    {
        $sql = "DELETE FROM materials WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("Material no encontrado con ID: {$id}");
            }

            return true;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al eliminar material: " . $e->getMessage());
        }
    }

    public function findById(mixed $id): ?object
    {
        $sql = "SELECT * FROM materials WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $material = new Material(
                name: $row['name'],
                description: $row['description'],
                unit: $row['unit'],
                unit_price: BigDecimal::of($row['unit_price']),
                stock: (int)$row['stock'],
                active: (bool)$row['active']
            );

            $material->setId((int)$row['id']);
            $material->setCreatedAt(new \DateTime($row['created_at']));
            $material->setUpdateAt(new \DateTime($row['updated_at']));

            return $material;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar material: " . $e->getMessage());
        }
    }
}