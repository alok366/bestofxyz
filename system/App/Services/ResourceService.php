<?php

namespace App\Services;

use App\Repositories\Smart\SmartResourceRepository;
use App\Repositories\Smart\SmartCategoryRepository;
use App\Repositories\Smart\SmartTagRepository;
use App\Repositories\Smart\SmartVoteRepository;
use App\Models\Resource;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Vote;
use App\Models\User;
use App\Utilities\UrlNormalizer;
use App\Utilities\SlugGenerator;
use App\Exceptions\DuplicateResourceException;
use App\Exceptions\NotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

class ResourceService extends BaseService
{
    protected SmartResourceRepository $resourceRepo;
    protected SmartCategoryRepository $categoryRepo;
    protected SmartTagRepository $tagRepo;
    protected SmartVoteRepository $voteRepo;

    public function __construct(
        ?SmartResourceRepository $resourceRepo = null,
        ?SmartCategoryRepository $categoryRepo = null,
        ?SmartTagRepository $tagRepo = null,
        ?SmartVoteRepository $voteRepo = null
    ) {
        $this->resourceRepo = $resourceRepo ?? new SmartResourceRepository();
        $this->categoryRepo = $categoryRepo ?? new SmartCategoryRepository();
        $this->tagRepo = $tagRepo ?? new SmartTagRepository();
        $this->voteRepo = $voteRepo ?? new SmartVoteRepository();
        parent::__construct($this->resourceRepo, $this->resourceRepo);
    }

    public function getResourcesForCategory(int $categoryId, string $sort = 'top', ?string $tag = null)
    {
        return $this->resourceRepo->getResourcesForCategory($categoryId, $sort, $tag);
    }

    public function getResourcesForSubcategory(int $subcategoryId, string $sort = 'top', ?string $tag = null)
    {
        return $this->getResourcesForCategory($subcategoryId, $sort, $tag);
    }

    public function findBySlug(string $slug): ?Resource
    {
        return $this->resourceRepo->findBySlug($slug);
    }

    public function findByCategoryAndSlug(int $categoryId, string $slug): ?Resource
    {
        return $this->resourceRepo->findByCategoryAndSlug($categoryId, $slug);
    }

    public function findBySubcategoryAndSlug(int $subcategoryId, string $slug): ?Resource
    {
        return $this->findByCategoryAndSlug($subcategoryId, $slug);
    }

    public function computeRank(Resource $resource): int
    {
        return $this->resourceRepo->computeRank($resource);
    }

    public function getYesterdayRankMap(int $categoryId): array
    {
        return $this->resourceRepo->getYesterdayRankMap($categoryId);
    }

    /**
     * Create a resource in an atomic transaction.
     *
     * @param array $data Input payload
     * @param User $user Current authenticated user
     * @return Resource
     * @throws NotFoundException
     * @throws DuplicateResourceException
     */
    public function createResource(array $data, User $user): Resource
    {
        return DB::transaction(function () use ($data, $user) {
            // 1. Resolve or create category
            $categoryId = $data['category_id'] ?? ($data['subcategory_id'] ?? null);
            $newCategoryName = trim($data['new_category_name'] ?? ($data['new_subcategory_name'] ?? ''));
            $parentId = $data['parent_id'] ?? null;

            if ($categoryId) :
                $category = is_numeric($categoryId)
                    ? Category::find((int) $categoryId)
                    : Category::where('name', $categoryId)->orWhere('slug', $categoryId)->first();

                if (!$category) :
                    throw new NotFoundException('Category not found.');
                endif;
                if (!in_array($category->status, ['live', 'pending'], true)) :
                    throw new NotFoundException('This category is not accepting submissions.');
                endif;
            elseif ($newCategoryName) :
                $parent = null;
                if ($parentId) :
                    $parent = is_numeric($parentId)
                        ? Category::find((int) $parentId)
                        : Category::where('slug', $parentId)->orWhere('name', $parentId)->first();
                    if (!$parent) :
                        throw new NotFoundException('Parent category not found.');
                    endif;
                endif;

                $slug = SlugGenerator::unique($newCategoryName, Category::class);
                $category = Category::create([
                    'parent_id'          => $parent ? (int) $parent->id : null,
                    'name'               => $newCategoryName,
                    'slug'               => $slug,
                    'description'        => $data['category_description'] ?? ($data['subcategory_description'] ?? "Community-curated resources for {$newCategoryName}."),
                    'status'             => 'pending',
                    'proposed_by'        => (int) $user->id,
                    'resource_threshold' => 5,
                ]);
            else :
                throw new NotFoundException('Either category_id or new_category_name must be provided.');
            endif;

            // 2. Normalize URL
            $rawUrl = $data['url'] ?? '';
            $normalizedUrl = UrlNormalizer::normalize($rawUrl);

            // 3. Compute url_hash (binary SHA-256)
            $urlHash = hash('sha256', $normalizedUrl, true);

            // 4. Check for duplicate within this category
            $existing = Resource::where('category_id', $category->id)
                ->where('url_hash', $urlHash)
                ->first();

            if ($existing) :
                throw new DuplicateResourceException('This URL has already been submitted to this category.');
            endif;

            // 5. Slug and host
            $title = trim($data['title'] ?? '');
            $slug = SlugGenerator::uniqueWithin($title, Resource::class, 'slug', 'category_id', (int) $category->id);

            $host = parse_url($normalizedUrl, PHP_URL_HOST) ?? '';
            $host = preg_replace('/^www\./', '', strtolower($host));

            // 6. Create resource
            $description = trim($data['description'] ?? '');
            $resource = Resource::create([
                'category_id'  => (int) $category->id,
                'submitted_by' => (int) $user->id,
                'title'        => $title,
                'slug'         => $slug,
                'url'          => $normalizedUrl,
                'url_hash'     => $urlHash,
                'host'         => $host,
                'description'  => $description,
                'score'        => 1, // Auto-upvoted by submitter
                'hot_score'    => (float) (time() / 45000),
            ]);

            // 7. Resolve and attach tags
            $tags = $data['tags'] ?? [];
            if (is_string($tags)) :
                $tags = array_map('trim', explode(',', $tags));
            endif;

            $tagIds = [];
            foreach ($tags as $tagName) :
                $cleanTagName = strtolower(trim((string) $tagName));
                if (!empty($cleanTagName)) :
                    $tag = Tag::firstOrCreate(['name' => $cleanTagName]);
                    $tagIds[] = $tag->id;
                endif;
            endforeach;

            if (!empty($tagIds)) :
                $resource->tags()->sync($tagIds);
            endif;

            // 8. Auto-upvote by submitter
            Vote::create([
                'resource_id' => (int) $resource->id,
                'user_id'     => (int) $user->id,
                'vote_type'   => 1,
            ]);

            // 9. Check pending -> live promotion
            if ($category->isPending()) :
                $count = Resource::where('category_id', $category->id)->count();
                if ($count >= $category->resource_threshold) :
                    $category->update([
                        'status'      => 'live',
                        'promoted_at' => Carbon::now(),
                    ]);
                endif;
            endif;

            $resource->load(['category.parent', 'tags', 'submitter']);

            return $resource;
        });
    }
}

