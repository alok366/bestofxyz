<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\VoteService;
use App\Models\User;
use App\Exceptions\NotFoundException;
use Illuminate\Http\Response;

class VoteController extends BaseController
{
    protected VoteService $voteService;

    public function __construct(?VoteService $voteService = null)
    {
        parent::__construct();
        $this->voteService = $voteService ?? new VoteService();
    }

    /**
     * POST /api/resources/{id}/vote
     *
     * Upvote or downvote a resource.
     */
    public function store($id): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required to vote.');
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
            $result = $this->voteService->vote((int) $id, (int) $user->id, $voteType, $ip);

            return $this->response->ok($result);
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                env('MIX_APP_ENV') === 'production' ? 'An unexpected error occurred while processing vote.' : 'Failed to process vote: ' . $e->getMessage()
            );
        }
    }

    /**
     * DELETE /api/resources/{id}/vote
     *
     * Remove the authenticated user's vote on a resource.
     */
    public function destroy($id): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required.');
        endif;

        try {
            $result = $this->voteService->removeVote((int) $id, (int) $user->id);

            return $this->response->ok($result);
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                env('MIX_APP_ENV') === 'production' ? 'An unexpected error occurred while removing vote.' : 'Failed to remove vote: ' . $e->getMessage()
            );
        }
    }
}
