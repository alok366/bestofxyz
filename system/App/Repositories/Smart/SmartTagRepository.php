<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentTagRepository;
use App\Models\Tag;

class SmartTagRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentTagRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentTagRepository();
    }

    public function allNames(): array
    {
        return $this->eloquent->allNames();
    }

    public function findOrCreateByName(string $name): Tag
    {
        return $this->eloquent->findOrCreateByName($name);
    }
}
