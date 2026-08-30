import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { Card, Button } from '@shared/ui';
import { useAuth } from '@shared/lib/auth';
import styles from '@pages/login/ui/AuthPage.module.less';

/**
 * RegisterPage — username + email + password registration form.
 *
 * On success, auto-logs in and redirects to ?redirect= param or home.
 * Shows field-level validation errors from the backend (RFC 9457).
 */
export const RegisterPage = () => {
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const redirectTo = searchParams.get('redirect') || '/';

    const { isAuthenticated, isLoading, error, register, clearError } = useAuth();

    const [username, setUsername] = useState('');
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
        if (!username.trim() || !email.trim() || !password) return;

        setSubmitting(true);
        const result = await register(username.trim(), email.trim(), password);
        setSubmitting(false);

        if (result.meta?.requestStatus === 'fulfilled') {
            navigate(redirectTo, { replace: true });
        }
    };

    // Extract field-level errors and global error from the error object
    const fieldErrors = error?.errors || {};
    const globalError = typeof error === 'string'
        ? error
        : (error?.detail && !error?.errors ? error.detail : null);

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <div className={styles.eyebrow}>Join the community</div>
                    <h1 className={styles.title}>Create your account</h1>
                    <p className={styles.description}>
                        Start voting, commenting, and submitting the best resources.
                    </p>
                </header>

                <Card as="form" className={styles.formCard} onSubmit={handleSubmit}>
                    {globalError && (
                        <div className={styles.globalError}>{globalError}</div>
                    )}

                    <div className={styles.field}>
                        <label htmlFor="register-username" className={styles.label}>
                            Username
                        </label>
                        <input
                            id="register-username"
                            type="text"
                            required
                            autoComplete="username"
                            className={`${styles.input} ${fieldErrors.username ? styles.inputError : ''}`}
                            placeholder="alok_dev"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                        />
                        {fieldErrors.username && (
                            <div className={styles.fieldError}>{fieldErrors.username[0]}</div>
                        )}
                    </div>

                    <div className={styles.field}>
                        <label htmlFor="register-email" className={styles.label}>
                            Email
                        </label>
                        <input
                            id="register-email"
                            type="email"
                            required
                            autoComplete="email"
                            className={`${styles.input} ${fieldErrors.email ? styles.inputError : ''}`}
                            placeholder="you@example.com"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                        />
                        {fieldErrors.email && (
                            <div className={styles.fieldError}>{fieldErrors.email[0]}</div>
                        )}
                    </div>

                    <div className={styles.field}>
                        <label htmlFor="register-password" className={styles.label}>
                            Password
                        </label>
                        <input
                            id="register-password"
                            type="password"
                            required
                            minLength={8}
                            autoComplete="new-password"
                            className={`${styles.input} ${fieldErrors.password ? styles.inputError : ''}`}
                            placeholder="Min. 8 characters"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                        />
                        {fieldErrors.password && (
                            <div className={styles.fieldError}>{fieldErrors.password[0]}</div>
                        )}
                    </div>

                    <Button
                        variant="primary"
                        type="submit"
                        className={styles.submitBtn}
                        disabled={submitting || isLoading}
                    >
                        {submitting ? 'Creating account…' : 'Create account'}
                    </Button>
                </Card>

                <div className={styles.footer}>
                    Already have an account?{' '}
                    <Link to={`/login${redirectTo !== '/' ? `?redirect=${encodeURIComponent(redirectTo)}` : ''}`}>
                        Log in
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default RegisterPage;
