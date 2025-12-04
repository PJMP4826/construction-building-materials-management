<?php

namespace App\Handlers\Command;

use App\Command\CrearMaterialCommand;
use App\Interfaces\ICommandHandler;
use Brick\Math\BigDecimal;
use Domain\Entities\Material;
use Domain\Interfaces\IReadRepository;
use Domain\interfaces\IWriteRepository;
use http\Exception\InvalidArgumentException;

class CrearMaterialCommandHandler implements ICommandHandler
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
        if (!$command instanceof CrearMaterialCommand) {
            throw new InvalidArgumentException(
                "El handler espera una instancia de CrearMaterialCommand"
            );
        }

        $this->isExistName($command->name);

        try {
            $unitPrice = BigDecimal::of($command->unitPrice);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(
                "Error al procesar precio: " . $e->getMessage()
            );
        }

        try {
            $material = new Material(
                name: $command->name,
                description: $command->description,
                unit: $command->unit,
                unit_price: $unitPrice,
                stock: $command->stock,
                active: $command->active
            );
        } catch (\Exception $e) {
            throw new \DomainException(
                "Error al crear el material" . $e->getMessage()
            );
        }

        try {
            $this->writeRepository->save($material);
        } catch (\Exception $e) {
            throw new $e;
        }

        return [
            'success' => true,
            'id' => $material->getId(),
            'material' => $material->toArray(),
        ];
    }

    public function isExistName(string $name): void
    {
        $existing = $this->readRepository->findAll(
            [
                'search' => $name,
            ],
            limit: 1
        );

        if (!empty($existing)) {
            foreach ($existing as $material) {
                if (strtolower(trim($material['name'])) === strtolower(trim($name))) {
                    throw new \DomainException(
                        "Ya existe un material con el nombre: $name"
                    );
                }
            }
        }
    }
}