<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\CategoryService;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Response;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;

    public function __construct(?CategoryService $categoryService = null)
    {
        parent::__construct();
        $this->categoryService = $categoryService ?? new CategoryService();
    }

    /**
     * GET /api/categories
     *
     * Directory listing of all curated categories and their subcategories.
     */
    public function index(): Response
    {
        $tree = $this->categoryService->getCategoryTree();
        $response = CategoryTransformer::toTreeResponse($tree['categories'], $tree['topResources']);

        return $this->response->ok($response);
    }
}
