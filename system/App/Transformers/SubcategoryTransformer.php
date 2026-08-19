<?php

namespace App\Transformers;

use App\Models\Subcategory;

class SubcategoryTransformer
{
    /**
     * Transform a live subcategory model with its resources into API response format.
     */
    public static function toLiveResponse(Subcategory $subcategory, $resources, string $sort, array $yesterdayRankMap): array
    {
        return [
            'name'          => $subcategory->name,
            'slug'          => $subcategory->slug,
            'group'         => $subcategory->category->name ?? '',
            'groupSlug'     => $subcategory->category->slug ?? '',
            'description'   => $subcategory->description ?? '',
            'resourceCount' => $resources->count(),
            'resources'     => ResourceTransformer::collectionToList($resources, $sort, $yesterdayRankMap),
        ];
    }

    /**
     * Transform a pending subcategory model with its resources into API response format.
     */
    public static function toPendingResponse(Subcategory $subcategory, $resources): array
    {
        $currentCount = $resources->count();
        $requiredCount = (int) $subcategory->resource_threshold;

        return [
            'name'         => $subcategory->name,
            'slug'         => $subcategory->slug,
            'group'        => $subcategory->category->name ?? '',
            'proposedBy'   => $subcategory->proposer->username ?? 'community',
            'proposedDate' => $subcategory->created_at ? $subcategory->created_at->format('M j, Y') : '',
            'description'  => $subcategory->description ?? '',
            'threshold'    => [
                'required' => $requiredCount,
                'current'  => $currentCount,
                'label'    => "{$currentCount} of {$requiredCount} resources needed to go live",
            ],
            'resources'    => ResourceTransformer::collectionToPendingList($resources),
        ];
    }
}
