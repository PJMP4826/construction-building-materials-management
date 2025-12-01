<?php

namespace App\Interfaces;

interface IQueryHandler
{
    public function handle(object $query): array;
}