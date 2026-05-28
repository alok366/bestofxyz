<?php

namespace App\Services;

class AuthService
{
    /**
     * Get the authenticated user's session object.
     *
     * @return object|null Session login object or null if not authenticated.
     */
    public function user(): ?object
    {
        return $_SESSION['login'] ?? null;
    }

    public function check(): bool
    {
        return isset($_SESSION['login']);
    }

}
