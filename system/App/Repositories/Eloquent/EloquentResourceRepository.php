<?php

namespace App\Repositories\Eloquent;

use App\Models\Resource;
use App\Models\ResourceRankSnapshot;
use Illuminate\Database\Capsule\Manager as DB;

class EloquentResourceRepository extends BaseEloquentRepository
{
    public function __construct(?Resource $model = null)
    {
        $this->model = $model ?? new Resource();
    }

    public function findBySubcategoryAndSlug(int $subcategoryId, string $slug): ?Resource
    {
        return $this->model->where('subcategory_id', $subcategoryId)
            ->where('slug', $slug)
            ->with(['tags', 'submitter', 'subcategory.category'])
            ->first();
    }

    public function findBySubcategoryAndUrlHash(int $subcategoryId, string $urlHash): ?Resource
    {
        return $this->model->where('subcategory_id', $subcategoryId)
            ->where('url_hash', $urlHash)
            ->first();
    }

    public function getResourcesForSubcategory(int $subcategoryId, string $sort = 'top', ?string $tag = null)
    {
        $query = $this->model->where('subcategory_id', $subcategoryId)
            ->with(['tags', 'submitter'])
            ->withCount('comments');

        if ($tag && strtoupper($tag) !== 'ALL') :
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', strtolower(trim($tag)));
            });
        endif;

        switch (strtolower($sort)) :
            case 'new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rising':
                $query->orderBy('hot_score', 'desc');
                break;
            case 'top':
            default:
                $query->orderBy('score', 'desc')
                      ->orderBy('created_at', 'asc');
                break;
        endswitch;

        return $query->get();
    }

    public function computeRank(Resource $resource): int
    {
        return $this->model->where('subcategory_id', $resource->subcategory_id)
            ->where(function ($q) use ($resource) {
                $q->where('score', '>', $resource->score)
                  ->orWhere(function ($q2) use ($resource) {
                      $q2->where('score', $resource->score)
                         ->where('created_at', '<', $resource->created_at);
                  });
            })
            ->count() + 1;
    }

    public function getYesterdayRankMap(int $subcategoryId): array
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $snapshots = ResourceRankSnapshot::where('subcategory_id', $subcategoryId)
            ->where('snapshot_date', $yesterday)
            ->get(['resource_id', 'rank']);

        $map = [];
        foreach ($snapshots as $snap) :
            $map[$snap->resource_id] = (int) $snap->rank;
        endforeach;

        return $map;
    }
}
