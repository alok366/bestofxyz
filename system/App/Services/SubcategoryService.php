<?php

namespace App\Services;

use App\Repositories\Smart\SmartSubcategoryRepository;
use App\Models\Subcategory;

class SubcategoryService extends BaseService
{
    protected SmartSubcategoryRepository $subcategoryRepo;

    public function __construct(?SmartSubcategoryRepository $subcategoryRepo = null)
    {
        $this->subcategoryRepo = $subcategoryRepo ?? new SmartSubcategoryRepository();
        parent::__construct($this->subcategoryRepo, $this->subcategoryRepo);
    }

    public function findBySlug(string $slug): ?Subcategory
    {
        return $this->subcategoryRepo->findBySlug($slug);
    }

    public function findPendingBySlug(string $slug): ?Subcategory
    {
        return $this->subcategoryRepo->findPendingBySlug($slug);
    }

    public function findLiveBySlug(string $slug): ?Subcategory
    {
        return $this->subcategoryRepo->findLiveBySlug($slug);
    }
}
