<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\ResourceService;
use App\Services\SubcategoryService;
use App\Services\VoteService;
use App\Services\JwtService;
use App\Transformers\ResourceTransformer;
use App\Models\User;
use App\Exceptions\DuplicateResourceException;
use App\Exceptions\NotFoundException;
use Illuminate\Http\Response;

class ResourceController extends BaseController
{
    protected ResourceService $resourceService;
    protected SubcategoryService $subcategoryService;
    protected VoteService $voteService;

    public function __construct(
        ?ResourceService $resourceService = null,
        ?SubcategoryService $subcategoryService = null,
        ?VoteService $voteService = null
    ) {
        parent::__construct();
        $this->resourceService = $resourceService ?? new ResourceService();
        $this->subcategoryService = $subcategoryService ?? new SubcategoryService();
        $this->voteService = $voteService ?? new VoteService();
    }

    /**
     * GET /api/categories/{catSlug}/resources/{resSlug}
     *
     * Returns resource detail, rank, tags, submitter, and the authenticated user's vote.
     */
    public function show(string $catSlug, string $resSlug): Response
    {
        $subcategory = $this->subcategoryService->findBySlug($catSlug);
        if (!$subcategory) :
            return $this->response->problem(404, 'Not Found', "Category '{$catSlug}' not found.");
        endif;

        $resource = $this->resourceService->findBySubcategoryAndSlug($subcategory->id, $resSlug);
        if (!$resource) :
            return $this->response->problem(404, 'Not Found', "Resource '{$resSlug}' not found in '{$catSlug}'.");
        endif;

        $rank = $this->resourceService->computeRank($resource);

        $user = $this->getAuthenticatedUser();
        $userVote = $user ? $this->voteService->getUserVote($resource->id, $user->id) : null;

        $data = ResourceTransformer::toDetail($resource, $rank, $userVote);

        return $this->response->ok($data);
    }

    /**
     * POST /api/resources
     *
     * Submit a resource (requires authentication).
     * Supports Mode 1 (existing subcategory) and Mode 2 (proposing new subcategory).
     */
    public function store(): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required to submit a resource.');
        endif;

        $data = $this->request()->all();

        // Validation
        $rules = [
            'url'                  => 'required|string|max:2048',
            'title'                => 'required|string|max:200',
            'description'          => 'nullable|string|max:500',
            'subcategory_id'       => 'nullable|integer',
            'category_id'          => 'nullable|integer',
            'new_subcategory_name' => 'nullable|string|max:150',
        ];

        $validator = $this->validator->make($data, $rules);
        if ($validator->fails()) :
            return $this->response->problem(
                422,
                'Validation Error',
                'The submission data was invalid.',
                ['errors' => $validator->errors()->toArray()]
            );
        endif;

        if (empty($data['subcategory_id']) && empty($data['new_subcategory_name'])) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Please select an existing subcategory or provide a new subcategory name.',
                ['errors' => ['subcategory_id' => ['Subcategory selection or name is required.']]]
            );
        endif;

        try {
            $resource = $this->resourceService->createResource($data, $user);

            $subSlug = $resource->subcategory->slug ?? '';
            $href = $resource->subcategory->isLive()
                ? "/categories/{$subSlug}/resources/{$resource->slug}"
                : "/pending/{$subSlug}";

            return $this->response->ok([
                'id'    => (int) $resource->id,
                'title' => $resource->title,
                'slug'  => $resource->slug,
                'href'  => $href,
            ], 201);
        } catch (DuplicateResourceException $e) {
            return $this->response->problem(409, 'Duplicate Resource', $e->getMessage());
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                env('MIX_APP_ENV') === 'production' ? 'An unexpected error occurred.' : 'Failed to create resource: ' . $e->getMessage()
            );
        }
    }

    /**
     * Resolve the authenticated user from request attributes, session, or optional Bearer token.
     */
    protected function getAuthenticatedUser(): ?User
    {
        $user = $this->request()->attributes->get('auth_user') ?? ($_SESSION['login'] ?? null);
        if ($user instanceof User) :
            return $user;
        endif;

        $authHeader = $this->request()->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) :
            $token = substr($authHeader, 7);
            try {
                $jwtService = new JwtService();
                $payload = $jwtService->validateToken($token);
                if (!empty($payload['sub'])) :
                    return User::find((int) $payload['sub']);
                endif;
            } catch (\Throwable $e) {
                // Ignore token errors for public endpoints
            }
        endif;

        return null;
    }
}
