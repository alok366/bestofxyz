/**
 * Auth API layer — thin wrappers around the bestofxyz client.
 * Layer: shared/lib/auth
 *
 * All functions return Promises. Error handling is done by the caller
 * (async thunks in authSlice). The bestofxyz client already handles
 * Bearer token injection and transparent 401 refresh.
 */

import { bestofxyz } from '@shared/api/bestofxyz';

export const authApi = {
  login: (email, password) =>
    bestofxyz.post('/auth/login', { email, password }),

  register: (username, email, password) =>
    bestofxyz.post('/auth/register', { username, email, password }),

  me: () =>
    bestofxyz.get('/auth/me'),

  refresh: (refreshToken) =>
    bestofxyz.post('/auth/refresh', { refresh_token: refreshToken }),

  logout: (refreshToken) =>
    bestofxyz.post('/auth/logout', { refresh_token: refreshToken }),
};
