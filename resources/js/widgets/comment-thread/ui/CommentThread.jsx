import React, { useState } from 'react';
import { CommentComposer } from './CommentComposer';
import { CommentItem } from './CommentItem';
import styles from './CommentThread.module.less';

/**
 * CommentThread — full threaded discussion block with sort filters,
 * interactive comment composer, and list of recursive comments.
 *
 * @param {object} props
 * @param {Array<object>} props.comments - List of comments
 * @param {number} [props.commentsCount] - Total comment count
 * @param {string[]} [props.sortOptions=['Top', 'New']] - Sort options
 * @param {{ initials: string, name: string }} [props.currentUser] - Current user info
 */
export const CommentThread = ({
    comments: initialComments = [],
    commentsCount,
    sortOptions = ['Top', 'New'],
    currentUser = { initials: 'Y', name: 'You' },
}) => {
    const [selectedSort, setSelectedSort] = useState(sortOptions[0] || 'Top');
    const [comments, setComments] = useState(initialComments);

    React.useEffect(() => {
        setComments(initialComments);
    }, [initialComments]);

    const handleAddComment = (bodyText) => {
        const newComment = {
            id: `c-new-${Date.now()}`,
            author: currentUser.name || 'You',
            timeAgo: 'Just now',
            votes: 1,
            body: bodyText,
            replies: [],
        };
        setComments([newComment, ...comments]);
    };

    const count = commentsCount ?? comments.length;

    return (
        <div className={styles.thread}>
            <div className={styles.headerRow}>
                <h2 className={styles.title}>{count} comments</h2>
                <div className={styles.sortTabs}>
                    {sortOptions.map((sort) => (
                        <button
                            key={sort}
                            type="button"
                            className={`${styles.sortTab} ${
                                selectedSort === sort ? styles.activeSort : ''
                            }`}
                            onClick={() => setSelectedSort(sort)}
                        >
                            {sort}
                        </button>
                    ))}
                </div>
            </div>

            <CommentComposer
                userInitials={currentUser.initials}
                onCommentSubmit={handleAddComment}
            />

            <div className={styles.commentList}>
                {comments.map((comment) => (
                    <CommentItem key={comment.id} {...comment} />
                ))}
            </div>
        </div>
    );
};

export default CommentThread;
