import React from 'react';
import { Link } from 'react-router-dom';
import { Card, Badge, Button } from '@shared/ui';
import { RankedResourceCard } from '@widgets/resource-card';
import { MOCK_PENDING_CATEGORY } from '../model/mockData';
import styles from './PendingCategoryPage.module.less';

/**
 * PendingCategoryPage — displays the pending/proposal state of a new category
 * before it reaches the required threshold of resources to go live.
 */
export const PendingCategoryPage = () => {
    const category = MOCK_PENDING_CATEGORY;
    const neededCount = category.threshold.targetCount - category.threshold.currentCount;

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                        {category.breadcrumb.map((crumb, idx) => (
                            <React.Fragment key={crumb.path}>
                                {idx > 0 && <span className={styles.separator}>/</span>}
                                {idx === category.breadcrumb.length - 1 ? (
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
                        {category.badge}
                    </Badge>

                    <h1 className={styles.title}>{category.title}</h1>
                    <p className={styles.description}>{category.description}</p>
                </header>

                <section className={styles.thresholdSection}>
                    <Card as="div" className={styles.thresholdCard}>
                        <div className={styles.progressHead}>
                            <div>
                                <div className={styles.progressTitle}>
                                    {category.threshold.currentCount} of{' '}
                                    {category.threshold.targetCount} resources
                                </div>
                                <div className={styles.progressSubtext}>
                                    {category.threshold.subtext}
                                </div>
                            </div>
                            <div className={styles.progressPercent}>
                                {category.threshold.percentage}%
                            </div>
                        </div>

                        <div className={styles.progressBar}>
                            <div
                                className={styles.progressFill}
                                style={{ width: `${category.threshold.percentage}%` }}
                            />
                        </div>

                        <p className={styles.explanation}>{category.threshold.explanation}</p>

                        <Link to={category.threshold.ctaPath} className={styles.ctaLink}>
                            <Button variant="primary" className={styles.ctaButton}>
                                {category.threshold.ctaText}
                            </Button>
                        </Link>
                    </Card>
                </section>

                <section className={styles.resourcesSection}>
                    <h2 className={styles.sectionTitle}>Resources so far</h2>

                    <div className={styles.resourceList}>
                        {category.resources.map((resource) => (
                            <RankedResourceCard key={resource.id || resource.title} {...resource} />
                        ))}

                        <Link to="/submit" className={styles.addDashedCard}>
                            + Add one of the {neededCount} resources still needed
                        </Link>
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
