import styles from './Badge.module.less';

/**
 * Type-colored pill badge for resource categories and status indicators.
 * @param {'book'|'video'|'interactive'|'tool'|'new'} [type='book']
 * @param {string} [className]
 * @param {import('react').ReactNode} children
 */
const TYPE_ICONS = {
    book: '📚',
    video: '🎥',
    interactive: '💻',
    tool: '🔧',
};

export const Badge = ({ type = 'book', className = '', children }) => {
    const cls = [styles.badge, styles[type], className].filter(Boolean).join(' ');
    return (
        <span className={cls}>
            {TYPE_ICONS[type] && <span aria-hidden="true">{TYPE_ICONS[type]}</span>} {children}
        </span>
    );
};
