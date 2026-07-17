<?php

namespace App\Controllers;

class UserSpaController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Render the User SPA shell.
     *
     * @return string
     */
    public function shell(): string
    {

        return $this->twig->render('User/spa-shell.twig', [
            'pageTitle' => 'PerkZilla',
            'pageSlug'  => 'spa',
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
