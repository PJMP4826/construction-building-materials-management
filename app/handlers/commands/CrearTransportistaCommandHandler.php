<?php

namespace App\Handlers\Command;

use App\Command\CrearTransportistaCommand;
use App\Interfaces\ICommandHandler;
use Domain\Entities\Transportist;
use Domain\Interfaces\IReadRepository;
use Domain\interfaces\IWriteRepository;
use http\Exception\InvalidArgumentException;

class CrearTransportistaCommandHandler implements ICommandHandler
{
    private readonly IReadRepository $readRepository;
    private readonly IWriteRepository $writeRepository;

    public function __construct(
        IReadRepository  $readRepository,
        IWriteRepository $writeRepository
    )
    {
        $this->readRepository = $readRepository;
        $this->writeRepository = $writeRepository;
    }

    public function handle(object $command): array
    {
        if (!$command instanceof CrearTransportistaCommand) {
            throw new InvalidArgumentException(
                "El handler espera una instancia de CrearTransportistaCommand"
            );
        }

        $this->isExistEmail($command->email);

        try {
            $transportist = new Transportist(
                name: $command->name,
                email: $command->email,
                deliveryArea: $command->deliveryZone,
                available: $command->available
            );
        } catch (\Exception $e) {
            throw new \DomainException(
                "Error al crear el transportista: " . $e->getMessage()
            );
        }

        try {
            $this->writeRepository->save($transportist);
        } catch (\Exception $e) {
            throw new $e;
        }

        return [
            'success' => true,
            'id' => $transportist->getId(),
            'transportist' => $transportist,
        ];
    }

    public function isExistEmail(?string $email): void
    {
        if ($email === null) {
            return; // Email es opcional
        }

        $existing = $this->readRepository->findAll(
            [
                'email' => $email,
            ],
            limit: 1
        );

        if (!empty($existing)) {
            foreach ($existing as $transportist) {
                if (strtolower(trim($transportist['email'] ?? '')) === strtolower(trim($email))) {
                    throw new \DomainException(
                        "Ya existe un transportista con el email: $email"
                    );
                }
            }
        }
    }
}

