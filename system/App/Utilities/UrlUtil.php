<?php

namespace Utils;

class UrlUtil
{
    /**
     * Get base URL with optional relative path
     *
     * @param string|null $relativePath
     * @return string
     */
    public static function baseUrl(?string $relativePath = null): string
    {
        return $_ENV['MIX_URL'] . $relativePath;
    }

}
