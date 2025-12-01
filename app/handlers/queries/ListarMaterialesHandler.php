<?php

namespace App\Handlers\Query;

use App\DTO\MaterialDto;
use App\Interfaces\IQueryHandler;
use App\Query\ListarMaterialesQuery;
use Domain\interfaces\IReadRepository;
use http\Exception\InvalidArgumentException;

class ListarMaterialesHandler implements IQueryHandler
{
    private readonly IReadRepository $repository;

    public function __construct(IReadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(object $query): array
    {
        if (!$query instanceof ListarMaterialesQuery) {
            throw new InvalidArgumentException("El handler espera una instancia de ListarMaterialesQuery");
        }

        $filters = $query->toFilters();

        $materialsData = $this->repository->findAll(
            filters: $filters,
            limit: $query->limit,
            offset: $query->getOffset()
        );

        $total = $this->repository->count($filters);

        /**
         * @var MaterialDto[] $data
         */
        $data = [];
        foreach ($materialsData as $row) {
            $data[] = MaterialDto::fromArray($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'page' => $query->page
        ];
    }
}