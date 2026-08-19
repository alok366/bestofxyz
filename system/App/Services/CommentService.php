<?php

namespace App\Services;

use App\Repositories\Smart\SmartCommentRepository;
use App\Models\Comment;
use App\Models\Resource;
use App\Exceptions\NotFoundException;
use App\Exceptions\DepthLimitException;
use App\Transformers\CommentTransformer;
use Illuminate\Database\Capsule\Manager as DB;

class CommentService extends BaseService
{
    protected SmartCommentRepository $commentRepo;

    public function __construct(?SmartCommentRepository $commentRepo = null)
    {
        $this->commentRepo = $commentRepo ?? new SmartCommentRepository();
        parent::__construct($this->commentRepo, $this->commentRepo);
    }

    /**
     * Get comment thread tree for a resource.
     *
     * @param int $resourceId
     * @param string $sort 'top' | 'new'
     * @param int|null $userId
     * @return array
     */
    public function getThread(int $resourceId, string $sort = 'top', ?int $userId = null): array
    {
        $comments = $this->commentRepo->getByResource($resourceId, $sort);

        $userVotes = [];
        if ($userId && $comments->isNotEmpty()) :
            $ids = $comments->pluck('id')->toArray();
            $userVotes = $this->commentRepo->getUserVotesForComments($userId, $ids);
        endif;

        return CommentTransformer::toTree($comments, $userVotes);
    }

    /**
     * Add a comment to a resource.
     *
     * @param int $resourceId
     * @param int $userId
     * @param string $body
     * @param int|null $parentId
     * @return Comment
     * @throws NotFoundException
     * @throws DepthLimitException
     */
    public function addComment(int $resourceId, int $userId, string $body, ?int $parentId = null): Comment
    {
        return DB::transaction(function () use ($resourceId, $userId, $body, $parentId) {
            $resource = Resource::find($resourceId);
            if (!$resource) :
                throw new NotFoundException('Resource not found.');
            endif;

            $depth = 0;
            if ($parentId) :
                $parent = Comment::find($parentId);
                if (!$parent || (int) $parent->resource_id !== (int) $resourceId) :
                    throw new NotFoundException('Parent comment not found for this resource.');
                endif;

                if ((int) $parent->depth >= 3) :
                    throw new DepthLimitException('Maximum nesting depth (3) exceeded.');
                endif;

                $depth = (int) $parent->depth + 1;
            endif;

            $comment = Comment::create([
                'resource_id' => $resourceId,
                'parent_id'   => $parentId,
                'user_id'     => $userId,
                'body'        => trim($body),
                'score'       => 0,
                'depth'       => $depth,
            ]);

            $comment->load('author');

            return $comment;
        });
    }
}
