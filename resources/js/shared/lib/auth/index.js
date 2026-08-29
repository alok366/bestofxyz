/**
 * Auth public API
 * Layer: shared/lib/auth
 */

export { useAuth } from './useAuth';
export {
  authSlice,
  default as authReducer,
  hydrateAuth,
  loginUser,
  registerUser,
  logoutUser,
  authErrorCleared,
} from './model/authSlice';
export { authListenerMiddleware } from './model/authListenerMiddleware';
export {
  selectAuthState,
  selectAuthUser,
  selectAuthStatus,
  selectAuthError,
  selectIsAuthenticated,
  selectIsAuthLoading,
  selectUserInitials,
  selectUserRole,
} from './model/selectors';
export { authApi } from './model/authApi';
