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

    public function findByCategoryAndSlug(int $categoryId, string $slug): ?Resource
    {
        return $this->eloquent->findByCategoryAndSlug($categoryId, $slug);
    }

    public function findByCategoryAndUrlHash(int $categoryId, string $urlHash): ?Resource
    {
        return $this->eloquent->findByCategoryAndUrlHash($categoryId, $urlHash);
    }

    public function getResourcesForCategory(int $categoryId, string $sort = 'top', ?string $tag = null)
    {
        return $this->eloquent->getResourcesForCategory($categoryId, $sort, $tag);
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

    public function getYesterdayRankMap(int $categoryId): array
    {
        return $this->eloquent->getYesterdayRankMap($categoryId);
    }
}

