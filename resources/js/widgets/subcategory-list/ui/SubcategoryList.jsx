import React from 'react';
import { SubcategoryCard } from './SubcategoryCard';
import styles from './SubcategoryList.module.less';

/**
 * SubcategoryList — stacked list of subcategory cards.
 *
 * @param {object} props
 * @param {Array<object>} props.items - Array of subcategory data objects
 */
export const SubcategoryList = ({ items = [] }) => (
    <div className={styles.list}>
        {items.map((item) => (
            <SubcategoryCard key={item.id || item.title} {...item} />
        ))}
    </div>
);

export default SubcategoryList;
