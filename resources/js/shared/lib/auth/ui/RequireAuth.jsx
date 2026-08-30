import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../useAuth';

/**
 * RequireAuth — route guard component.
 *
 * Wraps routes that require authentication:
 *   <Route path="/submit" element={<RequireAuth><SubmitResourcePage /></RequireAuth>} />
 *
 * Behavior:
 * - status 'idle' or 'loading' → show a loading spinner (auth is hydrating)
 * - status 'unauthenticated'   → redirect to /login?redirect=<current-path>
 * - status 'authenticated'     → render children
 */
export const RequireAuth = ({ children }) => {
    const { isAuthenticated, isLoading } = useAuth();
    const location = useLocation();

    if (isLoading) {
        return (
            <div
                style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    minHeight: '40vh',
                    color: 'var(--muted, #888)',
                    fontSize: '14px',
                }}
            >
                Checking authentication…
            </div>
        );
    }

    if (!isAuthenticated) {
        return (
            <Navigate
                to={`/login?redirect=${encodeURIComponent(location.pathname)}`}
                replace
            />
        );
    }

    return children;
};

export default RequireAuth;
