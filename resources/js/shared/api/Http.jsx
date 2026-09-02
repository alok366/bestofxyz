import axios from 'axios';
import Logger from '../lib/Logger';

/**
 * Http — Promise-based API client (no envelope, no callbacks).
 *
 * HTTP status codes drive success/failure. Response body is the data.
 * Sends CSRF token on every request. Auth tokens are managed server-side
 * via httpOnly cookies — no token storage in JS.
 *
 * Usage:
 *   import { Http } from '@shared/api';
 *
 *   const categories = await Http.get('/categories');
 *   const created    = await Http.post('/resources', payload);
 *   const updated    = await Http.put(`/resources/${id}`, payload);
 *   await Http.delete(`/resources/${id}`);
 *
 *   // File upload (FormData) — browser sets Content-Type + boundary automatically
 *   const formData = new FormData();
 *   formData.append('image', file);
 *   const uploaded = await Http.post('/resources', formData);
 */

const CSRF       = typeof document !== 'undefined' ? (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '') : '';
const API_BASE   = String(import.meta.env.VITE_APP_URL || '').replace(/\/+$/, '') + '/api/v4';
const AUTH_BASE  = String(import.meta.env.VITE_APP_URL || '').replace(/\/+$/, '');
const DEFAULT_TIMEOUT = 15000;

const setMode = () => {};

const getHeaders = (data) => {
    const headers = { 'X-CSRF-TOKEN': CSRF };

    if (!(data instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const request = async (method, endpoint, { data = null, params = null, timeout = DEFAULT_TIMEOUT, responseType = 'json', signal = null } = {}) => {
    try {
        const response = await axios({
            method,
            url: API_BASE + endpoint,
            params: params || undefined,
            data: data || undefined,
            headers: getHeaders(data),
            timeout,
            responseType,
            signal: signal || undefined,
        });

        return response.data;
    } catch (err) {
        if (err.response?.status === 403 && typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('pk:forbidden'));
        }
        Logger.error(err, `Http::request ${method.toUpperCase()} ${endpoint}`);
        throw err;
    }
};

const Http = {
    get:    (endpoint, params, options)  => request('get',    endpoint, { params, ...options }),
    post:   (endpoint, data,   options)  => request('post',   endpoint, { data,   ...options }),
    put:    (endpoint, data,   options)  => request('put',    endpoint, { data,   ...options }),
    delete: (endpoint, data,   options)  => request('delete', endpoint, { data,   ...options }),

    /**
     * Legacy mode setter stub for backwards compatibility.
     */
    setMode,

    /**
     * Authenticate with username and password.
     * Server sets httpOnly cookie — no token handling in JS.
     */
    authenticate: async (username, password) => {
        try {
            const response = await axios({
                method: 'post',
                url: AUTH_BASE + '/auth/token',
                data: { username, password },
                headers: { 'Content-Type': 'application/json' },
                timeout: DEFAULT_TIMEOUT,
            });

            return response.data;
        } catch (err) {
            Logger.error(err, 'Http::authenticate');
            throw err;
        }
    },

    /**
     * Revoke the session cookie server-side.
     * Silently succeeds — logout intent is fulfilled regardless.
     */
    logout: async () => {
        try {
            await axios({
                method: 'post',
                url: AUTH_BASE + '/auth/logout',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                timeout: DEFAULT_TIMEOUT,
            });
        } catch (err) {
            Logger.error(err, 'Http::logout');
        }
    },
};

export { Http };
export default Http;
