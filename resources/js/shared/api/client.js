const BASE_URL = '/api';

async function request(method, path, { body, params } = {}) {
    const url = new URL(BASE_URL + path, window.location.origin);
    if (params) {
        Object.entries(params).forEach(([k, v]) => {
            if (v !== null && v !== undefined) url.searchParams.set(k, v);
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

    if (res.status === 401) {
        const refreshed = await tryRefreshToken();
        if (refreshed) return request(method, path, { body, params });
        window.location.href = '/auth/login';
        throw new Error('Authentication required');
    }

    if (!res.ok) {
        const error = await res.json().catch(() => ({}));
        throw { status: res.status, ...error };
    }

    return res.json();
}

async function tryRefreshToken() {
    const refreshToken = localStorage.getItem('refresh_token');
    if (!refreshToken) return false;

    try {
        const res = await fetch(BASE_URL + '/auth/refresh', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ refresh_token: refreshToken }),
        });
        if (!res.ok) return false;
        const data = await res.json();
        localStorage.setItem('access_token', data.access_token);
        localStorage.setItem('refresh_token', data.refresh_token);
        return true;
    } catch {
        return false;
    }
}

export const api = {
    get:    (path, params) => request('GET', path, { params }),
    post:   (path, body)   => request('POST', path, { body }),
    delete: (path)         => request('DELETE', path),
};

export default api;
