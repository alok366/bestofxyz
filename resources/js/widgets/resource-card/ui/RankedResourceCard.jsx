import React from 'react';
import { Card } from '@shared/ui';
import { VoteControl } from './VoteControl';
import styles from './RankedResourceCard.module.less';

/**
 * RankedResourceCard — displays a single ranked resource in category listing
 * with rank number, movement delta, vote control, host tag, tag badges, and discussion meta.
 *
 * @param {object} props
 * @param {number} props.rank - Numeric rank position (1, 2, 3...)
 * @param {{ type: 'up'|'down'|'flat', label: string }} [props.delta] - Rank delta indicator
 * @param {number} props.votes - Current vote count
 * @param {string} props.title - Resource title
 * @param {string} [props.host] - Domain/host tag (e.g. "doc.rust-lang.org")
 * @param {string} props.description - Summary description
 * @param {string[]} [props.tags] - Array of tag labels
 * @param {string} [props.submitter] - Username of submitter
 * @param {string} [props.submitterTime] - Relative submission time
 * @param {number} [props.commentsCount=0] - Number of comments
 * @param {string} [props.href='#'] - Link to resource detail
 */
export const RankedResourceCard = ({
    rank,
    delta = { type: 'flat', label: '—' },
    votes,
    title,
    host,
    description,
    tags = [],
    submitter,
    submitterTime,
    commentsCount = 0,
    href = '#',
}) => {
    const rankClass = rank === 1 ? styles.rank1 : rank === 2 ? styles.rank2 : rank === 3 ? styles.rank3 : '';
    const deltaClass = delta.type === 'up' ? styles.deltaUp : delta.type === 'down' ? styles.deltaDown : styles.deltaFlat;

    return (
        <Card as="article" lift className={styles.card}>
            <div className={`${styles.rankStack} ${rankClass}`}>
                <div className={styles.rankNumber}>{rank}</div>
                <div className={`${styles.rankDelta} ${deltaClass}`}>{delta.label}</div>
            </div>

            <div className={styles.voteWrapper}>
                <VoteControl count={votes} />
            </div>

            <div className={styles.main}>
                <div className={styles.titleRow}>
                    <a href={href} className={styles.title}>
                        {title}
                    </a>
                    {host && <span className={styles.host}>{host}</span>}
                </div>

                <p className={styles.blurb}>{description}</p>

                {tags.length > 0 && (
                    <div className={styles.tagRow}>
                        {tags.map((tag) => (
                            <span key={tag} className={styles.tagChip}>
                                {tag}
                            </span>
                        ))}
                    </div>
                )}

                <div className={styles.meta}>
                    {submitter && (
                        <span className={styles.submitter}>
                            submitted {submitterTime ? `${submitterTime} ` : ''}by{' '}
                            <span className={styles.submitterName}>{submitter}</span>
                        </span>
                    )}
                    <a
                        href={href}
                        className={`${styles.comments} ${commentsCount === 0 ? styles.faint : ''}`}
                    >
                        {commentsCount === 0
                            ? 'no comments yet'
                            : `${commentsCount} comment${commentsCount === 1 ? '' : 's'}`}
                    </a>
                </div>
            </div>
        </Card>
    );
};

export default RankedResourceCard;
