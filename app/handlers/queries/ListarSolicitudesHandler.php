<?php

namespace App\Handlers\Query;

use App\DTO\SolicitudDto;
use App\Interfaces\IQueryHandler;
use App\Query\ListarSolicitudesQuery;
use Domain\interfaces\IReadRepository;
use http\Exception\InvalidArgumentException;

class ListarSolicitudesHandler implements IQueryHandler
{
    private readonly IReadRepository $repository;

    public function __construct(IReadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(object $query): array
    {
        if (!$query instanceof ListarSolicitudesQuery) {
            throw new InvalidArgumentException("El handler espera una instancia de ListarSolicitudesQuery");
        }

        $filters = $query->toFilters();

        $solicitudesData = $this->repository->findAll(
            filters: $filters,
            limit: $query->limit,
            offset: $query->getOffset()
        );

        $total = $this->repository->count($filters);

        /**
         * @var SolicitudDto[] $data
         */
        $data = [];
        foreach ($solicitudesData as $row) {
            $data[] = SolicitudDto::fromArray($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'page' => $query->page
        ];
    }
}

