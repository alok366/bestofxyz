/**
 * bestofxyz — lightweight API client for public and authenticated endpoints.
 *
 * Uses native fetch (no axios). Base URL is /api.
 * Attaches Bearer token from localStorage when present.
 */

const BASE = '/api';

async function request(method, path, { body, params } = {}) {
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
