import styles from './VoteControl.module.less';

/**
 * Vertical vote control with up/down arrows and count.
 * @param {number} count - Current vote count
 */
export const VoteControl = ({ count }) => (
    <div className={styles.vote}>
        <button className={styles.up} type="button" aria-label="Upvote">▲</button>
        <div className={styles.count}>{count}</div>
        <button className={styles.down} type="button" aria-label="Downvote">▼</button>
    </div>
);
