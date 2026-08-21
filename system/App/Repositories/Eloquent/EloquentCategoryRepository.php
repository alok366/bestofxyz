<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Resource;
use Illuminate\Database\Capsule\Manager as DB;

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
            ->where('status', '!=', 'archived')
            ->orderBy('display_order', 'asc')
            ->with(['children' => function ($query) {
                $query->whereIn('status', ['live', 'pending'])
                      ->withCount('resources')
                      ->orderBy('name', 'asc');
            }])
            ->get();

        // For each child category, load the top-ranked resource
        $childIds = [];
        foreach ($rootCategories as $category) :
            foreach ($category->children as $child) :
                $childIds[] = (int) $child->id;
            endforeach;
        endforeach;

        $topResources = [];
        if (!empty($childIds)) :
            $subQuery = Resource::select(
                'id',
                'category_id',
                'title',
                'score',
                DB::raw('ROW_NUMBER() OVER (PARTITION BY category_id ORDER BY score DESC, created_at ASC) as rn')
            )->whereIn('category_id', $childIds);

            $resources = DB::table(DB::raw("({$subQuery->toSql()}) as ranked"))
                ->mergeBindings($subQuery->getQuery())
                ->where('rn', 1)
                ->get(['id', 'category_id', 'title', 'score']);

            foreach ($resources as $res) :
                $topResources[$res->category_id] = (object) [
                    'id'    => (int) $res->id,
                    'title' => $res->title,
                    'score' => (int) $res->score,
                ];
            endforeach;
        endif;

        return [
            'categories'   => $rootCategories,
            'topResources' => $topResources,
        ];
    }
}

