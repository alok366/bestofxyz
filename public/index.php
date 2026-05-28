<?php

require_once __DIR__ . '/../framework/Bootstrap/web.php';

require __DIR__ . '/../framework/Bootstrap/events.php';
require __DIR__ . '/../framework/Bootstrap/router.php';

require_once __DIR__ . '/../routes/web/index.php';

try {
    $request = Illuminate\Http\Request::createFromGlobals();
    $response = $router->dispatch($request);
    if ($response) :
        $response->send();
        exit;
    endif;
} catch (Exception $e) {
    error_log($e);
    exit;
}
