<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTeamMateMiddleware
{
    public function handle($request, Closure $next, $module)
    {
        if (!isset($_SESSION['login'])) {
            return new RedirectResponse('/', 302);
        }

        $login = $_SESSION['login'];

        if (isset($login->userType) && $login->userType === 'T') :
            if (
                isset($login->teamMateModuleAccess) &&
                !empty($login->teamMateModuleAccess[$module])
            ) :
                return $next($request);
            endif;
            return new RedirectResponse('/error/403', 302);
        endif;

        return $next($request);
    }
}
