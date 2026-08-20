<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;

$promoted = DB::update("
    UPDATE categories c
    SET c.status = 'live', c.promoted_at = NOW(), c.updated_at = NOW()
    WHERE c.status = 'pending'
    AND (SELECT COUNT(*) FROM resources r WHERE r.category_id = c.id) >= c.resource_threshold
");

if ($promoted > 0) :
    echo date('Y-m-d H:i:s') . " — Promoted {$promoted} categories.\n";
endif;

