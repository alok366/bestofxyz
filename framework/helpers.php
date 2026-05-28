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
    if (!str_starts_with($path, '/')):
        $path = "/{$path}";
    endif;

    // Filesystem path includes public/, URL path does not
    $manifestPath = UrlUtil::basePath('/public' . $manifestDirectory . '/mix-manifest.json');

    if (!$manifest && file_exists($manifestPath)):
        $manifest = json_decode(file_get_contents($manifestPath), true);
    endif;

    if (isset($manifest[$path])):
        return $manifestDirectory . $manifest[$path];
    endif;

    throw new Exception("Unable to locate Mix file: {$path}.");
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

/**
 * @deprecated Use $this->response instead.
 * Refer BizPlanController.php for usage.
 */
function response() 
{
    return new class {
        public function json(array $data, int $status = 200, array $headers = []) {
            http_response_code($status);
            header('Content-Type: application/json');
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
            echo json_encode($data);
            exit;
        }

        public function text(string $content, int $status = 200, array $headers = []) {
            http_response_code($status);
            header('Content-Type: text/plain');
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
            echo $content;
            exit;
        }

        public function file(string $filePath, string $downloadName = null) {
            if (!file_exists($filePath)) {
                http_response_code(404);
                echo 'File not found.';
                exit;
            }
            header('Content-Type: ' . mime_content_type($filePath));
            if ($downloadName) {
                header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            }
            readfile($filePath);
            exit;
        }

        public function createSuccess() {
            http_response_code(201); 
            header('Content-Type: application/json');
            echo json_encode([
                'data' => null,
                'status' => 'success',
                'message' => 'Resource created successfully'
            ]);
            exit;
        }
        
        public function createError() {
            http_response_code(500); 
            header('Content-Type: application/json');
            echo json_encode([
                'data' => null,
                'message' => 'Failed to create resource'
            ]);
            exit;
        }
        

        public function updateSuccess() {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'data' => null,
                'message' => 'Resource updated successfully'
            ]);
            exit;
        }

        public function updateError() {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'data' => null,
                'message' => 'Failed to update resource'
            ]);
            exit;
        }

        public function notFound() {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Resource not found'
            ]);
            exit;
        }
    };
}

function rurlPath($rotationURL, $folder)
{
    return config('app.rotation.path') . "$rotationURL/" . config('app.rotation.folder') . "$folder/";
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

