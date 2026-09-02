<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load config if ConfigService exists
if (class_exists('Framework\Services\ConfigService')) {
    \Framework\Services\ConfigService::load(dirname(__DIR__) . '/config');
}
