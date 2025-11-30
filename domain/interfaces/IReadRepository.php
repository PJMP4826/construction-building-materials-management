<?php

namespace Domain\interfaces;

interface IReadRepository
{
    public function findById(mixed $id): ?object;

    public function findAll(array $filters = [], int $limit = 10, int $offset = 0): array;

    public function count(array $filters = []): int;
}