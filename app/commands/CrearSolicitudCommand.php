<?php

namespace App\Command;

class CrearSolicitudCommand
{
    public readonly int $materialId;
    public readonly int $quantity;
    public readonly string $deliveryAddress;
    public readonly string $requiredAt;

    /**
     * @param int $materialId
     * @param int $quantity
     * @param string $deliveryAddress
     * @param string $requiredAt
     */
    public function __construct(
        int $materialId,
        int $quantity,
        string $deliveryAddress,
        string $requiredAt
    )
    {
        if ($materialId <= 0) {
            throw new \InvalidArgumentException("El ID del material debe ser válido");
        }

        if ($quantity <= 0) {
            throw new \InvalidArgumentException("La cantidad debe ser mayor a 0");
        }

        if (empty(trim($deliveryAddress))) {
            throw new \InvalidArgumentException("La dirección de entrega es obligatoria");
        }

        if (strlen(trim($deliveryAddress)) > 1000) {
            throw new \InvalidArgumentException("La dirección no puede exceder 1000 caracteres");
        }

        if (empty(trim($requiredAt))) {
            throw new \InvalidArgumentException("La fecha requerida es obligatoria");
        }

        // validar que la fecha sea valida y futura
        $timestamp = strtotime($requiredAt);
        if ($timestamp === false) {
            throw new \InvalidArgumentException("La fecha requerida no es válida");
        }

        if ($timestamp <= time()) {
            throw new \InvalidArgumentException("La fecha requerida debe ser futura");
        }

        $this->materialId = $materialId;
        $this->quantity = $quantity;
        $this->deliveryAddress = trim($deliveryAddress);
        $this->requiredAt = trim($requiredAt);
    }
}

