<?php
require __DIR__ . '/../../framework/Bootstrap/cli.php';
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

$deleted = DB::table('resource_rank_snapshots')
    ->where('snapshot_date', '<', Carbon::now()->subDays(30)->toDateString())
    ->delete();

echo date('Y-m-d H:i:s') . " — Pruned {$deleted} snapshot rows.\n";
