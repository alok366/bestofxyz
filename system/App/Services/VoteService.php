<?php

namespace App\Services;

use App\Repositories\Smart\SmartVoteRepository;
use App\Models\Vote;
use App\Models\Resource;
use App\Exceptions\NotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use InvalidArgumentException;

class VoteService extends BaseService
{
    protected SmartVoteRepository $voteRepo;

    public function __construct(?SmartVoteRepository $voteRepo = null)
    {
        $this->voteRepo = $voteRepo ?? new SmartVoteRepository();
        parent::__construct($this->voteRepo, $this->voteRepo);
    }

    public function getUserVote(int $resourceId, int $userId): ?int
    {
        return $this->voteRepo->getUserVote($resourceId, $userId);
    }

    /**
     * Cast or update a vote on a resource.
     *
     * @param int $resourceId
     * @param int $userId
     * @param int $voteType 1 or -1
     * @param string|null $ip
     * @return array ['score' => int, 'userVote' => int, 'changed' => bool]
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function vote(int $resourceId, int $userId, int $voteType, ?string $ip = null): array
    {
        if (!in_array($voteType, [1, -1], true)) :
            throw new InvalidArgumentException('Vote type must be 1 or -1.');
        endif;

        $ipHash = $ip ? hash('sha256', $ip, true) : null;

        return DB::transaction(function () use ($resourceId, $userId, $voteType, $ipHash) {
            $resource = Resource::find($resourceId);
            if (!$resource) :
                throw new NotFoundException('Resource not found.');
            endif;

            $existing = Vote::where('resource_id', $resourceId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) :
                if ((int) $existing->vote_type === $voteType) :
                    return [
                        'score'    => (int) $resource->score,
                        'userVote' => $voteType,
                        'changed'  => false,
                    ];
                endif;

                $delta = $voteType - (int) $existing->vote_type;
                $existing->update([
                    'vote_type' => $voteType,
                    'ip_hash'   => $ipHash,
                ]);
            else :
                Vote::create([
                    'resource_id' => $resourceId,
                    'user_id'     => $userId,
                    'vote_type'   => $voteType,
                    'ip_hash'     => $ipHash,
                ]);
                $delta = $voteType;
            endif;

            // Atomic increment on resource score
            Resource::where('id', $resourceId)->increment('score', $delta);
            $newScore = (int) Resource::where('id', $resourceId)->value('score');

            return [
                'score'    => $newScore,
                'userVote' => $voteType,
                'changed'  => true,
            ];
        });
    }

    /**
     * Remove a user's vote on a resource.
     *
     * @param int $resourceId
     * @param int $userId
     * @return array ['score' => int, 'userVote' => null, 'changed' => bool]
     * @throws NotFoundException
     */
    public function removeVote(int $resourceId, int $userId): array
    {
        return DB::transaction(function () use ($resourceId, $userId) {
            $resource = Resource::find($resourceId);
            if (!$resource) :
                throw new NotFoundException('Resource not found.');
            endif;

            $existing = Vote::where('resource_id', $resourceId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$existing) :
                return [
                    'score'    => (int) $resource->score,
                    'userVote' => null,
                    'changed'  => false,
                ];
            endif;

            $delta = -1 * (int) $existing->vote_type;
            $existing->delete();

            Resource::where('id', $resourceId)->increment('score', $delta);
            $newScore = (int) Resource::where('id', $resourceId)->value('score');

            return [
                'score'    => $newScore,
                'userVote' => null,
                'changed'  => true,
            ];
        });
    }
}
