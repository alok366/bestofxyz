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
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get all categories with subcategories (live + pending),
     * resource counts, and top resource per subcategory.
     */
    public function allCategoriesTree(): array
    {
        $categories = $this->model
            ->orderBy('display_order', 'asc')
            ->with(['subcategories' => function ($query) {
                $query->whereIn('status', ['live', 'pending'])
                      ->withCount('resources')
                      ->orderBy('name', 'asc');
            }])
            ->get();

        // For each subcategory, load the top resource
        $subcatIds = [];
        foreach ($categories as $category) :
            foreach ($category->subcategories as $subcat) :
                $subcatIds[] = $subcat->id;
            endforeach;
        endforeach;

        $topResources = [];
        if (!empty($subcatIds)) :
            // Get top resource per subcategory
            $resources = Resource::whereIn('subcategory_id', $subcatIds)
                ->orderBy('score', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($resources as $res) :
                if (!isset($topResources[$res->subcategory_id])) :
                    $topResources[$res->subcategory_id] = $res;
                endif;
            endforeach;
        endif;

        return [
            'categories' => $categories,
            'topResources' => $topResources,
        ];
    }
}
