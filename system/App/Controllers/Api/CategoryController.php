<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\CategoryService;
use App\Services\ResourceService;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Response;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;
    protected ResourceService $resourceService;

    public function __construct(
        ?CategoryService $categoryService = null,
        ?ResourceService $resourceService = null
    ) {
        parent::__construct();
        $this->categoryService = $categoryService ?? new CategoryService();
        $this->resourceService = $resourceService ?? new ResourceService();
    }

    /**
     * GET /api/categories
     *
     * Directory listing of all curated categories and their child categories.
     */
    public function index(): Response
    {
        $tree = $this->categoryService->getCategoryTree();
        $response = CategoryTransformer::toTreeResponse($tree['categories'], $tree['topResources']);

        return $this->response->ok($response);
    }

    /**
     * GET /api/categories/{slug}?sort=top|new|rising&tag=free|video|...
     *
     * Returns category details and its ranked resources.
     */
    public function show(string $slug): Response
    {
        $category = $this->categoryService->findLiveBySlug($slug);

        if (!$category) :
            return $this->response->problem(404, 'Not Found', "Category '{$slug}' not found.");
        endif;

        $sort = $this->request()->query('sort', 'top');
        $tag = $this->request()->query('tag');

        $resources = $this->resourceService->getResourcesForCategory($category->id, $sort, $tag);
        $yesterdayRankMap = $this->resourceService->getYesterdayRankMap($category->id);

        $data = CategoryTransformer::toLiveResponse($category, $resources, $sort, $yesterdayRankMap);

        return $this->response->ok($data);
    }
}

