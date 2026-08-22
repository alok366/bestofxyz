/**
 * Multi-tier frontend Logger — mirrors BMLogger (PHP) architecture.
 *
 * Tier 1: Console    — always (dev: all levels; prod: warn/error/fatal only)
 * Tier 2: Backend API — error + fatal → POST /api/v4/logs → DB
 * Tier 3: Slack      — triggered server-side (Tier 2 tags level; backend decides)
 *
 * Slack messages include [FRONTEND] prefix so engineers can distinguish
 * from [BACKEND] errors at a glance.
 */

const LOG_ENDPOINT = '/api/v4/logs';
const IS_DEV = import.meta.env.DEV || import.meta.env.VITE_DEBUG == '1' || import.meta.env.VITE_DEBUG === 'true';
const API_BASE = String(import.meta.env.VITE_APP_URL || '').replace(/\/+$/, '');

/**
 * Benign messages we never send to the backend log.
 */
const _IGNORED_MESSAGES = [
    'ResizeObserver loop completed with undelivered notifications',
    'ResizeObserver loop limit exceeded',
    'No elements with [data-tippy-content] found'
];

const _isIgnored = (message) => {
    const text = typeof message === 'string' ? message : String(message);
    return _IGNORED_MESSAGES.some((needle) => text.toLowerCase().includes(needle.toLowerCase()));
};

/** Rate-limit: max 10 API log calls per minute to prevent flood */
let _apiCallCount = 0;
let _apiResetTimer = null;

const _canSendToApi = () => {
    if (_apiCallCount >= 10) {
        return false;
    }
    _apiCallCount++;
    if (!_apiResetTimer) {
        _apiResetTimer = setTimeout(() => {
            _apiCallCount = 0;
            _apiResetTimer = null;
        }, 60000);
    }
    return true;
};

/**
 * Tier 2 — send error/fatal to backend for DB storage + Slack notification.
 * Fire-and-forget: never blocks caller, never throws.
 */
const _sendToBackend = (level, message, context, meta) => {
    if (_isIgnored(message)) {
        return;
    }
    if (!_canSendToApi()) {
        return;
    }

    const payload = {
        level,
        message: typeof message === 'string' ? message : String(message),
        context,
        url: window.location.href,
        user_agent: navigator.userAgent,
        meta: meta || null
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers = { 'Content-Type': 'application/json' };
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    fetch(API_BASE + LOG_ENDPOINT, {
        method: 'POST',
        headers,
        body: JSON.stringify(payload),
        keepalive: true
    }).catch(() => {
        // Tier 2 failure is silent — we already logged to console (Tier 1)
    });
};

/**
 * Extract a readable message from various input types.
 */
const _extractMessage = (input) => {
    if (typeof input === 'string') {
        return input;
    }
    if (input instanceof Error) {
        return input.stack || input.message;
    }
    if (typeof input === 'object' && input !== null) {
        if (input.message) {
            return input.error?.stack || input.error?.message || input.message;
        }
        return JSON.stringify(input);
    }
    return 'An unknown error occurred.';
};

class Logger {
    /**
     * Debug — console only, dev mode only.
     * @param {string} label - Group label
     * @param {*} message - Debug data
     */
    static debug(label, message) {
        if (!IS_DEV) {
            return;
        }
        console.group(label);
        console.warn(message);
        console.groupEnd();
    }

    /**
     * Info — console only, dev mode only.
     * @param {*} message - Info message
     */
    static info(message) {
        if (!IS_DEV) {
            return;
        }
        console.log(message);
    }

    /**
     * Warn — console always, no backend.
     * @param {*} message - Warning message
     * @param {string} [context] - Component/method name
     */
    static warn(message, context = '') {
        const msg = _extractMessage(message);
        console.warn(context ? `[${context}] ${msg}` : msg);
    }

    /**
     * Error — console + backend API (Tier 2).
     * @param {*} input - Error object, string, or {message, error} shape
     * @param {string} [context] - Component/method name
     * @param {Object} [meta] - Optional structured metadata
     */
    static error(input, context = '', meta = null) {
        const msg = _extractMessage(input);
        console.error(context ? `[${context}] ${msg}` : msg);
        _sendToBackend('error', msg, context, meta);
    }

    /**
     * Fatal — console + backend API (Tier 2) + triggers Slack (Tier 3 server-side).
     * @param {*} input - Error object or string
     * @param {string} [context] - Component/method name
     * @param {Object} [meta] - Optional structured metadata
     */
    static fatal(input, context = '', meta = null) {
        const msg = _extractMessage(input);
        console.error(`[FATAL] ${context ? `[${context}] ` : ''}${msg}`);
        _sendToBackend('fatal', msg, context, meta);
    }
}

if (typeof window !== 'undefined') {
    window.addEventListener('error', (event) => {
        Logger.fatal(
            event.error || event.message,
            'window::unhandledError',
            { filename: event.filename, lineno: event.lineno, colno: event.colno }
        );
    });

    window.addEventListener('unhandledrejection', (event) => {
        Logger.fatal(
            event.reason?.message || event.reason || 'Unhandled promise rejection',
            'window::unhandledRejection'
        );
    });
}

export default Logger;
