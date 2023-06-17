<?php

namespace App\Interfaces;
use Illuminate\Support\Collection;

interface ReferencesRepositoryInterface
{
    public function create(string $model, array $data);
    public function getAll(string $model): Collection;
}