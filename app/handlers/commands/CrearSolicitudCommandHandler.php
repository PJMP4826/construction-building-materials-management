<?php

namespace App\Handlers\Command;

use App\Command\CrearSolicitudCommand;
use App\Interfaces\ICommandHandler;
use Domain\Entities\Solicitud;
use Domain\Interfaces\IReadRepository;
use Domain\interfaces\IWriteRepository;
use http\Exception\InvalidArgumentException;

class CrearSolicitudCommandHandler implements ICommandHandler
{
    private readonly IReadRepository $readRepository;
    private readonly IWriteRepository $writeRepository;
    private readonly IReadRepository $materialReadRepository;

    public function __construct(
        IReadRepository  $readRepository,
        IWriteRepository $writeRepository,
        IReadRepository  $materialReadRepository
    )
    {
        $this->readRepository = $readRepository;
        $this->writeRepository = $writeRepository;
        $this->materialReadRepository = $materialReadRepository;
    }

    public function handle(object $command): array
    {
        if (!$command instanceof CrearSolicitudCommand) {
            throw new InvalidArgumentException(
                "El handler espera una instancia de CrearSolicitudCommand"
            );
        }

        //validar que el material existe
        $material = $this->materialReadRepository->findById($command->materialId);
        if ($material === null) {
            throw new \DomainException(
                "El material con ID {$command->materialId} no existe"
            );
        }

        // validar que hay stock disponible
        $this->validateStock($material, $command->quantity);

        try {
            $solicitud = new Solicitud(
                materialId: $command->materialId,
                cantidad: $command->quantity,
                direccionEntrega: $command->deliveryAddress,
                fechaRequerida: $command->requiredAt,
                estado: 'PENDIENTE'
            );
        } catch (\Exception $e) {
            throw new \DomainException(
                "Error al crear la solicitud: " . $e->getMessage()
            );
        }

        try {
            $this->writeRepository->save($solicitud);
        } catch (\Exception $e) {
            throw new $e;
        }

        return [
            'success' => true,
            'id' => $solicitud->getId(),
            'solicitud' => $solicitud,
        ];
    }

    private function validateStock(object $material, int $quantity): void
    {
        //asumiendo que el material tiene un method getStock()
        if (method_exists($material, 'getStock')) {
            $stock = $material->getStock();
            if ($stock < $quantity) {
                throw new \DomainException(
                    "Stock insuficiente. Disponible: {$stock}, Solicitado: {$quantity}"
                );
            }
        }
    }
}

