<?php

// Bootstrap CLI environment (no sessions, Twig, or web globals)
require_once __DIR__ . '/../framework/Bootstrap/cli.php';

use Mailer\Services\MessageQueueTriggerService;
use Utils\BMLogger;

try {
    // Process the email queue
    $result = MessageQueueTriggerService::processQueue();
    echo json_encode($result) . PHP_EOL;
    
    exit(0); // Success exit code
    
} catch (\Exception $e) {
    BMLogger::log([
        'message' => 'Email queue processor failed',
        'error' => $e->getMessage(),
        'file' => __FILE__,
        'line' => __LINE__,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1); // Error exit code
}