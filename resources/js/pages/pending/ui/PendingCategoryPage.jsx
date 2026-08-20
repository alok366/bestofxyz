import React, { useState, useEffect } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Card, Badge, Button } from '@shared/ui';
import { RankedResourceCard } from '@widgets/resource-card';
import { bestofxyz } from '@shared/api/bestofxyz';
import styles from './PendingCategoryPage.module.less';

/**
 * PendingCategoryPage — displays the pending/proposal state of a new category
 * before it reaches the required threshold of resources to go live.
 */
export const PendingCategoryPage = () => {
    const { slug } = useParams();
    const [category, setCategory] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (!slug) return;
        setLoading(true);
        setError(null);
        bestofxyz
            .get(`/pending/${slug}`)
            .then((data) => {
                setCategory(data);
                setLoading(false);
            })
            .catch((err) => {
                setError(err);
                setLoading(false);
            });
    }, [slug]);

    if (loading) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: 'var(--text-secondary, #888)' }}>
                        Loading pending category…
                    </div>
                </div>
            </div>
        );
    }

    if (error || !category) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: '#e53e3e' }}>
                        Pending category not found or failed to load.
                    </div>
                    <footer className={styles.backNav}>
                        <Link to="/categories" className={styles.backLink}>
                            ← Back to all categories
                        </Link>
                    </footer>
                </div>
            </div>
        );
    }

    const currentCount = category.threshold?.current ?? (category.resources?.length ?? 0);
    const targetCount = category.threshold?.required ?? 5;
    const percentage = targetCount > 0 ? Math.min(100, Math.round((currentCount / targetCount) * 100)) : 0;
    const neededCount = Math.max(0, targetCount - currentCount);

    const breadcrumbs = [
        { label: 'Categories', path: '/categories' },
        ...(category.group ? [{ label: category.group, path: '/categories' }] : []),
        { label: category.name || slug, path: `/pending/${slug}` },
    ];

    const resources = category.resources || [];

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                        {breadcrumbs.map((crumb, idx) => (
                            <React.Fragment key={crumb.path + idx}>
                                {idx > 0 && <span className={styles.separator}>/</span>}
                                {idx === breadcrumbs.length - 1 ? (
                                    <span className={styles.current}>{crumb.label}</span>
                                ) : (
                                    <Link to={crumb.path} className={styles.crumbLink}>
                                        {crumb.label}
                                    </Link>
                                )}
                            </React.Fragment>
                        ))}
                    </nav>

                    <Badge type="new" className={styles.statusBadge}>
                        New — not yet public
                    </Badge>

                    <h1 className={styles.title}>{category.name}</h1>
                    <p className={styles.description}>{category.description}</p>
                </header>

                <section className={styles.thresholdSection}>
                    <Card as="div" className={styles.thresholdCard}>
                        <div className={styles.progressHead}>
                            <div>
                                <div className={styles.progressTitle}>
                                    {currentCount} of {targetCount} resources
                                </div>
                                <div className={styles.progressSubtext}>
                                    needed before this category goes live
                                </div>
                            </div>
                            <div className={styles.progressPercent}>{percentage}%</div>
                        </div>

                        <div className={styles.progressBar}>
                            <div
                                className={styles.progressFill}
                                style={{ width: `${percentage}%` }}
                            />
                        </div>

                        <p className={styles.explanation}>
                            Once it clears the threshold, this shows up in the category directory and becomes searchable.
                            Until then it's only reachable by direct link — anyone who lands here can still browse and vote on what's already in it.
                        </p>

                        <Link to="/submit" className={styles.ctaLink}>
                            <Button variant="primary" className={styles.ctaButton}>
                                Submit a resource to help it go live →
                            </Button>
                        </Link>
                    </Card>
                </section>

                <section className={styles.resourcesSection}>
                    <h2 className={styles.sectionTitle}>Resources so far</h2>

                    <div className={styles.resourceList}>
                        {resources.map((resource) => (
                            <RankedResourceCard key={resource.id || resource.title} {...resource} />
                        ))}

                        {neededCount > 0 && (
                            <Link to="/submit" className={styles.addDashedCard}>
                                + Add one of the {neededCount} resources still needed
                            </Link>
                        )}
                    </div>
                </section>

                <footer className={styles.backNav}>
                    <Link to="/categories" className={styles.backLink}>
                        ← Back to all categories
                    </Link>
                </footer>
            </div>
        </div>
    );
};

export default PendingCategoryPage;

