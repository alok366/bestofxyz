<?php

namespace App\Services;

use App\Repositories\Smart\SmartCommentVoteRepository;
use App\Models\CommentVote;
use App\Models\Comment;
use App\Exceptions\NotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use InvalidArgumentException;

class CommentVoteService extends BaseService
{
    protected SmartCommentVoteRepository $commentVoteRepo;

    public function __construct(?SmartCommentVoteRepository $commentVoteRepo = null)
    {
        $this->commentVoteRepo = $commentVoteRepo ?? new SmartCommentVoteRepository();
        parent::__construct($this->commentVoteRepo, $this->commentVoteRepo);
    }

    /**
     * Cast or update a vote on a comment.
     *
     * @param int $commentId
     * @param int $userId
     * @param int $voteType 1 or -1
     * @param string|null $ip
     * @return array
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function vote(int $commentId, int $userId, int $voteType, ?string $ip = null): array
    {
        if (!in_array($voteType, [1, -1], true)) :
            throw new InvalidArgumentException('Vote type must be 1 or -1.');
        endif;

        $ipHash = $ip ? hash('sha256', $ip, true) : null;

        return DB::transaction(function () use ($commentId, $userId, $voteType, $ipHash) {
            $comment = Comment::find($commentId);
            if (!$comment) :
                throw new NotFoundException('Comment not found.');
            endif;

            $existing = CommentVote::where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) :
                if ((int) $existing->vote_type === $voteType) :
                    return [
                        'score'    => (int) $comment->score,
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
                CommentVote::create([
                    'comment_id' => $commentId,
                    'user_id'    => $userId,
                    'vote_type'  => $voteType,
                    'ip_hash'    => $ipHash,
                ]);
                $delta = $voteType;
            endif;

            Comment::where('id', $commentId)->increment('score', $delta);
            $newScore = (int) Comment::where('id', $commentId)->value('score');

            return [
                'score'    => $newScore,
                'userVote' => $voteType,
                'changed'  => true,
            ];
        });
    }
}
