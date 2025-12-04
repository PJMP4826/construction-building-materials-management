<?php

namespace App\DTO;

use Domain\Entities\Transportist;

class TransportistaDto
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string  $email,
        public readonly string  $deliveryZone,
        public readonly bool    $available,
        public readonly float   $averageRating,
        public readonly int     $deliveryCount,
        public readonly string  $createdAt,
        public readonly string  $updatedAt
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'delivery_zone' => $this->deliveryZone,
            'available' => $this->available,
            'average_rating' => $this->averageRating,
            'delivery_count' => $this->deliveryCount,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            name: $data['name'] ?? '',
            email: $data['email'] ?? null,
            deliveryZone: $data['delivery_zone'] ?? $data['deliveryArea'] ?? '',
            available: (bool)($data['available'] ?? false),
            averageRating: (float)($data['average_rating'] ?? 0.00),
            deliveryCount: (int)($data['delivery_count'] ?? $data['deliveryCounts'] ?? 0),
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }

    public static function fromTransportist(Transportist $transportist): self
    {
        return new self(
            id: $transportist->getId(),
            name: $transportist->getName(),
            email: $transportist->getEmail(),
            deliveryZone: $transportist->getDeliveryArea(),
            available: $transportist->isAvailable(),
            averageRating: $transportist->getRatingAverage(),
            deliveryCount: $transportist->getDeliveryCounts(),
            createdAt: $transportist->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $transportist->getUpdateAt()->format('Y-m-d H:i:s')
        );
    }
}

