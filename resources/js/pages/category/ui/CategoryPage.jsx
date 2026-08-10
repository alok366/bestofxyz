import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { RankedResourceCard } from '@widgets/resource-card';
import { MOCK_CATEGORY_DETAIL } from '../model/mockData';
import styles from './CategoryPage.module.less';

/**
 * CategoryPage — renders category direct ranking view with breadcrumb,
 * description, sort/tag filters, and community-ranked resource cards.
 */
export const CategoryPage = () => {
    const category = MOCK_CATEGORY_DETAIL;
    const [selectedSort, setSelectedSort] = useState(category.sortOptions[0] || 'Top');
    const [selectedTag, setSelectedTag] = useState(null);

    const filteredResources = category.resources.filter((res) => {
        if (!selectedTag) return true;
        return res.tags.includes(selectedTag);
    });

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                        {category.breadcrumb.map((crumb, idx) => (
                            <React.Fragment key={crumb.path}>
                                {idx > 0 && <span className={styles.separator}>/</span>}
                                {idx === category.breadcrumb.length - 1 ? (
                                    <span className={styles.current}>{crumb.label}</span>
                                ) : (
                                    <Link to={crumb.path} className={styles.crumbLink}>
                                        {crumb.label}
                                    </Link>
                                )}
                            </React.Fragment>
                        ))}
                    </nav>

                    <h1 className={styles.title}>{category.title}</h1>
                    <p className={styles.description}>{category.description}</p>
                    <div className={styles.stats}>{category.stats}</div>
                </header>

                <section className={styles.section}>
                    <div className={styles.filterBar}>
                        <div className={styles.sortGroup}>
                            {category.sortOptions.map((sort) => (
                                <button
                                    key={sort}
                                    type="button"
                                    className={`${styles.sortChip} ${selectedSort === sort ? styles.activeSort : ''}`}
                                    onClick={() => setSelectedSort(sort)}
                                >
                                    {sort}
                                </button>
                            ))}
                        </div>

                        <div className={styles.tagGroup}>
                            {category.filterTags.map((tag) => {
                                const isTagActive = selectedTag === tag;
                                return (
                                    <button
                                        key={tag}
                                        type="button"
                                        className={`${styles.filterTag} ${isTagActive ? styles.activeTag : ''}`}
                                        onClick={() => setSelectedTag(isTagActive ? null : tag)}
                                    >
                                        {tag}
                                    </button>
                                );
                            })}
                        </div>
                    </div>

                    <div className={styles.resourceList}>
                        {filteredResources.map((res) => (
                            <RankedResourceCard key={res.id || res.title} {...res} />
                        ))}
                    </div>
                </section>

                <footer className={styles.backNav}>
                    <Link to="/categories" className={styles.backLink}>
                        ← Back to all categories
                    </Link>
                </footer>
            </div>
        </div>
    );
};

export default CategoryPage;
