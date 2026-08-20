import React, { useState, useEffect } from 'react';
import { Link, useParams } from 'react-router-dom';
import { RankedResourceCard } from '@widgets/resource-card';
import { bestofxyz } from '@shared/api/bestofxyz';
import styles from './CategoryPage.module.less';

const SORT_OPTIONS = ['Top', 'New', 'Rising'];

/**
 * CategoryPage — renders category direct ranking view with breadcrumb,
 * description, sort/tag filters, and community-ranked resource cards.
 */
export const CategoryPage = () => {
    const { slug } = useParams();
    const [category, setCategory] = useState(null);
    const [tags, setTags] = useState([]);
    const [selectedSort, setSelectedSort] = useState('Top');
    const [selectedTag, setSelectedTag] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        bestofxyz
            .get('/tags')
            .then((data) => {
                if (Array.isArray(data)) setTags(data);
            })
            .catch(() => {});
    }, []);

    useEffect(() => {
        if (!slug) return;
        setLoading(true);
        setError(null);
        bestofxyz
            .get(`/categories/${slug}`, {
                sort: selectedSort.toLowerCase(),
                tag: selectedTag || undefined,
            })
            .then((data) => {
                setCategory(data);
                setLoading(false);
            })
            .catch((err) => {
                setError(err);
                setLoading(false);
            });
    }, [slug, selectedSort, selectedTag]);

    if (loading && !category) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: 'var(--text-secondary, #888)' }}>
                        Loading category…
                    </div>
                </div>
            </div>
        );
    }

    if (error && !category) {
        return (
            <div className={styles.page}>
                <div className={styles.container}>
                    <div style={{ textAlign: 'center', padding: '60px 0', color: '#e53e3e' }}>
                        Category not found or failed to load.
                    </div>
                    <footer className={styles.backNav}>
                        <Link to="/categories" className={styles.backLink}>
                            ← Back to all categories
                        </Link>
                    </footer>
                </div>
            </div>
        );
    }

    const breadcrumbs = [
        { label: 'Categories', path: '/categories' },
        ...(category?.group ? [{ label: category.group, path: '/categories' }] : []),
        { label: category?.name || slug, path: `/categories/${slug}` },
    ];

    const resources = category?.resources || [];

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <nav className={styles.breadcrumb} aria-label="Breadcrumb">
                        {breadcrumbs.map((crumb, idx) => (
                            <React.Fragment key={crumb.path + idx}>
                                {idx > 0 && <span className={styles.separator}>/</span>}
                                {idx === breadcrumbs.length - 1 ? (
                                    <span className={styles.current}>{crumb.label}</span>
                                ) : (
                                    <Link to={crumb.path} className={styles.crumbLink}>
                                        {crumb.label}
                                    </Link>
                                )}
                            </React.Fragment>
                        ))}
                    </nav>

                    <h1 className={styles.title}>{category?.name}</h1>
                    <p className={styles.description}>{category?.description}</p>
                    <div className={styles.stats}>
                        {category?.resourceCount ?? resources.length} resources · re-ranked daily
                    </div>
                </header>

                <section className={styles.section}>
                    <div className={styles.filterBar}>
                        <div className={styles.sortGroup}>
                            {SORT_OPTIONS.map((sort) => (
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

                        {tags.length > 0 && (
                            <div className={styles.tagGroup}>
                                {tags.map((tag) => {
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
                        )}
                    </div>

                    <div className={styles.resourceList}>
                        {loading && (
                            <div style={{ textAlign: 'center', padding: '20px 0', color: 'var(--text-secondary, #888)' }}>
                                Updating…
                            </div>
                        )}
                        {!loading && resources.length === 0 && (
                            <div style={{ textAlign: 'center', padding: '40px 0', color: 'var(--text-secondary, #888)' }}>
                                No resources found in this category yet.
                            </div>
                        )}
                        {resources.map((res) => (
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

