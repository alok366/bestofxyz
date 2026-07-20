<?php

/**
 * Run every 4 hours to refresh daily customer login stats
 */

// Bootstrap CLI environment (no sessions, or web globals)
require_once __DIR__ . '/../framework/Bootstrap/cli.php';

use App\Services\CustomerService;

$service = new CustomerService();
$service->storeDailyLoginAggregates();



