<?php

/*
|--------------------------------------------------------------------------
| Illuminate Capsule (database connection)
|--------------------------------------------------------------------------
| Isolated here so it can be required independently by the test bootstrap
| without pulling in session, globals, or legacy web-app globals.
*/

global $capsule;

try {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection([
        'driver'    => config('database.driver', 'mysql'),
        'host'      => config('database.host'),
        'database'  => config('database.database'),
        'username'  => config('database.username'),
        'password'  => config('database.password'),
        'charset'   => config('database.charset', 'utf8mb4'),
        'collation' => config('database.collation', 'utf8mb4_unicode_ci'),
        'prefix'    => config('database.prefix', ''),
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    if (config('app.debug')) :
        $capsule->getConnection()->enableQueryLog();

        // Dump the query log
        // $queryLog = $capsule->getConnection()->getQueryLog();
        // dd($queryLog);
    endif;
} catch (\PDOException $e) {
    die("Database connection error:");
}
