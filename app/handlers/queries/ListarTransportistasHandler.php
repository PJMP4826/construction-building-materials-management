<?php

namespace App\Handlers\Query;

use App\DTO\TransportistaDto;
use App\Interfaces\IQueryHandler;
use App\Query\ListarTransportistasQuery;
use Domain\interfaces\IReadRepository;
use http\Exception\InvalidArgumentException;

class ListarTransportistasHandler implements IQueryHandler
{
    private readonly IReadRepository $repository;

    public function __construct(IReadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(object $query): array
    {
        if (!$query instanceof ListarTransportistasQuery) {
            throw new InvalidArgumentException("El handler espera una instancia de ListarTransportistasQuery");
        }

        $filters = $query->toFilters();

        $transportistasData = $this->repository->findAll(
            filters: $filters,
            limit: $query->limit,
            offset: $query->getOffset()
        );

        $total = $this->repository->count($filters);

        /**
         * @var TransportistaDto[] $data
         */
        $data = [];
        foreach ($transportistasData as $row) {
            $data[] = TransportistaDto::fromArray($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'page' => $query->page
        ];
    }
}

