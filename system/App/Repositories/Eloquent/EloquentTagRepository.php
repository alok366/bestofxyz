<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;

class EloquentTagRepository extends BaseEloquentRepository
{
    public function __construct(?Tag $model = null)
    {
        $this->model = $model ?? new Tag();
    }

    public function allNames(): array
    {
        return $this->model->orderBy('name', 'asc')->pluck('name')->toArray();
    }

    public function findOrCreateByName(string $name): Tag
    {
        $cleanName = strtolower(trim($name));
        return $this->model->firstOrCreate(['name' => $cleanName]);
    }
}
