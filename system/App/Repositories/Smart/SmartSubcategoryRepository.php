<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentSubcategoryRepository;
use App\Models\Subcategory;

class SmartSubcategoryRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentSubcategoryRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentSubcategoryRepository();
    }

    public function findBySlug(string $slug): ?Subcategory
    {
        return $this->eloquent->findBySlug($slug);
    }

    public function findPendingBySlug(string $slug): ?Subcategory
    {
        return $this->eloquent->findPendingBySlug($slug);
    }

    public function findLiveBySlug(string $slug): ?Subcategory
    {
        return $this->eloquent->findLiveBySlug($slug);
    }
}
