<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Models\Category;

class SmartCategoryRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentCategoryRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentCategoryRepository();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->eloquent->findBySlug($slug);
    }

    public function findLiveBySlug(string $slug): ?Category
    {
        return $this->eloquent->findLiveBySlug($slug);
    }

    public function findPendingBySlug(string $slug): ?Category
    {
        return $this->eloquent->findPendingBySlug($slug);
    }

    public function allCategoriesTree(): array
    {
        return $this->eloquent->allCategoriesTree();
    }
}
