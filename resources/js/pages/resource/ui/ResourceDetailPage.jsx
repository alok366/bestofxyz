import React, { useState, useEffect } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Card, Badge } from '@shared/ui';
import { VoteControl } from '@widgets/resource-card';
import { CommentThread } from '@widgets/comment-thread';
import { bestofxyz } from '@shared/api/bestofxyz';
import styles from './ResourceDetailPage.module.less';

/**
 * ResourceDetailPage — full resource view displaying ranking badge,
 * host link, description, vote control, tag chips, and threaded discussion comments.
 */
export const ResourceDetailPage = () => {
    const { catSlug, resSlug, slug } = useParams();
    const resourceSlug = resSlug || slug;
    const [resource, setResource] = useState(null);
    const [comments, setComments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (!resourceSlug) return;
        setLoading(true);
        setError(null);

        const endpoint = catSlug && resSlug
            ? `/categories/${catSlug}/resources/${resSlug}`
            : `/resources/${resourceSlug}`;

        bestofxyz
            .get(endpoint)
            .then((data) => {
                setResource(data);
                setLoading(false);
                if (data.id) {
                    bestofxyz
                        .get(`/resources/${data.id}/comments`)
                        .then((commentsData) => {
                            if (Array.isArray(commentsData)) setComments(commentsData);
                        })
                        .catch(() => {});
                }
            })
            .catch((err) => {
                setError(err);
                setLoading(false);
            });
    }, [catSlug, resSlug, resourceSlug]);

    if (loading) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: 'var(--text-secondary, #888)' }}>
                        Loading resource…
                    </div>
                </div>
            </div>
        );
    }

    if (error || !resource) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: '#e53e3e' }}>
                        Resource not found or failed to load.
                    </div>
                    <footer className={styles.backNav}>
                        <Link to={catSlug ? `/categories/${catSlug}` : '/categories'} className={styles.backLink}>
                            ← Back to category
                        </Link>
                    </footer>
                </div>
            </div>
        );
    }

    const categorySlug = resource.categorySlug || catSlug;
    const breadcrumb = [
        { label: 'Categories', path: '/categories' },
        ...(resource.groupName ? [{ label: resource.groupName, path: `/categories/${resource.groupSlug || ''}` }] : []),
        ...(resource.categoryName ? [{ label: resource.categoryName, path: `/categories/${categorySlug}` }] : []),
        { label: resource.title, path: `/resource/${resourceSlug}` },
    ];

    const rankBadge = resource.rank ? `#${resource.rank} in ${resource.categoryName || 'Category'}` : null;
    const topicPath = resource.categorySlug ? `/categories/${resource.categorySlug}` : (catSlug ? `/categories/${catSlug}` : '/categories');
    const topic = resource.categoryName || 'category';

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                    {breadcrumb.map((crumb, idx) => (
                        <React.Fragment key={crumb.path + idx}>
                            {idx > 0 && <span className={styles.separator}>/</span>}
                            {idx === breadcrumb.length - 1 ? (
                                <span className={styles.current}>{crumb.label}</span>
                            ) : (
                                <Link to={crumb.path} className={styles.crumbLink}>
                                    {crumb.label}
                                </Link>
                            )}
                        </React.Fragment>
                    ))}
                </nav>

                <Card as="section" className={styles.heroCard}>
                    <div className={styles.voteWrapper}>
                        <VoteControl count={resource.votes ?? 0} />
                    </div>

                    <div className={styles.heroContent}>
                        {rankBadge && (
                            <Badge type="new" className={styles.rankBadge}>
                                {rankBadge}
                            </Badge>
                        )}

                        <h1 className={styles.title}>{resource.title}</h1>

                        {resource.host && (
                            <a
                                href={resource.href || '#'}
                                target="_blank"
                                rel="noopener noreferrer"
                                className={styles.hostLink}
                            >
                                {resource.host} ↗
                            </a>
                        )}

                        <p className={styles.description}>{resource.description}</p>

                        {resource.tags && resource.tags.length > 0 && (
                            <div className={styles.tagRow}>
                                {resource.tags.map((tag) => (
                                    <span key={tag} className={styles.tagChip}>
                                        {tag}
                                    </span>
                                ))}
                            </div>
                        )}

                        <div className={styles.submitterMeta}>
                            submitted by{' '}
                            <span className={styles.submitterName}>{resource.submitter}</span>
                            {resource.submitterTime && ` · ${resource.submitterTime}`}
                        </div>
                    </div>
                </Card>

                <section className={styles.discussionSection}>
                    <CommentThread
                        comments={comments}
                        commentsCount={comments.length}
                        sortOptions={['Top', 'New']}
                        currentUser={{ initials: 'Y', name: 'You' }}
                    />
                </section>

                <footer className={styles.backNav}>
                    <Link
                        to={topicPath}
                        className={styles.backLink}
                    >
                        ← Back to {topic}
                    </Link>
                </footer>
            </div>
        </div>
    );
};

export default ResourceDetailPage;

