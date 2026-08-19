<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;

$promoted = DB::update("
    UPDATE subcategories s
    SET s.status = 'live', s.promoted_at = NOW(), s.updated_at = NOW()
    WHERE s.status = 'pending'
    AND (SELECT COUNT(*) FROM resources r WHERE r.subcategory_id = s.id) >= s.resource_threshold
");

if ($promoted > 0) :
    echo date('Y-m-d H:i:s') . " — Promoted {$promoted} subcategories.\n";
endif;
