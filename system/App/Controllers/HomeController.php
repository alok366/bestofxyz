<?php

namespace App\Controllers;

class HomeController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }


    public function showHomeTimeline()
    {
        return $this->twig->render('User/spa-shell.twig', [
            'csrf_token' => $this->getCsrfToken()
        ]);
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
