<?php

namespace App\Query;

class ListarTransportistasQuery
{
    public function __construct(
        public readonly ?bool   $available = null,
        public readonly ?string $searchTerm = null,
        public readonly ?string $deliveryZone = null,
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
        
        if($this->available !== null){
            $filters['available'] = $this->available;
        }

        if($this->searchTerm !== null && trim($this->searchTerm) !== ""){
            $filters['search'] = trim($this->searchTerm);
        }

        if($this->deliveryZone !== null && trim($this->deliveryZone) !== ""){
            $filters['delivery_zone'] = trim($this->deliveryZone);
        }

        return $filters;
    }
}

