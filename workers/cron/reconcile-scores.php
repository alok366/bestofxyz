<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;

// Resource scores
$drifted = DB::select("
    SELECT r.id, r.score AS current_score,
           COALESCE(SUM(v.vote_type), 0) AS actual_score
    FROM resources r
    LEFT JOIN votes v ON v.resource_id = r.id
    GROUP BY r.id, r.score
    HAVING current_score != actual_score
");

if (count($drifted) > 0) :
    $ids = implode(',', array_column($drifted, 'id'));
    DB::update("
        UPDATE resources r
        SET r.score = (SELECT COALESCE(SUM(v.vote_type), 0) FROM votes v WHERE v.resource_id = r.id)
        WHERE r.id IN ({$ids})
    ");
    echo date('Y-m-d H:i:s') . " — Reconciled " . count($drifted) . " resource scores.\n";
else :
    echo date('Y-m-d H:i:s') . " — All resource scores consistent.\n";
endif;

// Comment scores
$driftedComments = DB::select("
    SELECT c.id, c.score AS current_score,
           COALESCE(SUM(cv.vote_type), 0) AS actual_score
    FROM comments c
    LEFT JOIN comment_votes cv ON cv.comment_id = c.id
    GROUP BY c.id, c.score
    HAVING current_score != actual_score
");

if (count($driftedComments) > 0) :
    $ids = implode(',', array_column($driftedComments, 'id'));
    DB::update("
        UPDATE comments c
        SET c.score = (SELECT COALESCE(SUM(cv.vote_type), 0) FROM comment_votes cv WHERE cv.comment_id = c.id)
        WHERE c.id IN ({$ids})
    ");
    echo date('Y-m-d H:i:s') . " — Reconciled " . count($driftedComments) . " comment scores.\n";
else :
    echo date('Y-m-d H:i:s') . " — All comment scores consistent.\n";
endif;
