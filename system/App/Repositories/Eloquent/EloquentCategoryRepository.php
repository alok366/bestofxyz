<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Resource;

class EloquentCategoryRepository extends BaseEloquentRepository
{
    public function __construct(?Category $model = null)
    {
        $this->model = $model ?? new Category();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)
            ->with(['parent', 'proposer'])
            ->first();
    }

    public function findLiveBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)
            ->where('status', 'live')
            ->with(['parent', 'proposer'])
            ->first();
    }

    public function findPendingBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)
            ->where('status', 'pending')
            ->with(['parent', 'proposer'])
            ->first();
    }

    /**
     * Get all top-level root categories with their child categories (live + pending),
     * resource counts, and top resource per child category.
     */
    public function allCategoriesTree(): array
    {
        $rootCategories = $this->model
            ->whereNull('parent_id')
            ->orderBy('display_order', 'asc')
            ->with(['children' => function ($query) {
                $query->whereIn('status', ['live', 'pending'])
                      ->withCount('resources')
                      ->orderBy('name', 'asc');
            }])
            ->get();

        // For each child category, load the top resource
        $childIds = [];
        foreach ($rootCategories as $category) :
            foreach ($category->children as $child) :
                $childIds[] = $child->id;
            endforeach;
        endforeach;

        $topResources = [];
        if (!empty($childIds)) :
            $resources = Resource::whereIn('category_id', $childIds)
                ->orderBy('score', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($resources as $res) :
                if (!isset($topResources[$res->category_id])) :
                    $topResources[$res->category_id] = $res;
                endif;
            endforeach;
        endif;

        return [
            'categories'   => $rootCategories,
            'topResources' => $topResources,
        ];
    }
}

