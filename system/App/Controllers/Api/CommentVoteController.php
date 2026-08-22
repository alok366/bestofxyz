<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\CommentVoteService;
use App\Models\User;
use App\Exceptions\NotFoundException;
use Illuminate\Http\Response;

class CommentVoteController extends BaseController
{
    protected CommentVoteService $commentVoteService;

    public function __construct(?CommentVoteService $commentVoteService = null)
    {
        parent::__construct();
        $this->commentVoteService = $commentVoteService ?? new CommentVoteService();
    }

    /**
     * POST /api/comments/{id}/vote
     *
     * Upvote or downvote a comment.
     */
    public function store($id): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required to vote on comments.');
        endif;

        $voteType = (int) $this->request()->input('vote_type');
        if (!in_array($voteType, [1, -1], true)) :
            return $this->response->problem(
                422,
                'Validation Error',
                'vote_type must be either 1 or -1.',
                ['errors' => ['vote_type' => ['The vote type must be 1 or -1.']]]
            );
        endif;

        try {
            $ip = $this->request()->ip();
            $result = $this->commentVoteService->vote((int) $id, (int) $user->id, $voteType, $ip);

            return $this->response->ok($result);
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                config('app.env') === 'production' ? 'An unexpected error occurred while processing vote.' : 'Failed to process comment vote: ' . $e->getMessage()
            );
        }
    }

    /**
     * DELETE /api/comments/{id}/vote
     *
     * Remove the authenticated user's vote on a comment.
     */
    public function destroy($id): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required.');
        endif;

        try {
            $result = $this->commentVoteService->removeVote((int) $id, (int) $user->id);

            return $this->response->ok($result);
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                config('app.env') === 'production' ? 'An unexpected error occurred while removing vote.' : 'Failed to remove comment vote: ' . $e->getMessage()
            );
        }
    }
}
