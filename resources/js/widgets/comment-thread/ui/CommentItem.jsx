import React, { useState } from 'react';
import styles from './CommentItem.module.less';

/**
 * CommentItem — renders a single threaded discussion comment with vote counter,
 * author metadata, response text, and recursive replies support.
 *
 * @param {object} props
 * @param {string} props.id - Comment ID
 * @param {string} props.author - Comment author username
 * @param {string} props.timeAgo - Relative timestamp
 * @param {number} props.votes - Current vote score
 * @param {string} props.body - Comment message content
 * @param {Array<object>} [props.replies=[]] - Nested reply comments
 * @param {boolean} [props.isReply=false] - Whether this comment is indented as a reply
 */
export const CommentItem = ({
    id,
    author,
    timeAgo,
    votes: initialVotes,
    body,
    replies = [],
    isReply = false,
}) => {
    const [voteCount, setVoteCount] = useState(initialVotes);
    const [userVote, setUserVote] = useState(null); // 'up' | 'down' | null

    const handleVote = (direction) => {
        if (userVote === direction) {
            setUserVote(null);
            setVoteCount(initialVotes);
        } else {
            setUserVote(direction);
            setVoteCount(direction === 'up' ? initialVotes + 1 : initialVotes - 1);
        }
    };

    return (
        <div className={`${styles.commentWrapper} ${isReply ? styles.isReply : ''}`}>
            <div id={id ? `comment-${id}` : undefined} className={styles.comment}>
                <div className={styles.voteControl}>
                    <button
                        type="button"
                        className={`${styles.voteBtn} ${userVote === 'up' ? styles.votedUp : ''}`}
                        aria-label="Upvote"
                        onClick={() => handleVote('up')}
                    >
                        ▲
                    </button>
                    <span
                        className={`${styles.voteScore} ${
                            voteCount < 0 ? styles.negativeScore : ''
                        }`}
                    >
                        {voteCount}
                    </span>
                    <button
                        type="button"
                        className={`${styles.voteBtn} ${userVote === 'down' ? styles.votedDown : ''}`}
                        aria-label="Downvote"
                        onClick={() => handleVote('down')}
                    >
                        ▼
                    </button>
                </div>

                <div className={styles.content}>
                    <div className={styles.header}>
                        <span className={styles.author}>{author}</span>
                        <span className={styles.time}>{timeAgo}</span>
                    </div>

                    <p className={styles.body}>{body}</p>

                    <div className={styles.actions}>
                        <button type="button" className={styles.actionBtn}>
                            Reply
                        </button>
                        <button type="button" className={styles.actionBtn}>
                            Share
                        </button>
                    </div>
                </div>
            </div>

            {replies.length > 0 && (
                <div className={styles.repliesList}>
                    {replies.map((reply) => (
                        <CommentItem key={reply.id} {...reply} isReply />
                    ))}
                </div>
            )}
        </div>
    );
};

export default CommentItem;
