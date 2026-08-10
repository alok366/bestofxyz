import React from 'react';
import { Link } from 'react-router-dom';
import { Card, Badge } from '@shared/ui';
import { VoteControl } from '@widgets/resource-card';
import { CommentThread } from '@widgets/comment-thread';
import { MOCK_RESOURCE_DETAIL } from '../model/mockData';
import styles from './ResourceDetailPage.module.less';

/**
 * ResourceDetailPage — full resource view displaying ranking badge,
 * host link, description, vote control, tag chips, and threaded discussion comments.
 */
export const ResourceDetailPage = () => {
    const resource = MOCK_RESOURCE_DETAIL;

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                    {resource.breadcrumb.map((crumb, idx) => (
                        <React.Fragment key={crumb.path}>
                            {idx > 0 && <span className={styles.separator}>/</span>}
                            {idx === resource.breadcrumb.length - 1 ? (
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
                        <VoteControl count={resource.votes} />
                    </div>

                    <div className={styles.heroContent}>
                        {resource.rankBadge && (
                            <Badge type="new" className={styles.rankBadge}>
                                {resource.rankBadge}
                            </Badge>
                        )}

                        <h1 className={styles.title}>{resource.title}</h1>

                        {resource.host && (
                            <a
                                href={resource.hostUrl || '#'}
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
                            {resource.submittedTimeAgo && ` · ${resource.submittedTimeAgo}`}
                        </div>
                    </div>
                </Card>

                <section className={styles.discussionSection}>
                    <CommentThread
                        comments={resource.comments}
                        commentsCount={resource.commentsCount}
                        sortOptions={resource.commentsSortOptions}
                        currentUser={resource.currentUser}
                    />
                </section>

                <footer className={styles.backNav}>
                    <Link
                        to={resource.topicPath || '/categories'}
                        className={styles.backLink}
                    >
                        ← Back to {resource.topic || 'categories'}
                    </Link>
                </footer>
            </div>
        </div>
    );
};

export default ResourceDetailPage;
