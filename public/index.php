<?php
require_once __DIR__ . '/../framework/Bootstrap/web.php';
require __DIR__ . '/../framework/Bootstrap/events.php';
require __DIR__ . '/../framework/Bootstrap/router.php';

require_once __DIR__ . '/../routes/api/index.php';
require_once __DIR__ . '/../routes/web/index.php';

try {
    $request = $container->make('request');
    $response = $router->dispatch($request);

    if ($response) {
        $response->send();
        exit;
    }

} catch (Exception $e) {
    error_log($e);
    if (isset($request) && str_starts_with($request->getPathInfo(), '/api/')) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/problem+json');
        $detail = (env('MIX_APP_ENV') === 'production' && !config('app.debug'))
            ? 'An unexpected server error occurred.'
            : $e->getMessage();
        echo json_encode([
            'type'   => 'https://httpstatuses.com/500',
            'title'  => 'Internal Server Error',
            'detail' => $detail,
            'status' => 500,
        ]);
    } else {
        $controller = new \App\Controllers\ErrorController();
        $controller->show500($e);
    }
    exit;
}