<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;

$affected = DB::update("
    UPDATE resources r
    INNER JOIN categories c ON r.category_id = c.id
    SET r.hot_score = SIGN(r.score) * LOG10(GREATEST(ABS(r.score), 1))
                     + (UNIX_TIMESTAMP(r.created_at) / 45000)
    WHERE c.status IN ('live', 'pending')
");

echo date('Y-m-d H:i:s') . " — Updated hot_score for {$affected} resources.\n";

