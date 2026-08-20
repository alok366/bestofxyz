import React, { useState, useEffect } from 'react';
import { CategoryDirectoryGrid } from '@widgets/category-directory';
import { bestofxyz } from '@shared/api/bestofxyz';
import { CATEGORY_DIRECTORY_HEADER } from '../model/mockData';
import styles from './AllCategoriesPage.module.less';

/**
 * AllCategoriesPage — directory page displaying all curated categories
 * and their community-ranked subcategory lists.
 */
export const AllCategoriesPage = () => {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        setLoading(true);
        bestofxyz
            .get('/categories')
            .then((data) => {
                const mapped = (Array.isArray(data) ? data : []).map((cat) => ({
                    ...cat,
                    subcategories: cat.categories || cat.subcategories || [],
                }));
                setCategories(mapped);
                setLoading(false);
            })
            .catch((err) => {
                setError(err);
                setLoading(false);
            });
    }, []);

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.header}>
                    <div className={styles.eyebrow}>{CATEGORY_DIRECTORY_HEADER.eyebrow}</div>
                    <h1 className={styles.title}>
                        {CATEGORY_DIRECTORY_HEADER.title.split('\n').map((line, idx) => (
                            <React.Fragment key={idx}>
                                {idx > 0 && <br />}
                                {line}
                            </React.Fragment>
                        ))}
                    </h1>
                    <p className={styles.description}>{CATEGORY_DIRECTORY_HEADER.description}</p>
                </header>

                <section className={styles.section}>
                    {loading ? (
                        <div style={{ textAlign: 'center', padding: '40px 0', color: 'var(--text-secondary, #888)' }}>
                            Loading categories…
                        </div>
                    ) : error ? (
                        <div style={{ textAlign: 'center', padding: '40px 0', color: '#e53e3e' }}>
                            Failed to load categories. Please try again.
                        </div>
                    ) : (
                        <CategoryDirectoryGrid categories={categories} />
                    )}
                </section>

                <footer className={styles.backNav}>
                    <a href="/" className={styles.backLink}>
                        ← Back to home
                    </a>
                </footer>
            </div>
        </div>
    );
};

export default AllCategoriesPage;
