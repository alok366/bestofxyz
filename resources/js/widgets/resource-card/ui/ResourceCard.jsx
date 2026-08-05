import { Card, Badge, Button } from '@shared/ui';
import { VoteControl } from './VoteControl';
import styles from './ResourceCard.module.less';

/**
 * Resource card — displays a single ranked resource with vote control,
 * metadata badges, and action buttons.
 */
export const ResourceCard = ({
    title,
    description,
    type = 'book',
    typeLabel,
    rating,
    discussions,
    voters,
    votes,
    rank,
}) => (
    <Card as="article" lift className={styles.card}>
        <VoteControl count={votes} />

        <Card.Body className={styles.body}>
            <div className={styles.title}>{title}</div>
            <div className={styles.desc}>{description}</div>

            <div className={styles.meta}>
                <Badge type={type}>{typeLabel || type}</Badge>
                <span>⭐ {rating}/10</span>
                <span>💬 {discussions} discussions</span>
                <span>👤 {voters} voters</span>
            </div>
        </Card.Body>

        <div className={styles.actions}>
            <div className={styles.rank}>{rank}</div>
            <Button>View Discussion</Button>
        </div>
    </Card>
);
