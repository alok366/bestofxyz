import styles from './SectionHeader.module.less';

/**
 * Section title with optional "View all" link.
 * @param {string} title
 * @param {string} [linkText]
 * @param {string} [linkHref]
 */
export const SectionHeader = ({ title, linkText, linkHref = '#' }) => (
    <div className={styles.header}>
        <div className={styles.title}>{title}</div>
        {linkText && (
            <a className={styles.link} href={linkHref}>
                {linkText}
            </a>
        )}
    </div>
);
