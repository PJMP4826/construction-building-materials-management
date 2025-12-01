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
}