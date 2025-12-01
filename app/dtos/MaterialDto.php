<?php

namespace App\DTO;

use Domain\Entities\Material;

class MaterialDto
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly string  $unit,
        public readonly float   $unitPrice,
        public readonly int     $stock,
        public readonly bool    $active,
        public readonly int     $totalSolicitudes,
        public readonly int     $cantidadTotalSolicitada
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit,
            'unit_price' => $this->unitPrice,
            'stock' => $this->stock,
            'active' => $this->active,
            'total_solicitudes' => $this->totalSolicitudes,
            'cantidad_total_solicitada' => $this->cantidadTotalSolicitada
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            name: $data['nombre'],
            description: $data['descripcion'] ?? null,
            unit: $data['unidad_medida'],
            unitPrice: (float)$data['precio_unitario'],
            stock: (int)$data['stock_actual'],
            active: (bool)$data['activo'],
            totalSolicitudes: (int)($data['total_solicitudes'] ?? 0),
            cantidadTotalSolicitada: (int)($data['cantidad_total_solicitada'] ?? 0)
        );
    }

    public static function fromMaterial(Material $material, int $totalSolicitudes = 0, int $cantidadTotalSolicitada = 0): self
    {
        return new self(
            id: (int)$material->getId(),
            name: $material->getName(),
            description: $material->getDescription(),
            unit: $material->getUnit()->value,
            unitPrice: $material->getUnitPrice()->toFloat(),
            stock: $material->getStock(),
            active: $material->isActive(),
            totalSolicitudes: $totalSolicitudes,
            cantidadTotalSolicitada: $cantidadTotalSolicitada
        );
    }
}