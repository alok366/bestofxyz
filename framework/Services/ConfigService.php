<?php

namespace Framework\Services;

/**
 * Configuration service that loads PHP config files and provides dot-notation access.
 *
 * Config files return arrays. Values are accessed via dot notation: config('mail.driver').
 * Supports nested keys to any depth: config('storage.s3.bucket').
 */
class ConfigService
{
    /** @var array<string, mixed> Flattened config storage */
    private static array $items = [];

    /** @var bool Whether config has been loaded */
    private static bool $loaded = false;

    /**
     * Load all config files from the given directory.
     *
     * @param string $configPath Absolute path to the config/ directory
     * @return void
     */
    public static function load(string $configPath): void
    {
        if (static::$loaded) :
            return;
        endif;

        $files = glob($configPath . '/*.php');
        if ($files === false) :
            return;
        endif;

        foreach ($files as $file) :
            $key = basename($file, '.php');
            $values = require $file;
            if (is_array($values)) :
                static::$items[$key] = $values;
            endif;
        endforeach;

        static::$loaded = true;
    }

    /**
     * Get a config value using dot notation.
     *
     * @param string|null $key Dot-notated key (e.g. 'mail.driver'). Null returns all config.
     * @param mixed $default Fallback value if key not found
     * @return mixed The config value or default
     */
    public static function get(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) :
            return static::$items;
        endif;

        $segments = explode('.', $key);
        $value = static::$items;

        foreach ($segments as $segment) :
            if (!is_array($value) || !array_key_exists($segment, $value)) :
                return $default;
            endif;
            $value = $value[$segment];
        endforeach;

        return $value;
    }

    /**
     * Set a config value at runtime using dot notation.
     *
     * @param string $key Dot-notated key
     * @param mixed $value Value to set
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $target = &static::$items;

        foreach ($segments as $i => $segment) :
            if ($i === count($segments) - 1) :
                $target[$segment] = $value;
            else :
                if (!isset($target[$segment]) || !is_array($target[$segment])) :
                    $target[$segment] = [];
                endif;
                $target = &$target[$segment];
            endif;
        endforeach;
    }

    /**
     * Check if a config key exists.
     *
     * @param string $key Dot-notated key
     * @return bool True if the key exists (even if value is null)
     */
    public static function has(string $key): bool
    {
        $segments = explode('.', $key);
        $value = static::$items;

        foreach ($segments as $segment) :
            if (!is_array($value) || !array_key_exists($segment, $value)) :
                return false;
            endif;
            $value = $value[$segment];
        endforeach;

        return true;
    }

    /**
     * Reset the config state (useful for testing).
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$items = [];
        static::$loaded = false;
    }
}
