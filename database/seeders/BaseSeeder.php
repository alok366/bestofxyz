<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Base class for all test data seeders.
 *
 * Provides Faker instance, DB helpers, and a consistent seed/clean interface.
 * Each concrete seeder defines its own tables, defaults, and relationships.
 *
 *
 * Usage from CLI:
 *   php tests/seed.php --clean
 */
abstract class BaseSeeder
{
    protected static ?Generator $faker = null;

    /**
     * Get the shared Faker instance.
     *
     * @return Generator
     */
    protected static function faker(): Generator
    {
        if (static::$faker === null) {
            static::$faker = FakerFactory::create();
        }
        return static::$faker;
    }

    /**
     * Insert a row and return its auto-increment ID.
     *
     * @param string $table Table name.
     * @param array $data Column => value pairs.
     * @return int The inserted row ID.
     */
    protected static function insertRow(string $table, array $data): int
    {
        DB::table($table)->insert($data);
        return (int) DB::connection()->getPdo()->lastInsertId();
    }

    /**
     * Merge user overrides onto defaults, filtering out keys not in defaults.
     *
     * @param array $defaults Default column values.
     * @param array $overrides User-provided overrides.
     * @return array Merged data array.
     */
    protected static function merge(array $defaults, array $overrides): array
    {
        return array_merge($defaults, $overrides);
    }

    /**
     * Generate a unique public ID string.
     *
     * @param string $prefix Short prefix for readability.
     * @return string
     */
    protected static function publicId(string $prefix = 'pub'): string
    {
        return $prefix . '-' . static::faker()->unique()->bothify('??###');
    }

    /**
     * Generate a unique EID string.
     *
     * @param string $prefix Short prefix for readability.
     * @return string
     */
    protected static function eid(string $prefix = 'eid'): string
    {
        return $prefix . '-' . static::faker()->unique()->bothify('??###');
    }

    /**
     * Return the list of tables this seeder writes to (for cleanup).
     *
     * @return array<string>
     */
    abstract public static function tables(): array;
}
