import React from 'react';
import { Card, Badge } from '@shared/ui';
import styles from './CategoryDirectoryCard.module.less';

/**
 * CategoryDirectoryCard — composite card displaying a top-level category,
 * its subcategory count and resource totals, and its ranked "best of" sub-lists.
 *
 * @param {object} props
 * @param {string} props.title - Category title (e.g. "Programming")
 * @param {number} props.subcategoriesCount - Number of subcategories
 * @param {number} props.resourcesCount - Total resources in category
 * @param {string} [props.href='#'] - Navigation link for category
 * @param {Array<{ id: string, name: string, topResource?: string, score?: number, badge?: string, status?: string }>} props.subcategories
 */
export const CategoryDirectoryCard = ({
    title,
    subcategoriesCount,
    resourcesCount,
    href = '#',
    subcategories = [],
}) => (
    <Card lift className={styles.card}>
        <div className={styles.header}>
            <div>
                <h3 className={styles.title}>{title}</h3>
                <div className={styles.stats}>
                    {subcategoriesCount} subcategories · {resourcesCount} resources
                </div>
            </div>
            <a href={href} className={styles.openBtn}>
                Open →
            </a>
        </div>

        <div className={styles.subList}>
            {subcategories.map((sub) => (
                <div key={sub.id || sub.name} className={styles.subRow}>
                    <div className={styles.subName}>
                        <span>{sub.name}</span>
                        {sub.badge && <Badge type="new">{sub.badge}</Badge>}
                    </div>
                    <div className={styles.subMeta}>
                        {sub.topResource ? (
                            <span>
                                {sub.topResource} · <span className={styles.score}>{sub.score}</span>
                            </span>
                        ) : sub.status ? (
                            <span className={styles.status}>{sub.status}</span>
                        ) : null}
                    </div>
                </div>
            ))}
        </div>
    </Card>
);

export default CategoryDirectoryCard;
