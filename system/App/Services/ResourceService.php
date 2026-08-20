<?php

namespace App\Services;

use App\Repositories\Smart\SmartResourceRepository;
use App\Repositories\Smart\SmartSubcategoryRepository;
use App\Repositories\Smart\SmartCategoryRepository;
use App\Repositories\Smart\SmartTagRepository;
use App\Repositories\Smart\SmartVoteRepository;
use App\Models\Resource;
use App\Models\Subcategory;
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
    protected SmartSubcategoryRepository $subcategoryRepo;
    protected SmartCategoryRepository $categoryRepo;
    protected SmartTagRepository $tagRepo;
    protected SmartVoteRepository $voteRepo;

    public function __construct(
        ?SmartResourceRepository $resourceRepo = null,
        ?SmartSubcategoryRepository $subcategoryRepo = null,
        ?SmartCategoryRepository $categoryRepo = null,
        ?SmartTagRepository $tagRepo = null,
        ?SmartVoteRepository $voteRepo = null
    ) {
        $this->resourceRepo = $resourceRepo ?? new SmartResourceRepository();
        $this->subcategoryRepo = $subcategoryRepo ?? new SmartSubcategoryRepository();
        $this->categoryRepo = $categoryRepo ?? new SmartCategoryRepository();
        $this->tagRepo = $tagRepo ?? new SmartTagRepository();
        $this->voteRepo = $voteRepo ?? new SmartVoteRepository();
        parent::__construct($this->resourceRepo, $this->resourceRepo);
    }

    public function getResourcesForSubcategory(int $subcategoryId, string $sort = 'top', ?string $tag = null)
    {
        return $this->resourceRepo->getResourcesForSubcategory($subcategoryId, $sort, $tag);
    }

    public function findBySubcategoryAndSlug(int $subcategoryId, string $slug): ?Resource
    {
        return $this->resourceRepo->findBySubcategoryAndSlug($subcategoryId, $slug);
    }

    public function computeRank(Resource $resource): int
    {
        return $this->resourceRepo->computeRank($resource);
    }

    public function getYesterdayRankMap(int $subcategoryId): array
    {
        return $this->resourceRepo->getYesterdayRankMap($subcategoryId);
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
            // 1. Resolve or create subcategory
            $subcategoryId = $data['subcategory_id'] ?? null;
            $newSubcategoryName = trim($data['new_subcategory_name'] ?? '');
            $categoryId = $data['category_id'] ?? null;

            if ($subcategoryId) :
                $subcategory = is_numeric($subcategoryId)
                    ? Subcategory::find((int) $subcategoryId)
                    : Subcategory::where('name', $subcategoryId)->orWhere('slug', $subcategoryId)->first();
                if (!$subcategory) :
                    throw new NotFoundException('Subcategory not found.');
                endif;
                if (!in_array($subcategory->status, ['live', 'pending'], true)) :
                    throw new NotFoundException('This subcategory is not accepting submissions.');
                endif;
            elseif ($newSubcategoryName) :
                if (!$categoryId) :
                    throw new NotFoundException('Parent category_id is required to create a subcategory.');
                endif;
                $category = is_numeric($categoryId)
                    ? Category::find((int) $categoryId)
                    : Category::where('slug', $categoryId)->orWhere('name', $categoryId)->first();
                if (!$category) :
                    throw new NotFoundException('Parent category not found.');
                endif;
                $categoryId = $category->id;

                $slug = SlugGenerator::unique($newSubcategoryName, Subcategory::class);
                $subcategory = Subcategory::create([
                    'category_id'        => (int) $categoryId,
                    'name'               => $newSubcategoryName,
                    'slug'               => $slug,
                    'description'        => $data['subcategory_description'] ?? "Community-curated resources for {$newSubcategoryName}.",
                    'status'             => 'pending',
                    'proposed_by'        => (int) $user->id,
                    'resource_threshold' => 5,
                ]);
            else :
                throw new NotFoundException('Either subcategory_id or new_subcategory_name must be provided.');
            endif;

            // 2. Normalize URL
            $rawUrl = $data['url'] ?? '';
            $normalizedUrl = UrlNormalizer::normalize($rawUrl);

            // 3. Compute url_hash (binary SHA-256)
            $urlHash = hash('sha256', $normalizedUrl, true);

            // 4. Check for duplicate within this subcategory
            $existing = Resource::where('subcategory_id', $subcategory->id)
                ->where('url_hash', $urlHash)
                ->first();

            if ($existing) :
                throw new DuplicateResourceException('This URL has already been submitted to this subcategory.');
            endif;

            // 5. Slug and host
            $title = trim($data['title'] ?? '');
            $slug = SlugGenerator::uniqueWithin($title, Resource::class, 'slug', 'subcategory_id', (int) $subcategory->id);

            $host = parse_url($normalizedUrl, PHP_URL_HOST) ?? '';
            $host = preg_replace('/^www\./', '', strtolower($host));

            // 6. Create resource
            $description = trim($data['description'] ?? '');
            $resource = Resource::create([
                'subcategory_id' => (int) $subcategory->id,
                'submitted_by'   => (int) $user->id,
                'title'          => $title,
                'slug'           => $slug,
                'url'            => $normalizedUrl,
                'url_hash'       => $urlHash,
                'host'           => $host,
                'description'    => $description,
                'score'          => 1, // Auto-upvoted by submitter
                'hot_score'      => (float) (time() / 45000),
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
            if ($subcategory->isPending()) :
                $count = Resource::where('subcategory_id', $subcategory->id)->count();
                if ($count >= $subcategory->resource_threshold) :
                    $subcategory->update([
                        'status'      => 'live',
                        'promoted_at' => Carbon::now(),
                    ]);
                endif;
            endif;

            $resource->load(['subcategory.category', 'tags', 'submitter']);

            return $resource;
        });
    }
}
