<?php

namespace App\Transformers;

use App\Models\Resource;

class ResourceTransformer
{
    /**
     * Transform a collection of resources for subcategory listing.
     */
    public static function collectionToList($resources, string $sort, array $yesterdayRankMap = []): array
    {
        $list = [];
        $rank = 1;

        foreach ($resources as $resource) :
            $delta = self::computeDelta($rank, $resource->id, $sort, $yesterdayRankMap);
            $list[] = self::toListItem($resource, $rank, $delta);
            $rank++;
        endforeach;

        return $list;
    }

    /**
     * Transform a collection of resources for pending subcategory listing.
     */
    public static function collectionToPendingList($resources): array
    {
        $list = [];
        $rank = 1;

        foreach ($resources as $resource) :
            $list[] = self::toPendingListItem($resource, $rank);
            $rank++;
        endforeach;

        return $list;
    }

    /**
     * Transform single resource for subcategory listing.
     */
    public static function toListItem(Resource $resource, int $rank, array $delta): array
    {
        $catSlug = $resource->category->slug ?? '';

        return [
            'id'            => (int) $resource->id,
            'rank'          => $rank,
            'delta'         => $delta,
            'votes'         => (int) $resource->score,
            'title'         => $resource->title,
            'host'          => $resource->host,
            'description'   => $resource->description,
            'tags'          => $resource->tags ? $resource->tags->pluck('name')->toArray() : [],
            'submitter'     => $resource->submitter->username ?? 'community',
            'submitterTime' => $resource->created_at ? $resource->created_at->diffForHumans() : '',
            'commentsCount' => (int) ($resource->comments_count ?? 0),
            'href'          => "/resource/{$resource->slug}",
        ];
    }

    /**
     * Transform single resource for pending subcategory listing.
     */
    public static function toPendingListItem(Resource $resource, int $rank): array
    {
        return [
            'id'            => (int) $resource->id,
            'rank'          => $rank,
            'delta'         => ['type' => 'flat', 'label' => '—'],
            'votes'         => (int) $resource->score,
            'title'         => $resource->title,
            'host'          => $resource->host,
            'description'   => $resource->description,
            'tags'          => $resource->tags ? $resource->tags->pluck('name')->toArray() : [],
            'submitter'     => $resource->submitter->username ?? 'community',
            'submitterTime' => $resource->created_at ? $resource->created_at->format('M j, Y') : '',
            'commentsCount' => (int) ($resource->comments_count ?? 0),
            'href'          => '#',
        ];
    }

    /**
     * Transform single resource for detail page.
     */
    public static function toDetail(Resource $resource, int $rank, ?int $userVote = null): array
    {
        return [
            'id'            => (int) $resource->id,
            'rank'          => $rank,
            'categoryName'  => $resource->category->name ?? '',
            'categorySlug'  => $resource->category->slug ?? '',
            'groupName'     => $resource->category->parent->name ?? '',
            'title'         => $resource->title,
            'host'          => $resource->host,
            'href'          => $resource->url,
            'description'   => $resource->description,
            'votes'         => (int) $resource->score,
            'tags'          => $resource->tags ? $resource->tags->pluck('name')->toArray() : [],
            'submitter'     => $resource->submitter->username ?? 'community',
            'submitterTime' => $resource->created_at ? $resource->created_at->format('M j, Y') : '',
            'userVote'      => $userVote,
        ];
    }

    /**
     * Compute rank delta comparison with yesterday's snapshot.
     */
    private static function computeDelta(int $currentRank, int $resourceId, string $sort, array $yesterdayRankMap): array
    {
        if (strtolower($sort) !== 'top' || !isset($yesterdayRankMap[$resourceId])) :
            return ['type' => 'flat', 'label' => '—'];
        endif;

        $previousRank = $yesterdayRankMap[$resourceId];

        if ($currentRank < $previousRank) :
            $diff = $previousRank - $currentRank;
            return ['type' => 'up', 'label' => "▲ {$diff}"];
        elseif ($currentRank > $previousRank) :
            $diff = $currentRank - $previousRank;
            return ['type' => 'down', 'label' => "▼ {$diff}"];
        else :
            return ['type' => 'flat', 'label' => '—'];
        endif;
    }
}
