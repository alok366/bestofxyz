<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\CategoryService;
use App\Services\ResourceService;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Response;

class PendingController extends BaseController
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
     * GET /api/pending/{slug}
     *
     * Returns pending category details and its proposed resources.
     */
    public function show(string $slug): Response
    {
        $category = $this->categoryService->findPendingBySlug($slug);

        if (!$category) :
            return $this->response->problem(404, 'Not Found', "Pending category '{$slug}' not found.");
        endif;

        $resources = $this->resourceService->getResourcesForCategory($category->id, 'top');
        $data = CategoryTransformer::toPendingResponse($category, $resources);

        return $this->response->ok($data);
    }
}

