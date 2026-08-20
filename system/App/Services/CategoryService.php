<?php

namespace App\Services;

use App\Repositories\Smart\SmartCategoryRepository;
use App\Models\Category;

class CategoryService extends BaseService
{
    protected SmartCategoryRepository $categoryRepo;

    public function __construct(?SmartCategoryRepository $categoryRepo = null)
    {
        $this->categoryRepo = $categoryRepo ?? new SmartCategoryRepository();
        parent::__construct($this->categoryRepo, $this->categoryRepo);
    }

    public function getCategoryTree(): array
    {
        return $this->categoryRepo->allCategoriesTree();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepo->findBySlug($slug);
    }

    public function findLiveBySlug(string $slug): ?Category
    {
        return $this->categoryRepo->findLiveBySlug($slug);
    }

    public function findPendingBySlug(string $slug): ?Category
    {
        return $this->categoryRepo->findPendingBySlug($slug);
    }
}
