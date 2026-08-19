<?php

namespace App\Utilities;

class SlugGenerator
{
    /**
     * Generate a globally unique slug for a model.
     * Used for subcategories (slug is globally unique).
     *
     * @param string $title Source text.
     * @param string $modelClass Fully-qualified Eloquent model class.
     * @param string $column Slug column name.
     * @return string Unique slug.
     */
    public static function unique(string $title, string $modelClass, string $column = 'slug'): string
    {
        $base = self::slugify($title);
        $slug = $base;
        $counter = 2;

        while ($modelClass::where($column, $slug)->exists()) :
            $slug = $base . '-' . $counter;
            $counter++;
        endwhile;

        return $slug;
    }

    /**
     * Generate a slug unique within a parent scope.
     * Used for resources (slug unique within a subcategory).
     *
     * @param string $title Source text.
     * @param string $modelClass Fully-qualified Eloquent model class.
     * @param string $column Slug column name.
     * @param string $scopeColumn Parent scope column.
     * @param int $scopeValue Parent scope value.
     * @return string Unique slug within scope.
     */
    public static function uniqueWithin(
        string $title,
        string $modelClass,
        string $column,
        string $scopeColumn,
        int $scopeValue
    ): string {
        $base = self::slugify($title);
        $slug = $base;
        $counter = 2;

        while ($modelClass::where($scopeColumn, $scopeValue)->where($column, $slug)->exists()) :
            $slug = $base . '-' . $counter;
            $counter++;
        endwhile;

        return $slug;
    }

    /**
     * Convert text to a URL-safe slug.
     *
     * @param string $text Source text.
     * @return string Slug.
     */
    private static function slugify(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        if (strlen($slug) > 200) :
            $slug = substr($slug, 0, 200);
            $slug = rtrim($slug, '-');
        endif;

        return $slug ?: 'untitled';
    }
}
