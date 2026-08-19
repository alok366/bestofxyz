<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Subcategory;

class CategoryTransformer
{
    /**
     * Transform a category tree collection with top resources.
     *
     * @param iterable $categories
     * @param array $topResources
     * @return array
     */
    public static function toTreeResponse(iterable $categories, array $topResources = []): array
    {
        $result = [];

        foreach ($categories as $category) :
            $totalResources = 0;
            $subcategoriesData = [];

            foreach ($category->subcategories as $subcat) :
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
