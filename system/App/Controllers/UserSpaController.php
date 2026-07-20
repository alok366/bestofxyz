<?php

namespace App\Controllers;
use Illuminate\Http\Response;

class UserSpaController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function renderApp(): Response
    {
        $html = file_get_contents(__DIR__ . '/../../../resources/views/User/app-shell.html');
        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }

    // ── CSRF Helpers ────────────────────────────────────────────────────

    /**
     * Get the CSRF token from the session (set by StartSessionMiddleware).
     *
     * @return string
     */
    protected function getCsrfToken(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }
}
