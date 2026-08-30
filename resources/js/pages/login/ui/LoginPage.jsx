import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { Card, Button } from '@shared/ui';
import { useAuth } from '@shared/lib/auth';
import styles from './AuthPage.module.less';

/**
 * LoginPage — email + password authentication form.
 *
 * On success, redirects to the ?redirect= param or home.
 * If already authenticated, immediately redirects away.
 */
export const LoginPage = () => {
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const redirectTo = searchParams.get('redirect') || '/';

    const { isAuthenticated, isLoading, error, login, clearError } = useAuth();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [submitting, setSubmitting] = useState(false);

    // Redirect if already authenticated
    useEffect(() => {
        if (isAuthenticated) {
            navigate(redirectTo, { replace: true });
        }
    }, [isAuthenticated, navigate, redirectTo]);

    // Clear error when navigating away
    useEffect(() => {
        return () => clearError();
    }, [clearError]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!email.trim() || !password) return;

        setSubmitting(true);
        const result = await login(email.trim(), password);
        setSubmitting(false);

        // loginUser thunk returns the action — check if it was fulfilled
        if (result.meta?.requestStatus === 'fulfilled') {
            navigate(redirectTo, { replace: true });
        }
    };

    // Derive a human-readable error string
    const errorMessage = typeof error === 'string'
        ? error
        : error?.detail || null;

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <div className={styles.eyebrow}>Welcome back</div>
                    <h1 className={styles.title}>Log in to BestFor.dev</h1>
                    <p className={styles.description}>
                        Vote, comment, and submit resources to the community.
                    </p>
                </header>

                <Card as="form" className={styles.formCard} onSubmit={handleSubmit}>
                    {errorMessage && (
                        <div className={styles.globalError}>{errorMessage}</div>
                    )}

                    <div className={styles.field}>
                        <label htmlFor="login-email" className={styles.label}>
                            Email
                        </label>
                        <input
                            id="login-email"
                            type="email"
                            required
                            autoComplete="email"
                            className={styles.input}
                            placeholder="you@example.com"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                        />
                    </div>

                    <div className={styles.field}>
                        <label htmlFor="login-password" className={styles.label}>
                            Password
                        </label>
                        <input
                            id="login-password"
                            type="password"
                            required
                            autoComplete="current-password"
                            className={styles.input}
                            placeholder="••••••••"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                        />
                    </div>

                    <Button
                        variant="primary"
                        type="submit"
                        className={styles.submitBtn}
                        disabled={submitting || isLoading}
                    >
                        {submitting ? 'Logging in…' : 'Log in'}
                    </Button>
                </Card>

                <div className={styles.footer}>
                    Don&apos;t have an account?{' '}
                    <Link to={`/register${redirectTo !== '/' ? `?redirect=${encodeURIComponent(redirectTo)}` : ''}`}>
                        Sign up
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default LoginPage;
