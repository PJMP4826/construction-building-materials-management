<?php

namespace App\Query;

class ListarMaterialesQuery
{
    public function __construct(
        public readonly ?bool   $active = null,
        public readonly ?string $searchTerm = null,
        public readonly int     $page = 1,
        public readonly int     $limit = 10
    )
    {
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function toFilters(): array
    {
        $filters = [];
        if($this->active !== null){
            $filters['active'] = $this->active;
        }

        if($this->searchTerm !== null && trim($this->searchTerm) !== ""){
            $filters['search'] = trim($this->searchTerm);
        }

        return $filters;
    }
}