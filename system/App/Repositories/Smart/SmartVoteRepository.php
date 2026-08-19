<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentVoteRepository;
use App\Models\Vote;

class SmartVoteRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentVoteRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentVoteRepository();
    }

    public function findByResourceAndUser(int $resourceId, int $userId): ?Vote
    {
        return $this->eloquent->findByResourceAndUser($resourceId, $userId);
    }

    public function getUserVote(int $resourceId, int $userId): ?int
    {
        return $this->eloquent->getUserVote($resourceId, $userId);
    }

    public function getRecentVoteCount(int $userId, int $seconds = 60): int
    {
        return $this->eloquent->getRecentVoteCount($userId, $seconds);
    }
}
