<?php

require_once __DIR__ . '/../framework/Bootstrap/web.php';

require __DIR__ . '/../framework/Bootstrap/events.php';
require __DIR__ . '/../framework/Bootstrap/router.php';

require_once __DIR__ . '/../routes/api/index.php';

try {
    $request = $container->make('request');
    $response = $router->dispatch($request);
    if ($response) :
        $response->send();
        exit;
    endif;
} catch (Throwable $e) {
    // Log the real error — without this we lose the trace and end up
    // chasing ghosts. BMLogger keeps a structured record; error_log
    // gives Tracy/apache a copy in the same place tools watch.
    error_log('[api.php] ' . get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());

    // RFC 9457 problem+json — keeps the API contract consistent with
    // the rest of the v4 routes (problemResponse in JwtAuthMiddleware,
    // BaseController->response->problem). 500 unless we have a more
    // specific signal; auth failures already have their own 401 paths
    // upstream and never reach this catch.
    http_response_code(500);
    header('Content-Type: application/problem+json');
    echo json_encode([
        'type'   => 'https://httpstatuses.com/500',
        'title'  => 'Server Error',
        'status' => 500,
        'detail' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.',
    ]);
    exit;
}
