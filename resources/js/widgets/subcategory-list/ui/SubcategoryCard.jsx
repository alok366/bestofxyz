import React from 'react';
import { Card, Badge } from '@shared/ui';
import styles from './SubcategoryCard.module.less';

/**
 * SubcategoryCard — list row representing a subcategory with top pick stats
 * or proposal progress bar.
 *
 * @param {object} props
 * @param {string} props.title - Subcategory title
 * @param {string} props.description - Short summary blurb
 * @param {string} [props.href='#'] - Navigation link
 * @param {string} [props.topPick] - Name of top pick resource
 * @param {number} [props.topPickVotes] - Upvote count for top pick
 * @param {number} [props.commentsThisWeek] - Recent comment count
 * @param {number} [props.resourceCount] - Total resources in subcategory
 * @param {boolean} [props.isPending=false] - Whether this subcategory is a pending proposal
 * @param {string} [props.badge] - Optional badge label (e.g. "New")
 * @param {number} [props.currentCount] - Current submissions count for pending status
 * @param {number} [props.targetCount] - Target submissions count
 * @param {number} [props.progressPercentage] - Progress percentage (0-100)
 * @param {string} [props.countLabel] - Label for counter (e.g. "resources", "to go live")
 */
export const SubcategoryCard = ({
    title,
    description,
    href = '#',
    topPick,
    topPickVotes,
    commentsThisWeek,
    resourceCount,
    isPending = false,
    badge,
    currentCount,
    targetCount,
    progressPercentage,
    countLabel,
}) => (
    <Card
        as="a"
        lift
        href={href}
        className={`${styles.card} ${isPending ? styles.pending : ''}`}
    >
        <div className={styles.main}>
            <div className={styles.titleRow}>
                <span className={styles.title}>{title}</span>
                {badge && <Badge type="new">{badge}</Badge>}
            </div>

            <p className={styles.blurb}>{description}</p>

            {isPending ? (
                <div className={styles.progressBar} aria-hidden="true">
                    <div
                        className={styles.progressFill}
                        style={{ width: `${progressPercentage ?? 0}%` }}
                    />
                </div>
            ) : (
                <div className={styles.meta}>
                    {topPick && (
                        <span className={styles.topPick}>
                            Top pick: {topPick} · {topPickVotes} votes
                        </span>
                    )}
                    {commentsThisWeek !== undefined && (
                        <span className={styles.comments}>
                            {commentsThisWeek} comment{commentsThisWeek === 1 ? '' : 's'} this week
                        </span>
                    )}
                </div>
            )}
        </div>

        <div className={styles.counter}>
            {isPending ? (
                <>
                    <div className={styles.countNumber}>
                        {currentCount}
                        <span className={styles.targetCount}>/{targetCount}</span>
                    </div>
                    <div className={styles.countLabel}>{countLabel || 'to go live'}</div>
                </>
            ) : (
                <>
                    <div className={styles.countNumber}>{resourceCount}</div>
                    <div className={styles.countLabel}>{countLabel || 'resources'}</div>
                </>
            )}
        </div>
    </Card>
);

export default SubcategoryCard;
