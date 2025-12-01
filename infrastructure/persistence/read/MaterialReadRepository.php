<?php

namespace Infrastructure\Read\Repository;

use Brick\Math\BigDecimal;
use Domain\Emuns\Unit;
use Domain\Entities\Material;
use Domain\Interfaces\IReadRepository;

class MaterialReadRepository implements IReadRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(mixed $id): ?object
    {
        try {
            $sql = "SELECT id, name, description, unit, unit_price, stock, active, created_at, updated_at FROM materials WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $unit = Unit::from($result['unit']);
                $unit_price = BigDecimal::of($result['unit_price']);
                $material = new Material(
                    name: $result['name'],
                    description: $result['description'],
                    unit: $unit,
                    unit_price: $unit_price,
                    stock: $result['stock'],
                    active: $result['active'],
                );

                $material->setId($result['id']);

                return $material;
            }

            return null;

        } catch (\PDOException $e) {
            throw new \Exception("Error al cargar el material" . $e->getMessage());
        }
    }

    public function findAll(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        try {
            $sql = "SELECT * FROM materials WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'name' => function ($value) {
                    return ['sql' => " AND name ILIKE :name", 'param' => '%' . $value . '%'];
                },
                'search' => function ($value) {  // Nuevo filtro
                    return ['sql' => " AND name ILIKE :search", 'param' => '%' . $value . '%'];
                },
                'active' => function ($value) {
                    return ['sql' => " AND active = :active", 'param' => (bool)$value];
                },
                'unit' => function ($value) {
                    return ['sql' => " AND unit = :unit", 'param' => $value];
                }
            ];

            foreach ($filters as $key => $value) {
                if (isset($allowedFilters[$key])) {
                    $rule = $allowedFilters[$key]($value);
                    $sql .= $rule['sql'];
                    $params[$key] = $rule['param'];
                }
            }

            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            throw new \Exception("Error fetching materials: " . $e->getMessage(), 0, $e);
        }
    }

    public function count(array $filters = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM materials WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'name' => function ($value) {
                    return ['sql' => " AND name ILIKE :name", 'param' => '%' . $value . '%'];
                },
                'search' => function ($value) {  // Agregar también aquí
                    return ['sql' => " AND name ILIKE :search", 'param' => '%' . $value . '%'];
                },
                'active' => function ($value) {
                    return ['sql' => " AND active = :active", 'param' => (bool) $value];
                },
                'unit' => function ($value) {
                    return ['sql' => " AND unit = :unit", 'param' => $value];
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
            throw new \Exception("Error counting materials: " . $e->getMessage(), 0, $e);
        }
    }
}