<?php

namespace App\Repositories\Eloquent;

use App\Models\CommentVote;

class EloquentCommentVoteRepository extends BaseEloquentRepository
{
    public function __construct(?CommentVote $model = null)
    {
        $this->model = $model ?? new CommentVote();
    }

    public function findByCommentAndUser(int $commentId, int $userId): ?CommentVote
    {
        return $this->model->where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getUserVote(int $commentId, int $userId): ?int
    {
        $vote = $this->findByCommentAndUser($commentId, $userId);
        return $vote ? (int) $vote->vote_type : null;
    }
}
