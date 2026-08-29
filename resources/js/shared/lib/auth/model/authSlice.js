/**
 * Auth Redux Slice
 * Layer: shared/lib/auth
 *
 * State shape:
 * {
 *   user: { id, username, email, role, created_at } | null,
 *   status: 'idle' | 'loading' | 'authenticated' | 'unauthenticated',
 *   error: string | null,
 * }
 *
 * Design decisions:
 * - Tokens live in localStorage only — the bestofxyz.js API client
 *   already reads them from there. Storing them in Redux would create
 *   a dual source of truth.
 * - The `status` field is a finite state machine, not a boolean, so the
 *   app can distinguish "haven't checked yet" (idle) from "checked and
 *   not logged in" (unauthenticated). This prevents flash-of-login-page
 *   on hard refresh.
 */
import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { authApi } from './authApi';

// ── localStorage helpers ──────────────────────────────────────────

const TOKEN_KEYS = { access: 'access_token', refresh: 'refresh_token' };

function storeTokens({ access_token, refresh_token }) {
  if (access_token) localStorage.setItem(TOKEN_KEYS.access, access_token);
  if (refresh_token) localStorage.setItem(TOKEN_KEYS.refresh, refresh_token);
}

function clearTokens() {
  localStorage.removeItem(TOKEN_KEYS.access);
  localStorage.removeItem(TOKEN_KEYS.refresh);
}

function hasStoredTokens() {
  return !!localStorage.getItem(TOKEN_KEYS.access);
}

// ── Async thunks ──────────────────────────────────────────────────

/**
 * Hydrate auth state on app mount.
 * Checks localStorage for an access token and, if present, calls
 * GET /api/auth/me to fetch the user profile. The bestofxyz client
 * auto-retries with refresh on 401, so this covers both fresh and
 * expired access tokens.
 */
export const hydrateAuth = createAsyncThunk(
  'auth/hydrate',
  async (_, { rejectWithValue }) => {
    if (!hasStoredTokens()) {
      return rejectWithValue('No stored tokens');
    }

    try {
      const user = await authApi.me();
      return { user };
    } catch (err) {
      clearTokens();
      return rejectWithValue(err?.detail || 'Session expired');
    }
  }
);

/**
 * Log in with email + password.
 * Stores tokens and returns user profile from the response.
 */
export const loginUser = createAsyncThunk(
  'auth/login',
  async ({ email, password }, { rejectWithValue }) => {
    try {
      const data = await authApi.login(email, password);
      storeTokens(data);
      return { user: data.user };
    } catch (err) {
      return rejectWithValue(
        err?.detail || err?.title || 'Invalid email or password.'
      );
    }
  }
);

/**
 * Register a new account.
 * Stores tokens and returns user profile from the response.
 */
export const registerUser = createAsyncThunk(
  'auth/register',
  async ({ username, email, password }, { rejectWithValue }) => {
    try {
      const data = await authApi.register(username, email, password);
      storeTokens(data);
      return { user: data.user };
    } catch (err) {
      // Pass through full error shape so the form can show field-level errors
      return rejectWithValue(
        err?.errors
          ? { detail: err.detail, errors: err.errors }
          : { detail: err?.detail || err?.title || 'Registration failed.' }
      );
    }
  }
);

/**
 * Log out — revoke refresh token server-side and clear local state.
 */
export const logoutUser = createAsyncThunk(
  'auth/logout',
  async () => {
    const refreshToken = localStorage.getItem(TOKEN_KEYS.refresh);
    if (refreshToken) {
      // Fire-and-forget — logout intent is fulfilled regardless
      authApi.logout(refreshToken).catch(() => {});
    }
    clearTokens();
  }
);

// ── Slice ─────────────────────────────────────────────────────────

const initialState = {
  user: null,
  status: 'idle',    // 'idle' | 'loading' | 'authenticated' | 'unauthenticated'
  error: null,
};

export const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    /** Reset error state (e.g. when navigating away from login page) */
    authErrorCleared: (state) => {
      state.error = null;
    },
  },
  extraReducers: (builder) => {
    // ── hydrate ────────────────────────────────────────
    builder
      .addCase(hydrateAuth.pending, (state) => {
        state.status = 'loading';
        state.error = null;
      })
      .addCase(hydrateAuth.fulfilled, (state, action) => {
        state.status = 'authenticated';
        state.user = action.payload.user;
        state.error = null;
      })
      .addCase(hydrateAuth.rejected, (state) => {
        state.status = 'unauthenticated';
        state.user = null;
        state.error = null; // hydration failure is not a user-facing error
      });

    // ── login ──────────────────────────────────────────
    builder
      .addCase(loginUser.pending, (state) => {
        state.status = 'loading';
        state.error = null;
      })
      .addCase(loginUser.fulfilled, (state, action) => {
        state.status = 'authenticated';
        state.user = action.payload.user;
        state.error = null;
      })
      .addCase(loginUser.rejected, (state, action) => {
        state.status = 'unauthenticated';
        state.user = null;
        state.error = action.payload || 'Login failed.';
      });

    // ── register ───────────────────────────────────────
    builder
      .addCase(registerUser.pending, (state) => {
        state.status = 'loading';
        state.error = null;
      })
      .addCase(registerUser.fulfilled, (state, action) => {
        state.status = 'authenticated';
        state.user = action.payload.user;
        state.error = null;
      })
      .addCase(registerUser.rejected, (state, action) => {
        state.status = 'unauthenticated';
        state.user = null;
        state.error = action.payload || { detail: 'Registration failed.' };
      });

    // ── logout ─────────────────────────────────────────
    builder
      .addCase(logoutUser.fulfilled, (state) => {
        state.status = 'unauthenticated';
        state.user = null;
        state.error = null;
      });
  },
});

export const { authErrorCleared } = authSlice.actions;
export default authSlice.reducer;
