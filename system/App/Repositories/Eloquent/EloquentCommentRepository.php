<?php

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Models\CommentVote;

class EloquentCommentRepository extends BaseEloquentRepository
{
    public function __construct(?Comment $model = null)
    {
        $this->model = $model ?? new Comment();
    }

    public function getByResource(int $resourceId, string $sort = 'top')
    {
        $query = $this->model->where('resource_id', $resourceId)
            ->with('author');

        if (strtolower($sort) === 'new') :
            $query->orderBy('created_at', 'desc');
        else :
            $query->orderBy('score', 'desc')
                  ->orderBy('created_at', 'asc');
        endif;

        return $query->get();
    }

    public function getUserVotesForComments(int $userId, array $commentIds): array
    {
        if (empty($commentIds)) :
            return [];
        endif;

        $votes = CommentVote::where('user_id', $userId)
            ->whereIn('comment_id', $commentIds)
            ->get(['comment_id', 'vote_type']);

        $map = [];
        foreach ($votes as $v) :
            $map[$v->comment_id] = (int) $v->vote_type;
        endforeach;

        return $map;
    }
}
