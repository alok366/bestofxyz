<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentResourceRepository;
use App\Models\Resource;

class SmartResourceRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentResourceRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentResourceRepository();
    }

    public function findBySubcategoryAndSlug(int $subcategoryId, string $slug): ?Resource
    {
        return $this->eloquent->findBySubcategoryAndSlug($subcategoryId, $slug);
    }

    public function findBySubcategoryAndUrlHash(int $subcategoryId, string $urlHash): ?Resource
    {
        return $this->eloquent->findBySubcategoryAndUrlHash($subcategoryId, $urlHash);
    }

    public function getResourcesForSubcategory(int $subcategoryId, string $sort = 'top', ?string $tag = null)
    {
        return $this->eloquent->getResourcesForSubcategory($subcategoryId, $sort, $tag);
    }

    public function computeRank(Resource $resource): int
    {
        return $this->eloquent->computeRank($resource);
    }

    public function getYesterdayRankMap(int $subcategoryId): array
    {
        return $this->eloquent->getYesterdayRankMap($subcategoryId);
    }
}
