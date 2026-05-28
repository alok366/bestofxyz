<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;

class AuthenticateAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!isset($_SESSION['login'])) {
            return new RedirectResponse('/', 302);
        }

        $login = $_SESSION['login'];

        return $next($request);
    }
}
