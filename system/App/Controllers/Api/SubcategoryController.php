<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\SubcategoryService;
use App\Services\ResourceService;
use App\Transformers\SubcategoryTransformer;
use Illuminate\Http\Response;

class SubcategoryController extends BaseController
{
    protected SubcategoryService $subcategoryService;
    protected ResourceService $resourceService;

    public function __construct(
        ?SubcategoryService $subcategoryService = null,
        ?ResourceService $resourceService = null
    ) {
        parent::__construct();
        $this->subcategoryService = $subcategoryService ?? new SubcategoryService();
        $this->resourceService = $resourceService ?? new ResourceService();
    }

    /**
     * GET /api/categories/{slug}?sort=top|new|rising&tag=free|video|...
     *
     * Returns subcategory details and its ranked resources.
     */
    public function show(string $slug): Response
    {
        $subcategory = $this->subcategoryService->findLiveBySlug($slug);

        if (!$subcategory) :
            return $this->response->problem(404, 'Not Found', "Category '{$slug}' not found.");
        endif;

        $sort = $this->request()->query('sort', 'top');
        $tag = $this->request()->query('tag');

        $resources = $this->resourceService->getResourcesForSubcategory($subcategory->id, $sort, $tag);
        $yesterdayRankMap = $this->resourceService->getYesterdayRankMap($subcategory->id);

        $data = SubcategoryTransformer::toLiveResponse($subcategory, $resources, $sort, $yesterdayRankMap);

        return $this->response->ok($data);
    }
}
