<?php
require_once __DIR__ . '/../framework/Bootstrap/web.php';
require __DIR__ . '/../framework/Bootstrap/events.php';
require __DIR__ . '/../framework/Bootstrap/router.php';

require_once __DIR__ . '/../routes/api/index.php';
require_once __DIR__ . '/../routes/web/index.php';

try {
    $request = Illuminate\Http\Request::capture();
    $response = $router->dispatch($request);

    if ($response) {
        $response->send();
        exit;
    }

} catch (Exception $e) {
    if (str_starts_with($request->getPathInfo(), '/api/')) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        error_log($e);
        $controller = new \App\Controllers\ErrorController();
        $controller->show500($e);
    }
    exit;
}