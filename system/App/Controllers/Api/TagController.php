<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\TagService;
use Illuminate\Http\Response;

class TagController extends BaseController
{
    protected TagService $tagService;

    public function __construct(?TagService $tagService = null)
    {
        parent::__construct();
        $this->tagService = $tagService ?? new TagService();
    }

    /**
     * GET /api/tags
     *
     * Returns list of all existing tag names.
     */
    public function index(): Response
    {
        $tags = $this->tagService->getAllTagNames();
        return $this->response->ok($tags);
    }
}
