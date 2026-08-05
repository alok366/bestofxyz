import styles from './Avatar.module.less';

/**
 * Circular gradient avatar with initials.
 * @param {string} initials - 1–2 character initials
 * @param {number} [size=34] - Width/height in px
 */
export const Avatar = ({ initials, size = 34 }) => (
    <div
        className={styles.avatar}
        style={{ width: size, height: size, fontSize: Math.round(size * 0.38) }}
    >
        {initials}
    </div>
);
