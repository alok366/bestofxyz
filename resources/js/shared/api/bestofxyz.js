/**
 * bestofxyz — lightweight API client for public and authenticated endpoints.
 *
 * Uses native fetch (no axios). Base URL is /api.
 * Attaches Bearer token from localStorage when present.
 * Transparently attempts token refresh on 401.
 */

const BASE = '/api';

async function tryRefreshToken() {
    const refreshToken = localStorage.getItem('refresh_token');
    if (!refreshToken) return false;

    try {
        const res = await fetch(`${BASE}/auth/refresh`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ refresh_token: refreshToken }),
        });
        if (!res.ok) {
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            return false;
        }
        const data = await res.json();
        if (data.access_token) {
            localStorage.setItem('access_token', data.access_token);
        }
        if (data.refresh_token) {
            localStorage.setItem('refresh_token', data.refresh_token);
        }
        return true;
    } catch {
        return false;
    }
}

async function request(method, path, { body, params, retry = true } = {}) {
    const url = new URL(BASE + path, window.location.origin);
    if (params) {
        Object.entries(params).forEach(([k, v]) => {
            if (v != null) url.searchParams.set(k, v);
        });
    }

    const headers = { 'Content-Type': 'application/json' };
    const token = localStorage.getItem('access_token');
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res = await fetch(url, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
    });

    if (res.status === 401 && retry && !path.startsWith('/auth/')) {
        const refreshed = await tryRefreshToken();
        if (refreshed) {
            return request(method, path, { body, params, retry: false });
        }
    }

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw { status: res.status, ...err };
    }

    return res.json();
}

export const bestofxyz = {
    get:    (path, params) => request('GET', path, { params }),
    post:   (path, body)   => request('POST', path, { body }),
    delete: (path)         => request('DELETE', path),
};

export const api = bestofxyz;
export default bestofxyz;
