<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentCommentVoteRepository;
use App\Models\CommentVote;

class SmartCommentVoteRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentCommentVoteRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentCommentVoteRepository();
    }

    public function findByCommentAndUser(int $commentId, int $userId): ?CommentVote
    {
        return $this->eloquent->findByCommentAndUser($commentId, $userId);
    }

    public function getUserVote(int $commentId, int $userId): ?int
    {
        return $this->eloquent->getUserVote($commentId, $userId);
    }
}
