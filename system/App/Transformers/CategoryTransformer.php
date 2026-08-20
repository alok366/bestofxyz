<?php

namespace App\Transformers;

use App\Models\Category;

class CategoryTransformer
{
    /**
     * Transform a category tree collection with top resources for directory view.
     */
    public static function toTreeResponse(iterable $categories, array $topResources = []): array
    {
        $result = [];

        foreach ($categories as $category) :
            $totalResources = 0;
            $subcategoriesData = [];
            $children = $category->children ?? ($category->subcategories ?? []);

            foreach ($children as $subcat) :
                $count = (int) ($subcat->resources_count ?? 0);
                $totalResources += $count;

                $top = $topResources[$subcat->id] ?? null;
                $isPending = $subcat->status === 'pending';

                $subData = [
                    'id'           => (int) $subcat->id,
                    'name'         => $subcat->name,
                    'topResource'  => $top ? $top->title : null,
                    'score'        => $top ? (int) $top->score : 0,
                    'badge'        => $isPending ? 'new' : null,
                    'status'       => $isPending ? 'pending' : 'live',
                    'progress'     => $isPending ? "{$count}/{$subcat->resource_threshold} resources" : null,
                    'href'         => $isPending ? "/pending/{$subcat->slug}" : "/categories/{$subcat->slug}",
                ];

                $subcategoriesData[] = $subData;
            endforeach;

            $result[] = [
                'id'                 => $category->slug,
                'title'              => $category->name,
                'subcategoriesCount' => count($subcategoriesData),
                'resourcesCount'     => $totalResources,
                'href'               => "/categories/{$category->slug}",
                'categories'         => $subcategoriesData,
            ];
        endforeach;

        return $result;
    }

    /**
     * Transform a live category model with its resources into API response format.
     */
    public static function toLiveResponse(Category $category, $resources, string $sort, array $yesterdayRankMap = []): array
    {
        return [
            'id'            => (int) $category->id,
            'name'          => $category->name,
            'slug'          => $category->slug,
            'group'         => $category->parent->name ?? '',
            'groupSlug'     => $category->parent->slug ?? '',
            'description'   => $category->description ?? '',
            'resourceCount' => $resources->count(),
            'resources'     => ResourceTransformer::collectionToList($resources, $sort, $yesterdayRankMap),
        ];
    }

    /**
     * Transform a pending category model with its resources into API response format.
     */
    public static function toPendingResponse(Category $category, $resources): array
    {
        $currentCount = $resources->count();
        $requiredCount = (int) $category->resource_threshold;

        return [
            'id'           => (int) $category->id,
            'name'         => $category->name,
            'slug'         => $category->slug,
            'group'        => $category->parent->name ?? '',
            'groupSlug'    => $category->parent->slug ?? '',
            'proposedBy'   => $category->proposer->username ?? 'community',
            'proposedDate' => $category->created_at ? $category->created_at->format('M j, Y') : '',
            'description'  => $category->description ?? '',
            'threshold'    => [
                'required' => $requiredCount,
                'current'  => $currentCount,
                'label'    => "{$currentCount} of {$requiredCount} resources needed to go live",
            ],
            'resources'    => ResourceTransformer::collectionToPendingList($resources),
        ];
    }

    public static function toResponse(Category $category): array
    {
        return [
            'id'            => (int) $category->id,
            'name'          => $category->name,
            'slug'          => $category->slug,
            'icon'          => $category->icon,
            'description'   => $category->description,
            'display_order' => (int) $category->display_order,
        ];
    }
}

