<?php

namespace Domain\interfaces;

interface IWriteRepository
{
    public function save(object $entity): void;
    public function update(object $entity): void;
    public function findById(mixed $id): ?object;
    public function delete(mixed $id): bool;
}