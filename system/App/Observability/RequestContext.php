<?php

namespace App\Observability;

/**
 * W3C Trace Context singleton for this request.
 *
 * Resolves a 32-hex `trace_id` and 16-hex `span_id` once per request — from
 * the inbound `traceparent` header when upstream (e.g. rotation proxy)
 * propagates it, otherwise generated fresh. 
 *
 * Format follows W3C Trace Context §3.2:
 *   traceparent: 00-<trace_id:32hex>-<span_id:16hex>-<flags:2hex>
 *
 * Why W3C and not a proprietary 16-hex id: interop. New Relic's PHP agent,
 * OpenTelemetry, Honeycomb, Tempo, Jaeger, Datadog APM all speak this.
 * When we adopt the OpenTelemetry PHP SDK in Observability Phase 2 the
 * existing trace_ids in `_system_logs.meta` and on queue rows stay valid —
 * no cutover pain.
 *
 * Thread model: PHP is shared-nothing per-request; one singleton instance
 * lives for the duration of one PHP-FPM worker handling one request. CLI
 * (artisan, workers) gets its own instance per process. `override()` lets
 * async workers rehydrate the parent trace from a queue row before
 * processing the job.
 */
final class RequestContext
{
    private static ?self $instance = null;

    private string $traceId;
    private string $spanId;

    private function __construct(?string $incomingTraceparent = null)
    {
        $parsed = $incomingTraceparent !== null
            ? self::parseTraceparent($incomingTraceparent)
            : null;

        $this->traceId = $parsed['trace_id'] ?? self::generateTraceId();
        // Always fresh span_id — this request is its own span inside the trace.
        $this->spanId  = self::generateSpanId();
    }

    /**
     * Lazy singleton. First call under a web request reads `$_SERVER['HTTP_TRACEPARENT']`
     * if present; under CLI (workers, artisan) generates fresh until `override()` is called.
     */
    public static function instance(): self
    {
        if (self::$instance === null) :
            $incoming = $_SERVER['HTTP_TRACEPARENT'] ?? null;
            self::$instance = new self(\is_string($incoming) ? $incoming : null);
        endif;
        return self::$instance;
    }

    /**
     * Replace the current trace with a caller-supplied trace_id. Used by
     * async workers after pulling a `trace_id` off the queue row — the job
     * continues the originating request's trace under a fresh span_id.
     *
     * Silently ignores malformed input (keeps whatever trace we already had)
     * so a bad queue row can never crash the worker.
     */
    public static function override(string $traceId): void
    {
        if (!self::isValidTraceId($traceId)) :
            return;
        endif;
        $ctx = self::instance();
        $ctx->traceId = $traceId;
        $ctx->spanId  = self::generateSpanId();
    }

    /**
     * Reset the singleton. Test-only hook — let individual tests exercise
     * the construction path (header parse, fallback generation) in isolation.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getSpanId(): string
    {
        return $this->spanId;
    }

    /**
     * W3C traceparent string for outbound propagation (future: Guzzle middleware).
     * Flags fixed at `01` (sampled) for now — sampling policy comes with OTel.
     */
    public function toTraceparent(): string
    {
        return '00-' . $this->traceId . '-' . $this->spanId . '-01';
    }

    /**
     * Parse an inbound W3C traceparent header.
     *
     * Spec §3.2.2: MUST validate and fall back to fresh-generated values on
     * any parse failure — malformed upstream headers never break the request.
     *
     * @return array{trace_id: string, parent_span_id: string}|null
     */
    private static function parseTraceparent(string $header): ?array
    {
        $parts = explode('-', trim($header));
        if (count($parts) !== 4) :
            return null;
        endif;
        [$version, $traceId, $parentSpanId, $flags] = $parts;

        // Version 00 is the only spec-approved version today. Future versions
        // MAY be longer; for 00 the full length must be exactly 55 chars.
        if ($version !== '00') :
            return null;
        endif;
        if (!self::isValidTraceId($traceId) || !self::isValidSpanId($parentSpanId)) :
            return null;
        endif;
        if (!preg_match('/^[0-9a-f]{2}$/', $flags)) :
            return null;
        endif;

        return [
            'trace_id'       => $traceId,
            'parent_span_id' => $parentSpanId,
        ];
    }

    private static function generateTraceId(): string
    {
        // 16 bytes → 32 hex chars. Must not be all-zero per spec.
        do {
            $id = bin2hex(random_bytes(16));
        } while ($id === str_repeat('0', 32));
        return $id;
    }

    private static function generateSpanId(): string
    {
        // 8 bytes → 16 hex chars. Must not be all-zero per spec.
        do {
            $id = bin2hex(random_bytes(8));
        } while ($id === str_repeat('0', 16));
        return $id;
    }

    private static function isValidTraceId(string $id): bool
    {
        return preg_match('/^[0-9a-f]{32}$/', $id) === 1
            && $id !== str_repeat('0', 32);
    }

    private static function isValidSpanId(string $id): bool
    {
        return preg_match('/^[0-9a-f]{16}$/', $id) === 1
            && $id !== str_repeat('0', 16);
    }
}
