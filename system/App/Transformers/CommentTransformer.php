<?php

namespace App\Transformers;

use App\Models\Comment;

class CommentTransformer
{
    /**
     * Assemble comments list into nested threaded tree.
     *
     * @param iterable $comments
     * @param array $userVotes Map of [comment_id => vote_type]
     * @return array
     */
    public static function toTree($comments, array $userVotes = []): array
    {
        $commentMap = [];
        $rootIds = [];

        foreach ($comments as $comment) :
            $id = (int) $comment->id;
            $commentMap[$id] = [
                'id'       => $id,
                'author'   => $comment->author->username ?? 'anonymous',
                'timeAgo'  => $comment->created_at ? $comment->created_at->diffForHumans() : '',
                'votes'    => (int) $comment->score,
                'body'     => $comment->body,
                'userVote' => $userVotes[$id] ?? null,
                'parentId' => $comment->parent_id ? (int) $comment->parent_id : null,
                'replies'  => [],
            ];
        endforeach;

        $tree = [];
        foreach ($commentMap as $id => &$node) :
            $parentId = $node['parentId'];
            unset($node['parentId']); // Clean internal property
            if ($parentId && isset($commentMap[$parentId])) :
                $commentMap[$parentId]['replies'][] = &$node;
            else :
                $tree[] = &$node;
            endif;
        endforeach;

        return $tree;
    }

    /**
     * Single comment to response.
     */
    public static function toResponse(Comment $comment, ?int $userVote = null): array
    {
        return [
            'id'       => (int) $comment->id,
            'author'   => $comment->author->username ?? 'anonymous',
            'timeAgo'  => $comment->created_at ? $comment->created_at->diffForHumans() : 'just now',
            'votes'    => (int) $comment->score,
            'body'     => $comment->body,
            'userVote' => $userVote,
            'replies'  => [],
        ];
    }
}
