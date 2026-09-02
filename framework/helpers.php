<?php

use Utils\UrlUtil;
use Framework\Services\ConfigService;

if (!function_exists('env')) :
    /**
     * Get an environment variable value with an optional default.
     *
     * Handles special string values: 'true'/'false' → bool, 'null' → null, '(empty)' → ''.
     *
     * @param string $key Environment variable name
     * @param mixed $default Fallback value if the variable is not set
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null) :
            return $default;
        endif;

        return match (strtolower($value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            '(empty)', "''"    => '',
            default            => $value,
        };
    }
endif;

if (!function_exists('config')) :
    /**
     * Get a configuration value using dot notation.
     *
     * @param string|null $key Dot-notated config key (e.g. 'mail.driver'). Null returns all config.
     * @param mixed $default Fallback value if key not found
     * @return mixed
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        return ConfigService::get($key, $default);
    }
endif;

function mix($path, $manifestDirectory = '/dist')
{
    static $manifest;
    static $viteManifest;
    static $hotUrl = false;

    if (!str_starts_with($path, '/')):
        $path = "/{$path}";
    endif;

    // Dev-server mode: if public/dist/hot exists, Vite is running and serving
    // source files directly. Emit http://localhost:5173/<source> for the four
    // SPA entry points; anything else (CSS, images) falls through to
    // the manifest.
    if ($hotUrl === false):
        $hotPath = UrlUtil::basePath('/public' . $manifestDirectory . '/hot');
        $hotUrl  = file_exists($hotPath) ? rtrim(file_get_contents($hotPath)) : null;
    endif;

    if ($hotUrl):
        $devMap = [
            '/fe-js/bundle.js' => '/resources/js/app/User/App.jsx',
            '/css/bundle.css'  => '/resources/less/App.less',
        ];
        if (isset($devMap[$path])):
            return $hotUrl . $devMap[$path];
        endif;
    endif;

    // Filesystem path includes public/, URL path does not
    $manifestPath = UrlUtil::basePath('/public' . $manifestDirectory . '/mix-manifest.json');

    if ($manifest === null && file_exists($manifestPath)):
        $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
    endif;

    if ($manifest !== null && isset($manifest[$path])):
        return $manifestDirectory . $manifest[$path];
    endif;

    $viteManifestPath = UrlUtil::basePath('/public' . $manifestDirectory . '/.vite/manifest.json');
    if ($viteManifest === null && file_exists($viteManifestPath)):
        $viteManifest = json_decode(file_get_contents($viteManifestPath), true) ?: [];
    endif;

    $entryFile = ltrim($path, '/');
    if (is_array($viteManifest)):
        foreach ($viteManifest as $row):
            if (($row['file'] ?? null) === $entryFile):
                return $manifestDirectory . '/' . ltrim($row['file'], '/');
            endif;
        endforeach;
    endif;

    throw new Exception("Unable to locate Mix file: {$path}.");
}

/**
 * Returns the hashed URLs of every CSS file that Vite attributed to the
 * given JS entry in its manifest.json (NOT mix-manifest.json). This is
 * how route-scoped CSS chunks reach the browser: Vite emits them as
 * siblings of each JS chunk, and the SPA shell must inject a
 * <link rel="stylesheet"> for every sibling the entry pulls in.
 *
 * Dev-mode (hot file present): returns [] because Vite's HMR client
 * injects <style> tags at runtime.
 *
 * See .claude/plans/project_css_code_splitting.md.
 *
 * @param  string $jsEntry  e.g. 'fe-js/bundle.js'
 * @return array<int,string>  URL paths, already hash-busted
 */
function vite_entry_css(string $jsEntry, string $manifestDirectory = '/dist'): array
{
    static $viteManifest;

    // Dev server handles CSS over the HMR websocket — no <link> tags needed.
    if (vite_hot_url()):
        return [];
    endif;

    $manifestPath = UrlUtil::basePath('/public' . $manifestDirectory . '/.vite/manifest.json');

    if ($viteManifest === null && file_exists($manifestPath)):
        $viteManifest = json_decode(file_get_contents($manifestPath), true) ?: [];
    endif;

    if (!$viteManifest):
        return [];
    endif;

    // Map 'fe-js/bundle.js' back to its manifest key. Vite keys entries by
    // source path ('resources/js/User/App.js'), so match on the 'file' field.
    $entryKey = null;
    foreach ($viteManifest as $key => $row):
        if (($row['file'] ?? null) === $jsEntry):
            $entryKey = $key;
            break;
        endif;
    endforeach;

    if ($entryKey === null):
        return [];
    endif;

    // Walk the static-`imports` graph and collect every chunk's CSS. We
    // skip `dynamicImports` — those are route/component chunks that Vite's
    // runtime preloader handles when the import() executes, and eager-
    // linking them would defeat code-splitting.
    $cssFiles = [];
    $seen     = [];
    $stack    = [$entryKey];
    while ($stack):
        $key = array_pop($stack);
        if (isset($seen[$key])) continue;
        $seen[$key] = true;
        $row = $viteManifest[$key] ?? null;
        if (!$row) continue;
        foreach ($row['css'] ?? [] as $cssFile):
            $cssFiles[$cssFile] = true;
        endforeach;
        foreach ($row['imports'] ?? [] as $imp):
            $stack[] = $imp;
        endforeach;
    endwhile;

    if (!$cssFiles):
        return [];
    endif;

    $urls = [];
    foreach (array_keys($cssFiles) as $cssFile):
        // '/'-prefix matches the mix-manifest key format.
        $urls[] = mix('/' . $cssFile, $manifestDirectory);
    endforeach;

    return $urls;
}

/**
 * Returns the Vite dev-server URL if public/dist/hot exists, else null.
 * Used by SPA shell templates to conditionally inject the @vite/client
 * script that establishes the HMR websocket.
 */
function vite_hot_url()
{
    static $url = false;
    if ($url === false):
        $hotPath = UrlUtil::basePath('/public/dist/hot');
        $url     = file_exists($hotPath) ? rtrim(file_get_contents($hotPath)) : null;
    endif;
    return $url;
}

function domainName()
{
    return config('app.domain');
}

function normalizeUrl($url) {
    $url = trim($url);

    // Remove existing protocol if it exists
    $url = preg_replace('#^https?://#i', '', $url);

    return 'https://' . $url;
}



/*
 * This function is used for Handing Curl request either in POST or Get 
 * $data = data come in array form
 * $url = calling url
 * $header = if any header request are there.
 */
function send_curl_request($data = NULL, $url = NULL, $get = NULL, $headers = NULL) {
    $ch = curl_init();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unrecognized';

    if($get):
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

        if($data == NULL): 
            curl_setopt($ch, CURLOPT_URL, $url);
        else:
            $fields_string = $url.'?'. ($data != NULL ? http_build_query($data) : '') ;
            curl_setopt($ch, CURLOPT_URL, $fields_string);
        endif;

        if($headers):
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        endif;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    endif;

    $fields_string = ($data != NULL ? http_build_query($data) : '');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, is_countable($data) ? count($data) : 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($headers):
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    endif;
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}



function getNameAliasFromEmail($email){
    if(strlen($email)):
        $splitEmail = explode("@", $email);
        return $splitEmail[0];
    endif;

    return "Guest";
}


function parsePut() 
{
    // Fetch content and determine boundary
    $raw_data = file_get_contents('php://input');
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        return json_decode($raw_data, true);
    }

    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

    if(empty($boundary)):
        parse_str($raw_data, $data);
        return $data;
    endif;

    // Fetch each part
    $parts = array_slice(explode($boundary, $raw_data), 1);
    $data = array();

    foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n") break; 

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
            list($name, $value) = explode(':', $header);
            $headers[strtolower($name)] = ltrim($value, ' '); 
        } 

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
            $filename = null;
            preg_match(
                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
                $headers['content-disposition'], 
                $matches
            );
            list(, $type, $name) = $matches;
            isset($matches[4]) and $filename = $matches[4]; 

            // handle your fields here
            switch ($name) {
                // this is a file upload
                case 'userfile':
                    file_put_contents($filename, $body);
                    break;

                // default for all other files is to populate $data
                default: 
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                    break;
            } 
        }
    }    

    return $data;
}



function baseUrl($relative_file_path = NULL)
{
    return config('app.url') . $relative_file_path;
}

function isJson($string) {
    if(!is_string($string)) return false;
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

