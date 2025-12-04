<?php

namespace App\Command;

class CrearTransportistaCommand
{
    public readonly string $name;
    public readonly ?string $email;
    public readonly string $deliveryZone;
    public readonly bool $available;

    /**
     * @param string $name
     * @param string|null $email
     * @param string $deliveryZone
     * @param bool $available
     */
    public function __construct(
        string $name,
        ?string $email,
        string $deliveryZone,
        bool $available = true
    )
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException("El nombre del transportista es obligatorio");
        }

        if (strlen(trim($name)) < 3) {
            throw new \InvalidArgumentException("El nombre debe tener al menos 3 caracteres");
        }

        if (strlen(trim($name)) > 255) {
            throw new \InvalidArgumentException("El nombre no puede exceder 255 caracteres");
        }

        if (empty(trim($deliveryZone))) {
            throw new \InvalidArgumentException("La zona de entrega es obligatoria");
        }

        if (strlen(trim($deliveryZone)) > 100) {
            throw new \InvalidArgumentException("La zona de entrega no puede exceder 100 caracteres");
        }

        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("El email es inválido");
        }

        if ($email !== null && strlen(trim($email)) > 255) {
            throw new \InvalidArgumentException("El email no puede exceder 255 caracteres");
        }

        $this->name = trim($name);
        $this->email = $email ? trim($email) : null;
        $this->deliveryZone = trim($deliveryZone);
        $this->available = $available;
    }
}

