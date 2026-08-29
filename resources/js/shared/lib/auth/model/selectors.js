/**
 * Auth state selectors
 * Layer: shared/lib/auth
 */

export const selectAuthState = (state) => state.auth;

export const selectAuthUser = (state) => state.auth?.user ?? null;

export const selectAuthStatus = (state) => state.auth?.status ?? 'idle';

export const selectAuthError = (state) => state.auth?.error ?? null;

export const selectIsAuthenticated = (state) =>
  state.auth?.status === 'authenticated';

export const selectIsAuthLoading = (state) =>
  state.auth?.status === 'loading' || state.auth?.status === 'idle';

export const selectUserInitials = (state) => {
  const username = state.auth?.user?.username;
  if (!username) return 'U';
  return username.charAt(0).toUpperCase();
};

export const selectUserRole = (state) =>
  state.auth?.user?.role ?? null;
