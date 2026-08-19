<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;

DB::statement("
    INSERT INTO resource_rank_snapshots
        (resource_id, subcategory_id, `rank`, score_at_snapshot, snapshot_date)
    SELECT
        r.id,
        r.subcategory_id,
        RANK() OVER (PARTITION BY r.subcategory_id ORDER BY r.score DESC, r.created_at ASC),
        r.score,
        CURDATE()
    FROM resources r
    INNER JOIN subcategories s ON r.subcategory_id = s.id
    WHERE s.status = 'live'
    ON DUPLICATE KEY UPDATE
        `rank` = VALUES(`rank`),
        score_at_snapshot = VALUES(score_at_snapshot)
");

echo date('Y-m-d H:i:s') . " — Rank snapshot created.\n";
