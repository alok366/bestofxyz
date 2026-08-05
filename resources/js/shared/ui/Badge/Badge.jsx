import styles from './Badge.module.less';

/**
 * Type-colored pill badge for resource categories.
 * @param {'book'|'video'|'interactive'|'tool'} type
 */
const TYPE_ICONS = {
    book: '📚',
    video: '🎥',
    interactive: '💻',
    tool: '🔧',
};

export const Badge = ({ type = 'book', children }) => {
    const cls = [styles.badge, styles[type]].filter(Boolean).join(' ');
    return (
        <span className={cls}>
            {TYPE_ICONS[type] && <span aria-hidden="true">{TYPE_ICONS[type]}</span>} {children}
        </span>
    );
};
