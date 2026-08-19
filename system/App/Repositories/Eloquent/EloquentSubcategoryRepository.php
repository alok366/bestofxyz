<?php

namespace App\Repositories\Eloquent;

use App\Models\Subcategory;

class EloquentSubcategoryRepository extends BaseEloquentRepository
{
    public function __construct(?Subcategory $model = null)
    {
        $this->model = $model ?? new Subcategory();
    }

    public function findBySlug(string $slug): ?Subcategory
    {
        return $this->model->where('slug', $slug)
            ->with(['category', 'proposer'])
            ->first();
    }

    public function findPendingBySlug(string $slug): ?Subcategory
    {
        return $this->model->where('slug', $slug)
            ->where('status', 'pending')
            ->with(['category', 'proposer'])
            ->first();
    }

    public function findLiveBySlug(string $slug): ?Subcategory
    {
        return $this->model->where('slug', $slug)
            ->where('status', 'live')
            ->with(['category', 'proposer'])
            ->first();
    }
}
