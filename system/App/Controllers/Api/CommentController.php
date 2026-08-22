<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\CommentService;
use App\Services\JwtService;
use App\Transformers\CommentTransformer;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\DepthLimitException;
use Illuminate\Http\Response;

class CommentController extends BaseController
{
    protected CommentService $commentService;

    public function __construct(?CommentService $commentService = null)
    {
        parent::__construct();
        $this->commentService = $commentService ?? new CommentService();
    }

    /**
     * GET /api/resources/{id}/comments?sort=top|new
     *
     * Returns full threaded comment tree for a resource.
     */
    public function index($id): Response
    {
        $sort = $this->request()->query('sort', 'top');
        $user = $this->getAuthenticatedUser();

        $tree = $this->commentService->getThread((int) $id, $sort, $user ? $user->id : null);

        return $this->response->ok($tree);
    }

    /**
     * POST /api/resources/{id}/comments
     *
     * Post a comment or reply to a resource.
     */
    public function store($id): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Authentication required to post a comment.');
        endif;

        $body = trim((string) $this->request()->input('body'));
        $parentId = $this->request()->input('parent_id') ? (int) $this->request()->input('parent_id') : null;

        if (empty($body)) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Comment body cannot be empty.',
                ['errors' => ['body' => ['Comment body is required.']]]
            );
        endif;

        if (strlen($body) > 5000) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Comment body exceeds maximum length of 5000 characters.',
                ['errors' => ['body' => ['Comment is too long.']]]
            );
        endif;

        try {
            $comment = $this->commentService->addComment((int) $id, (int) $user->id, $body, $parentId);
            $data = CommentTransformer::toResponse($comment);

            return $this->response->ok($data, 201);
        } catch (DepthLimitException $e) {
            return $this->response->problem(422, 'Depth Limit Exceeded', $e->getMessage());
        } catch (NotFoundException $e) {
            return $this->response->problem(404, 'Not Found', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                config('app.env') === 'production' ? 'An unexpected error occurred while posting comment.' : 'Failed to post comment: ' . $e->getMessage()
            );
        }
    }
}
