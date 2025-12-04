<?php

namespace App\Interfaces;

interface ICommandHandler
{
    public function handle(object $query): array;
}