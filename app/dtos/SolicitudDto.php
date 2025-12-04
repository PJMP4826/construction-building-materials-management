<?php

namespace App\DTO;

use Domain\Entities\Solicitud;

class SolicitudDto
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $materialId,
        public readonly ?string  $materialName,
        public readonly int     $quantity,
        public readonly string  $deliveryAddress,
        public readonly string  $requiredAt,
        public readonly string  $status,
        public readonly ?int    $courierId,
        public readonly ?string $courierName,
        public readonly ?string $assignedAt,
        public readonly ?string $deliveredAt,
        public readonly string  $createdAt,
        public readonly string  $updatedAt
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'material_id' => $this->materialId,
            'material_name' => $this->materialName,
            'quantity' => $this->quantity,
            'delivery_address' => $this->deliveryAddress,
            'required_at' => $this->requiredAt,
            'status' => $this->status,
            'courier_id' => $this->courierId,
            'courier_name' => $this->courierName,
            'assigned_at' => $this->assignedAt,
            'delivered_at' => $this->deliveredAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            materialId: (int)$data['material_id'],
            materialName: $data['material_name'] ?? null,
            quantity: (int)$data['quantity'],
            deliveryAddress: $data['delivery_address'] ?? $data['direccionEntrega'] ?? '',
            requiredAt: $data['required_at'] ?? $data['fechaRequerida'] ?? '',
            status: $data['status'] ?? $data['estado'] ?? 'PENDING',
            courierId: isset($data['courier_id']) ? (int)$data['courier_id'] : null,
            courierName: $data['courier_name'] ?? null,
            assignedAt: $data['assigned_at'] ?? null,
            deliveredAt: $data['delivered_at'] ?? null,
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }

    public static function fromSolicitud(Solicitud $solicitud, ?string $materialName = null, ?string $courierName = null): self
    {
        // mapear estados de la entidad a la BD
        $statusMap = [
            'PENDIENTE' => 'PENDING',
            'ASIGNADA' => 'ASSIGNED',
            'ENTREGADA' => 'DELIVERED'
        ];
        
        $status = $statusMap[$solicitud->getEstado()] ?? $solicitud->getEstado();
        
        return new self(
            id: $solicitud->getId(),
            materialId: $solicitud->getMaterialId(),
            materialName: $materialName,
            quantity: $solicitud->getCantidad(),
            deliveryAddress: $solicitud->getDireccionEntrega(),
            requiredAt: $solicitud->getFechaRequerida(),
            status: $status,
            courierId: $solicitud->getTransportistaId(),
            courierName: $courierName,
            assignedAt: null, // desde de la BD
            deliveredAt: $solicitud->getFechaEntrega(),
            createdAt: '', //desde de la BD
            updatedAt: '' // desde de la BD
        );
    }
}

