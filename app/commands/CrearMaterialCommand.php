<?php

namespace App\commands;

use http\Exception\InvalidArgumentException;

class CrearMaterialCommand
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly string $unit;
    public readonly string $unitPrice;
    public readonly int $stock;
    public readonly bool $active;

    /**
     * @param string $name
     * @param string|null $description
     * @param string $unit
     * @param string $unitPrice
     * @param int $stock
     * @param bool $active
     */
    public function __construct(
        string $name,
        ?string $description,
        string $unit,
        string $unitPrice,
        int $stock = 0,
        bool $active = true
    )
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException("El nombre del material es obligatorio");
        }

        if (strlen(trim($name)) < 3) {
            throw new \InvalidArgumentException("El nombre debe tener al menos 3 caracteres");
        }

        if (strlen(trim($name)) > 255) {
            throw new \InvalidArgumentException("El nombre no puede exceder 255 caracteres");
        }


        if (empty(trim($unit))) {
            throw new \InvalidArgumentException("La unidad de medida es obligatoria");
        }

        if (strlen(trim($unit)) > 50) {
            throw new \InvalidArgumentException("La unidad no puede exceder 50 caracteres");
        }


        if (empty(trim($unitPrice))) {
            throw new \InvalidArgumentException("El precio unitario es obligatorio");
        }


        if (!is_numeric($unitPrice)) {
            throw new \InvalidArgumentException("El precio debe ser un número valido");
        }


        if ((float)$unitPrice < 0) {
            throw new \InvalidArgumentException("El precio no puede ser negativo");
        }


        if ($stock < 0) {
            throw new \InvalidArgumentException("El stock inicial no puede ser negativo");
        }


        if ($description !== null && strlen(trim($description)) > 5000) {
            throw new \InvalidArgumentException("La descripcion no puede exceder 5000 caracteres");
        }

        $this->name = $name;
        $this->description = $description;
        $this->unit = $unit;
        $this->unitPrice = $unitPrice;
        $this->stock = $stock;
        $this->active = $active;
    }
}