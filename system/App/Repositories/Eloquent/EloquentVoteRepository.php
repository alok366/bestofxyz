<?php

namespace App\Repositories\Eloquent;

use App\Models\Vote;
use Carbon\Carbon;

class EloquentVoteRepository extends BaseEloquentRepository
{
    public function __construct(?Vote $model = null)
    {
        $this->model = $model ?? new Vote();
    }

    public function findByResourceAndUser(int $resourceId, int $userId): ?Vote
    {
        return $this->model->where('resource_id', $resourceId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getUserVote(int $resourceId, int $userId): ?int
    {
        $vote = $this->findByResourceAndUser($resourceId, $userId);
        return $vote ? (int) $vote->vote_type : null;
    }

    public function getRecentVoteCount(int $userId, int $seconds = 60): int
    {
        $threshold = Carbon::now()->subSeconds($seconds);
        return $this->model->where('user_id', $userId)
            ->where('created_at', '>=', $threshold)
            ->count();
    }
}
