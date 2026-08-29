/**
 * React hook for consuming auth state and actions.
 * Layer: shared/lib/auth
 *
 * Usage:
 *   const { user, isAuthenticated, isLoading, login, register, logout } = useAuth();
 */
import { useCallback } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { loginUser, registerUser, logoutUser, authErrorCleared } from './model/authSlice';
import {
  selectAuthUser,
  selectAuthStatus,
  selectAuthError,
  selectIsAuthenticated,
  selectIsAuthLoading,
  selectUserInitials,
} from './model/selectors';

export const useAuth = () => {
  const dispatch = useDispatch();
  const user = useSelector(selectAuthUser);
  const status = useSelector(selectAuthStatus);
  const error = useSelector(selectAuthError);
  const isAuthenticated = useSelector(selectIsAuthenticated);
  const isLoading = useSelector(selectIsAuthLoading);
  const initials = useSelector(selectUserInitials);

  const login = useCallback(
    (email, password) => dispatch(loginUser({ email, password })),
    [dispatch]
  );

  const register = useCallback(
    (username, email, password) => dispatch(registerUser({ username, email, password })),
    [dispatch]
  );

  const logout = useCallback(
    () => dispatch(logoutUser()),
    [dispatch]
  );

  const clearError = useCallback(
    () => dispatch(authErrorCleared()),
    [dispatch]
  );

  return {
    user,
    status,
    error,
    isAuthenticated,
    isLoading,
    initials,
    login,
    register,
    logout,
    clearError,
  };
};
