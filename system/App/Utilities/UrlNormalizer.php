<?php

namespace App\Utilities;

class UrlNormalizer
{
    /**
     * Normalize a URL for consistent hashing and deduplication.
     *
     * Rules applied:
     * 1. Ensure https:// scheme if missing
     * 2. Lowercase scheme and host
     * 3. Remove www. prefix
     * 4. Remove default ports (:80 for http, :443 for https)
     * 5. Remove trailing slash from path (unless path is just "/")
     * 6. Remove fragment (#section)
     * 7. Remove tracking query params (utm_*, fbclid, gclid, ref, mc_*, yclid)
     * 8. Sort remaining query params alphabetically
     *
     * @param string $url Raw URL from user input.
     * @return string Normalized URL.
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);

        if (!preg_match('#^https?://#i', $url)) :
            $url = 'https://' . $url;
        endif;

        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) :
            return $url;
        endif;

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host']);
        $host = preg_replace('/^www\./', '', $host);

        $port = '';
        if (!empty($parts['port'])) :
            if (!(($scheme === 'http' && $parts['port'] == 80) ||
                  ($scheme === 'https' && $parts['port'] == 443))) :
                $port = ':' . $parts['port'];
            endif;
        endif;

        $path = $parts['path'] ?? '/';
        if ($path !== '/' && str_ends_with($path, '/')) :
            $path = rtrim($path, '/');
        endif;

        $query = '';
        if (!empty($parts['query'])) :
            parse_str($parts['query'], $params);
            $trackingPrefixes = ['utm_', 'ref', 'fbclid', 'gclid', 'mc_', 'yclid'];
            $filtered = array_filter($params, function ($key) use ($trackingPrefixes) {
                foreach ($trackingPrefixes as $prefix) :
                    if (str_starts_with($key, $prefix)) return false;
                endforeach;
                return true;
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($filtered)) :
                ksort($filtered);
                $query = '?' . http_build_query($filtered);
            endif;
        endif;

        return $scheme . '://' . $host . $port . $path . $query;
    }
}
