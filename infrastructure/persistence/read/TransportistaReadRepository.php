<?php

namespace Infrastructure\Read\Repository;

use Domain\Entities\Transportist;
use Domain\Interfaces\IReadRepository;

class TransportistaReadRepository implements IReadRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(mixed $id): ?object
    {
        try {
            $sql = "SELECT id, name, email, delivery_zone, available, average_rating, delivery_count, created_at, updated_at FROM couriers WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $transportist = new Transportist(
                    name: $result['name'],
                    email: $result['email'],
                    deliveryArea: $result['delivery_zone'],
                    available: (bool)$result['available'],
                    ratingAverage: (float)$result['average_rating'],
                    deliveryCounts: (int)$result['delivery_count']
                );

                $transportist->setId((int)$result['id']);
                $transportist->setCreatedAt(new \DateTime($result['created_at']));
                $transportist->setUpdateAt(new \DateTime($result['updated_at']));

                return $transportist;
            }

            return null;

        } catch (\PDOException $e) {
            throw new \Exception("Error al cargar el transportista: " . $e->getMessage());
        }
    }

    public function findAll(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        try {
            $sql = "SELECT * FROM couriers WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'name' => function ($value) {
                    return ['sql' => " AND name ILIKE :name", 'param' => '%' . $value . '%'];
                },
                'search' => function ($value) {
                    return ['sql' => " AND name ILIKE :search", 'param' => '%' . $value . '%'];
                },
                'available' => function ($value) {
                    return ['sql' => " AND available = :available", 'param' => (bool)$value];
                },
                'delivery_zone' => function ($value) {
                    return ['sql' => " AND delivery_zone ILIKE :delivery_zone", 'param' => '%' . $value . '%'];
                },
                'email' => function ($value) {
                    return ['sql' => " AND email = :email", 'param' => $value];
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
            throw new \Exception("Error fetching transportistas: " . $e->getMessage(), 0, $e);
        }
    }

    public function count(array $filters = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM couriers WHERE 1=1";
            $params = [];

            $allowedFilters = [
                'name' => function ($value) {
                    return ['sql' => " AND name ILIKE :name", 'param' => '%' . $value . '%'];
                },
                'search' => function ($value) {
                    return ['sql' => " AND name ILIKE :search", 'param' => '%' . $value . '%'];
                },
                'available' => function ($value) {
                    return ['sql' => " AND available = :available", 'param' => (bool) $value];
                },
                'delivery_zone' => function ($value) {
                    return ['sql' => " AND delivery_zone ILIKE :delivery_zone", 'param' => '%' . $value . '%'];
                },
                'email' => function ($value) {
                    return ['sql' => " AND email = :email", 'param' => $value];
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
            throw new \Exception("Error counting transportistas: " . $e->getMessage(), 0, $e);
        }
    }
}

