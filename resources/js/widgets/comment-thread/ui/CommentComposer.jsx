import React, { useState } from 'react';
import { Avatar, Button } from '@shared/ui';
import styles from './CommentComposer.module.less';

/**
 * CommentComposer — inline message input box with user avatar,
 * multiline textarea, guidelines hint, and submit button.
 *
 * @param {object} props
 * @param {string} [props.userInitials='Y'] - Initials for the logged-in user avatar
 * @param {function} [props.onCommentSubmit] - Callback when comment is posted
 */
export const CommentComposer = ({ userInitials = 'Y', onCommentSubmit }) => {
    const [text, setText] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!text.trim()) return;
        if (onCommentSubmit) {
            onCommentSubmit(text.trim());
        }
        setText('');
    };

    return (
        <form className={styles.composer} onSubmit={handleSubmit}>
            <div className={styles.avatarWrapper}>
                <Avatar initials={userInitials} size={36} />
            </div>

            <div className={styles.inputArea}>
                <textarea
                    className={styles.textarea}
                    placeholder="Share your experience with this resource…"
                    rows={3}
                    value={text}
                    onChange={(e) => setText(e.target.value)}
                />

                <div className={styles.footer}>
                    <span className={styles.hint}>Be specific — what worked, what didn't.</span>
                    <Button variant="primary" type="submit" className={styles.submitBtn}>
                        Post comment
                    </Button>
                </div>
            </div>
        </form>
    );
};

export default CommentComposer;
