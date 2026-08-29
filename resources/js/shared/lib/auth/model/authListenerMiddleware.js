/**
 * Auth Listener Middleware for localStorage Side-Effects
 * Layer: shared/lib/auth
 *
 * Follows the same pattern as themeListenerMiddleware.js.
 * Handles side-effects that should happen when auth state changes
 * but don't belong in the slice reducers (which must be pure).
 */
import { createListenerMiddleware, isAnyOf } from '@reduxjs/toolkit';
import { logoutUser } from './authSlice';

export const authListenerMiddleware = createListenerMiddleware();

/**
 * On logout, clear tokens from localStorage.
 *
 * Note: the logoutUser thunk already clears tokens before the action
 * reaches reducers, so this listener is a safety net — ensuring tokens
 * are cleared even if a future code path dispatches the fulfilled action
 * without going through the thunk.
 */
authListenerMiddleware.startListening({
  matcher: isAnyOf(logoutUser.fulfilled),
  effect: () => {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  },
});
