import { Card, Avatar } from '@shared/ui';
import styles from './DiscussionCard.module.less';

/**
 * Discussion card — a community post with avatar, body, and engagement meta.
 */
export const DiscussionCard = ({ initials, username, timeAgo, body, upvotes, replies, tag }) => (
    <Card className={styles.discussion}>
        <Card.Header className={styles.top}>
            <Avatar initials={initials} />
            <div>
                <strong>{username}</strong>
                <br />
                <span className={styles.time}>{timeAgo}</span>
            </div>
        </Card.Header>

        <Card.Body>
            <p className={styles.body}>{body}</p>
        </Card.Body>

        <Card.Footer className={styles.meta}>
            <span>▲ {upvotes}</span>
            <span>💬 {replies} replies</span>
            <span>🏷 {tag}</span>
        </Card.Footer>
    </Card>
);
