<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vote;
use Carbon\Carbon;

class VoteRateLimitMiddleware
{
    /**
     * Handle incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->attributes->get('auth_user') ?? ($_SESSION['login'] ?? null);

        if ($user && isset($user->id)) :
            $userId = (int) $user->id;
            $oneMinuteAgo = Carbon::now()->subMinute();
            $recentVotes = Vote::where('user_id', $userId)
                ->where('created_at', '>=', $oneMinuteAgo)
                ->count();

            if ($recentVotes >= 30) :
                return new Response(
                    json_encode([
                        'type'   => 'https://httpstatuses.com/429',
                        'title'  => 'Rate Limited',
                        'detail' => 'You have exceeded the rate limit of 30 votes per minute. Please try again shortly.',
                        'status' => 429,
                    ]),
                    429,
                    ['Content-Type' => 'application/problem+json']
                );
            endif;
        endif;

        return $next($request);
    }
}
