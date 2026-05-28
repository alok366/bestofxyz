<?php

namespace App\Services;

use Illuminate\Http\Response;

class ResponseService
{
    public function json(array $data, int $status = 200, array $headers = [])
    {
        http_response_code($status);
        header('Content-Type: application/json');
        foreach ($headers as $key => $value) :
            header("$key: $value");
        endforeach;
        echo json_encode($data);
        exit;
    }

    public function text(string $content, int $status = 200, array $headers = [])
    {
        http_response_code($status);
        header('Content-Type: text/plain');
        foreach ($headers as $key => $value) :
            header("$key: $value");
        endforeach;
        echo $content;
        exit;
    }

    public function file(string $filePath, ?string $downloadName = null)
    {
        if (!file_exists($filePath)) :
            http_response_code(404);
            echo 'File not found.';
            exit;
        endif;
        header('Content-Type: ' . mime_content_type($filePath));
        if ($downloadName) :
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        endif;
        readfile($filePath);
        exit;
    }

    public function createSuccess(string $message = 'Resource created successfully')
    {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'success',
            'message' => $message
        ]);
        exit;
    }

    public function createError(string $message = 'Failed to create resource')
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function updateSuccess(string $message = 'Resource updated successfully')
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'success',
            'message' => $message
        ]);
        exit;
    }

    public function updateError(string $message = 'Failed to update resource')
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function deleteSuccess(string $message = 'Resource deleted successfully')
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'success',
            'message' => $message
        ]);
        exit;
    }

    public function deleteError(string $message = 'Failed to delete resource')
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function notFound(string $message = 'Resource not found')
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function validationError($errors = [], string $message = 'Validation failed')
    {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'errors' => array_keys(is_object($errors) ? $errors->toArray() : $errors) //to avoid display real error. Just show field names
        ]);
        exit;
    }

    public function success(array $data = [], string $message = 'Request successful')
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'data'    => $data,
            'status'  => 'success',
            'message' => $message,
        ]);
        exit;
    }

    public function fetchError(string $message = 'Failed to retrieve resource')
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'data'    => null,
            'status'  => 'error',
            'message' => $message,
        ]);
        exit;
    }

    // ── Stateless API (v4) ─────────────────────────────────────────────
    // These methods return Response objects instead of echo+exit,
    // making them testable and compatible with the middleware pipeline.

    /**
     * Return a JSON success response.
     *
     * @param mixed $data Response payload (array, object, or null).
     * @param int $status HTTP status code.
     * @return Response
     */
    public function ok(mixed $data = null, int $status = 200): Response
    {
        return new Response(
            json_encode($data),
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Return an RFC 9457 problem+json error response.
     *
     * Per RFC 9457 §3.2 the problem object MAY contain "extension members" —
     * additional top-level keys carrying problem-type-specific data. The
     * canonical example is a 422 carrying `errors: { fieldName: [messages] }`
     * for validation failures (matches Laravel/Symfony's default validator
     * shape, so the frontend renderer is generic across the API surface).
     *
     * @param int $status HTTP status code.
     * @param string $title Short error title.
     * @param string $detail Human-readable error explanation.
     * @param array $extensions Optional RFC 9457 extension members.
     * @return Response
     */
    public function problem(int $status, string $title, string $detail, array $extensions = []): Response
    {
        $body = [
            'type'   => 'about:blank',
            'title'  => $title,
            'detail' => $detail,
            'status' => $status,
        ];

        return new Response(
            json_encode(array_merge($body, $extensions)),
            $status,
            ['Content-Type' => 'application/problem+json']
        );
    }
}
