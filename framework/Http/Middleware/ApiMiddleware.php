<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ApiService;
use App\Services\ApiActionRegistryService;

class ApiMiddleware
{
    private ApiService $apiService;
    private ApiActionRegistryService $actionRegistryService;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->actionRegistryService = new ApiActionRegistryService();
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'OPTIONS') :
            return $this->createResponse(headers: [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
                'Access-Control-Max-Age' => '86400'
            ]);
        endif;

        if ($request->getMethod() !== 'POST') :
            return $this->createResponse(status: 'HTTP/1.1 404 Not Found');
        endif;

        $contentType = $request->header('Content-Type');
        if (!$contentType || !str_contains($contentType, 'multipart/form-data')) :
            return $this->createResponse(status: 'HTTP/1.1 415 Unsupported Media Type');
        endif;

        $apiKey = $request->input('api_key');
        if (empty($apiKey)) :
            return $this->createResponse(status: 'HTTP/1.1 401 Unauthorized');
        endif;

        $action = $request->input('action');
        if (empty($action) || !$this->actionRegistryService->hasAction($action)) :
            return $this->createResponse(status: 'HTTP/1.1 400 Bad Request');
        endif;

        $developer = $this->apiService->validateApiKey($apiKey);
        if (!$developer) :
            return $this->createResponse(status: 'HTTP/1.1 403 Forbidden');
        endif;

        $rateLimitCheck = $this->apiService->checkRateLimit($developer);
        if (!$rateLimitCheck['allowed']) :
            return $this->createResponse(
                data: [
                    'error' => 'Rate limit exceeded',
                    'retry_after' => $rateLimitCheck['retry_after'],
                    'limit' => $rateLimitCheck['limit'],
                    'remaining' => 0
                ],
                status: 'HTTP/1.1 429 Too Many Requests',
                headers: [
                    'Retry-After' => $rateLimitCheck['retry_after'],
                    'X-RateLimit-Limit' => $rateLimitCheck['limit'],
                    'X-RateLimit-Remaining' => '0',
                    'X-RateLimit-Reset' => $rateLimitCheck['reset_time']
                ]
            );
        endif;

        $this->apiService->recordRequest($developer['id']);
        $_POST['authenticated_developer'] = $developer;
        unset($_POST['api_key']);
        $response = $next($request);

        return $this->addRateLimitHeaders(
            $this->addCorsHeaders($response),
            $rateLimitCheck
        );
    }

    private function createResponse(array $data = [], string $status = 'HTTP/1.1 200 OK', array $headers = [])
    {
        $defaultHeaders = array_merge([
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*'
        ], $headers);

        // Extract numeric status code for Response constructor
        preg_match('/HTTP\/1\.[01]\s+(\d{3})/', $status, $matches);
        $statusCode = $matches[1] ?? 200;

        return new Response(json_encode($data), (int)$statusCode, $defaultHeaders);
    }

    private function addCorsHeaders($response)
    {
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    private function addRateLimitHeaders($response, array $rateLimitInfo)
    {
        return $response
            ->header('X-RateLimit-Limit', $rateLimitInfo['limit'])
            ->header('X-RateLimit-Remaining', $rateLimitInfo['remaining'])
            ->header('X-RateLimit-Reset', $rateLimitInfo['reset_time']);
    }
}
