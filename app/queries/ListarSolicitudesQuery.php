<?php

namespace App\Query;

class ListarSolicitudesQuery
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int    $materialId = null,
        public readonly ?int    $courierId = null,
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
        
        if($this->status !== null && trim($this->status) !== ""){
            $filters['status'] = trim($this->status);
        }

        if($this->materialId !== null){
            $filters['material_id'] = $this->materialId;
        }

        if($this->courierId !== null){
            $filters['courier_id'] = $this->courierId;
        }

        if($this->searchTerm !== null && trim($this->searchTerm) !== ""){
            $filters['search'] = trim($this->searchTerm);
        }

        return $filters;
    }
}

