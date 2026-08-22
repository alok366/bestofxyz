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
 *   const campaigns = await Http.get('/campaigns');
 *   const created   = await Http.post('/campaigns', { name: 'New' });
 *   const updated   = await Http.put(`/campaigns/${id}`, payload);
 *   await Http.delete(`/campaigns/${id}`);
 *
 *   // File upload (FormData) — browser sets Content-Type + boundary automatically
 *   const formData = new FormData();
 *   formData.append('image', file);
 *   const uploaded = await Http.post('/media', formData);
 */

const CSRF       = typeof document !== 'undefined' ? (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '') : '';
const API_BASE   = String(import.meta.env.VITE_APP_URL || '').replace(/\/+$/, '') + '/api/v4';
const AUTH_BASE  = String(import.meta.env.VITE_APP_URL || '').replace(/\/+$/, '');
const DEFAULT_TIMEOUT = 15000;

/**
 * Engine mode. Growth (default) stamps X-Campaign-ID from the active
 * Growth campaign onto every request. Broadcasts (the outbound mailer)
 * has no relation to Growth campaigns, so we MUST NOT send that header
 * on /mailer/* — otherwise a future server-side scope middleware would
 * silently filter Broadcasts queries by an unrelated Growth campaign id.
 *
 * The router calls Http.setMode() on route change. Defaulting to
 * 'growth' is safe because all existing consumers are Growth.
 */
let currentMode = 'growth';

const setMode = (mode) => {
    if (mode === 'growth' || mode === 'broadcasts') {
        currentMode = mode;
    }
};

/**
 * Read the current campaign public_id from localStorage.
 */
const getCampaignId = () => {
    try {
        if (typeof localStorage === 'undefined') return null;
        const raw = localStorage.getItem('pkzbmerscurcamp');
        if (!raw) return null;
        const store = JSON.parse(raw);
        return store?.data?.id || null;
    } catch (err) {
        Logger.error(err, 'Http::getCampaignId');
        return null;
    }
};

const getHeaders = (data, campaignOverride = null) => {
    const headers = { 'X-CSRF-TOKEN': CSRF };

    if (currentMode === 'growth') {
        const campaignId = campaignOverride || getCampaignId();
        if (campaignId) {
            headers['X-Campaign-ID'] = campaignId;
        }
    }

    if (!(data instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const request = async (method, endpoint, { data = null, params = null, timeout = DEFAULT_TIMEOUT, responseType = 'json', signal = null, campaignId = null } = {}) => {
    try {
        const response = await axios({
            method,
            url: API_BASE + endpoint,
            params: params || undefined,
            data: data || undefined,
            headers: getHeaders(data, campaignId),
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
     * Switch engine mode. 'growth' (default) stamps X-Campaign-ID;
     * 'broadcasts' suppresses it. Router calls this on route change.
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
