<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentCommentRepository;
use App\Models\Comment;

class SmartCommentRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentCommentRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentCommentRepository();
    }

    public function getByResource(int $resourceId, string $sort = 'top')
    {
        return $this->eloquent->getByResource($resourceId, $sort);
    }

    public function getUserVotesForComments(int $userId, array $commentIds): array
    {
        return $this->eloquent->getUserVotesForComments($userId, $commentIds);
    }
}
