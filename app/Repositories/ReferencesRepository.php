<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Interfaces\ReferencesRepositoryInterface;

class ReferencesRepository implements ReferencesRepositoryInterface
{
    public function create(string $model, array $data)
    {
        $reference = new $model();
        $reference->name = $data['name'];
        $reference->save();
        return $reference;
    }

    public function getAll(string $model): Collection
    {
        $modelClass = 'App\Models\\' . ucfirst($model);
        $references = $modelClass::select('name')->orderBy('name')->get();
        return $references;
    }
}