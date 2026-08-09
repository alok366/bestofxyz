import React from 'react';
import { CategoryDirectoryCard } from './CategoryDirectoryCard';
import styles from './CategoryDirectoryGrid.module.less';

/**
 * CategoryDirectoryGrid — responsive 2-column grid rendering directory category cards.
 *
 * @param {object} props
 * @param {Array<object>} props.categories - Array of category board objects
 */
export const CategoryDirectoryGrid = ({ categories = [] }) => (
    <div className={styles.grid}>
        {categories.map((category) => (
            <CategoryDirectoryCard key={category.id || category.title} {...category} />
        ))}
    </div>
);

export default CategoryDirectoryGrid;
