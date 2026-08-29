import { describe, it, expect } from 'vitest';
import {
  selectAuthState,
  selectAuthUser,
  selectAuthStatus,
  selectAuthError,
  selectIsAuthenticated,
  selectIsAuthLoading,
  selectUserInitials,
  selectUserRole,
} from './selectors';

const authenticatedState = {
  auth: {
    user: { id: 42, username: 'alok', email: 'a@b.com', role: 'admin', created_at: '2026-08-17T00:00:00Z' },
    status: 'authenticated',
    error: null,
  },
};

const unauthenticatedState = {
  auth: {
    user: null,
    status: 'unauthenticated',
    error: 'Invalid email or password.',
  },
};

const idleState = {
  auth: {
    user: null,
    status: 'idle',
    error: null,
  },
};

const loadingState = {
  auth: {
    user: null,
    status: 'loading',
    error: null,
  },
};

describe('auth selectors', () => {
  it('selectAuthState returns the full auth slice', () => {
    expect(selectAuthState(authenticatedState)).toBe(authenticatedState.auth);
  });

  it('selectAuthUser returns user when authenticated', () => {
    expect(selectAuthUser(authenticatedState)).toEqual(authenticatedState.auth.user);
  });

  it('selectAuthUser returns null when unauthenticated', () => {
    expect(selectAuthUser(unauthenticatedState)).toBeNull();
  });

  it('selectAuthUser returns null when auth slice is missing', () => {
    expect(selectAuthUser({})).toBeNull();
  });

  it('selectAuthStatus returns the status string', () => {
    expect(selectAuthStatus(authenticatedState)).toBe('authenticated');
    expect(selectAuthStatus(unauthenticatedState)).toBe('unauthenticated');
    expect(selectAuthStatus(idleState)).toBe('idle');
  });

  it('selectAuthError returns the error or null', () => {
    expect(selectAuthError(authenticatedState)).toBeNull();
    expect(selectAuthError(unauthenticatedState)).toBe('Invalid email or password.');
  });

  it('selectIsAuthenticated is true only when authenticated', () => {
    expect(selectIsAuthenticated(authenticatedState)).toBe(true);
    expect(selectIsAuthenticated(unauthenticatedState)).toBe(false);
    expect(selectIsAuthenticated(idleState)).toBe(false);
    expect(selectIsAuthenticated(loadingState)).toBe(false);
  });

  it('selectIsAuthLoading is true for idle and loading', () => {
    expect(selectIsAuthLoading(idleState)).toBe(true);
    expect(selectIsAuthLoading(loadingState)).toBe(true);
    expect(selectIsAuthLoading(authenticatedState)).toBe(false);
    expect(selectIsAuthLoading(unauthenticatedState)).toBe(false);
  });

  it('selectUserInitials returns first letter of username uppercased', () => {
    expect(selectUserInitials(authenticatedState)).toBe('A');
  });

  it('selectUserInitials returns "U" fallback when no user', () => {
    expect(selectUserInitials(unauthenticatedState)).toBe('U');
    expect(selectUserInitials({})).toBe('U');
  });

  it('selectUserRole returns the role when authenticated', () => {
    expect(selectUserRole(authenticatedState)).toBe('admin');
  });

  it('selectUserRole returns null when unauthenticated', () => {
    expect(selectUserRole(unauthenticatedState)).toBeNull();
  });
});
