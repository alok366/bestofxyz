import { describe, it, expect, vi, beforeEach } from 'vitest';
import authReducer, {
  authErrorCleared,
  hydrateAuth,
  loginUser,
  registerUser,
  logoutUser,
} from './authSlice';

describe('authSlice pure reducers', () => {
  const initialState = {
    user: null,
    status: 'idle',
    error: null,
  };

  it('starts with idle status, null user, null error', () => {
    const state = authReducer(undefined, { type: 'unknown' });
    expect(state).toEqual(initialState);
  });

  it('clears error on authErrorCleared', () => {
    const prevState = {
      user: null,
      status: 'unauthenticated',
      error: 'Something went wrong',
    };

    const nextState = authReducer(prevState, authErrorCleared());
    expect(nextState.error).toBeNull();
    expect(nextState.status).toBe('unauthenticated');
  });
});

describe('authSlice hydrate thunk reducers', () => {
  it('sets loading on hydrateAuth.pending', () => {
    const prevState = { user: null, status: 'idle', error: null };
    const action = { type: hydrateAuth.pending.type };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('loading');
    expect(nextState.error).toBeNull();
  });

  it('sets authenticated with user on hydrateAuth.fulfilled', () => {
    const user = { id: 1, username: 'alok', email: 'a@b.com', role: 'user' };
    const prevState = { user: null, status: 'loading', error: null };
    const action = { type: hydrateAuth.fulfilled.type, payload: { user } };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('authenticated');
    expect(nextState.user).toEqual(user);
    expect(nextState.error).toBeNull();
  });

  it('sets unauthenticated on hydrateAuth.rejected (no user-facing error)', () => {
    const prevState = { user: null, status: 'loading', error: null };
    const action = { type: hydrateAuth.rejected.type, payload: 'No stored tokens' };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('unauthenticated');
    expect(nextState.user).toBeNull();
    expect(nextState.error).toBeNull(); // hydration failure is silent
  });
});

describe('authSlice login thunk reducers', () => {
  it('sets loading on loginUser.pending', () => {
    const prevState = { user: null, status: 'unauthenticated', error: 'old error' };
    const action = { type: loginUser.pending.type };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('loading');
    expect(nextState.error).toBeNull();
  });

  it('sets authenticated with user on loginUser.fulfilled', () => {
    const user = { id: 42, username: 'alok', email: 'a@b.com', role: 'user' };
    const prevState = { user: null, status: 'loading', error: null };
    const action = { type: loginUser.fulfilled.type, payload: { user } };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('authenticated');
    expect(nextState.user).toEqual(user);
  });

  it('sets unauthenticated with error on loginUser.rejected', () => {
    const prevState = { user: null, status: 'loading', error: null };
    const action = {
      type: loginUser.rejected.type,
      payload: 'Invalid email or password.',
    };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('unauthenticated');
    expect(nextState.user).toBeNull();
    expect(nextState.error).toBe('Invalid email or password.');
  });
});

describe('authSlice register thunk reducers', () => {
  it('sets authenticated with user on registerUser.fulfilled', () => {
    const user = { id: 99, username: 'newuser', email: 'new@b.com', role: 'user' };
    const prevState = { user: null, status: 'loading', error: null };
    const action = { type: registerUser.fulfilled.type, payload: { user } };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('authenticated');
    expect(nextState.user).toEqual(user);
  });

  it('sets unauthenticated with field errors on registerUser.rejected', () => {
    const prevState = { user: null, status: 'loading', error: null };
    const errorPayload = {
      detail: 'Username is already taken.',
      errors: { username: ['This username is already registered.'] },
    };
    const action = { type: registerUser.rejected.type, payload: errorPayload };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('unauthenticated');
    expect(nextState.error).toEqual(errorPayload);
  });
});

describe('authSlice logout thunk reducers', () => {
  it('clears user and sets unauthenticated on logoutUser.fulfilled', () => {
    const prevState = {
      user: { id: 1, username: 'alok' },
      status: 'authenticated',
      error: null,
    };
    const action = { type: logoutUser.fulfilled.type };

    const nextState = authReducer(prevState, action);
    expect(nextState.status).toBe('unauthenticated');
    expect(nextState.user).toBeNull();
    expect(nextState.error).toBeNull();
  });
});
