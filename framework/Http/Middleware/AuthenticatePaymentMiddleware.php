<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;

class AuthenticatePaymentMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!isset($_SESSION['login'])) {
            return new RedirectResponse('/', 302);
        }
        return $next($request);
    }
}
