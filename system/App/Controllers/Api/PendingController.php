<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\SubcategoryService;
use App\Services\ResourceService;
use App\Transformers\SubcategoryTransformer;
use Illuminate\Http\Response;

class PendingController extends BaseController
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
     * GET /api/pending/{slug}
     *
     * Returns pending subcategory details and its proposed resources.
     */
    public function show(string $slug): Response
    {
        $subcategory = $this->subcategoryService->findPendingBySlug($slug);

        if (!$subcategory) :
            return $this->response->problem(404, 'Not Found', "Pending subcategory '{$slug}' not found.");
        endif;

        $resources = $this->resourceService->getResourcesForSubcategory($subcategory->id, 'top');
        $data = SubcategoryTransformer::toPendingResponse($subcategory, $resources);

        return $this->response->ok($data);
    }
}
