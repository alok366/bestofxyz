import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { SubmitResourceForm } from '@features/submit-resource';
import { bestofxyz } from '@shared/api/bestofxyz';
import styles from './SubmitResourcePage.module.less';

const PAGE_META = {
    eyebrow: 'New submission',
    title: 'Add a resource.',
    description:
        'Point people at something worth their time. It goes straight into the right "best of" list — existing or brand new.',
};

/**
 * SubmitResourcePage — form page allowing users to submit new resources
 * into existing categories or propose brand new categories.
 */
export const SubmitResourcePage = () => {
    const [categories, setCategories] = useState([]);
    const [availableTags, setAvailableTags] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            bestofxyz.get('/categories').catch(() => []),
            bestofxyz.get('/tags').catch(() => []),
        ]).then(([cats, tags]) => {
            if (Array.isArray(cats)) setCategories(cats);
            if (Array.isArray(tags)) setAvailableTags(tags);
            setLoading(false);
        });
    }, []);

    const domains = categories.map((c) => c.title);
    const existingCategories = categories.flatMap((c) =>
        (c.categories || c.subcategories || []).map((s) => s.name)
    );
    const defaultTags = availableTags.slice(0, 2);

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <div className={styles.eyebrow}>{PAGE_META.eyebrow}</div>
                    <h1 className={styles.title}>{PAGE_META.title}</h1>
                    <p className={styles.description}>{PAGE_META.description}</p>
                </header>

                <section className={styles.formSection}>
                    {loading ? (
                        <div style={{ textAlign: 'center', padding: '60px 0', color: 'var(--text-secondary, #888)' }}>
                            Loading form…
                        </div>
                    ) : (
                        <SubmitResourceForm
                            domains={
                                domains.length > 0
                                    ? domains
                                    : ['Programming', 'Design Tools', 'Data Science', 'Productivity', 'Marketing', 'No-Code Tools']
                            }
                            existingCategories={existingCategories}
                            defaultTags={defaultTags.length > 0 ? defaultTags : ['free', 'official']}
                        />
                    )}
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

export default SubmitResourcePage;

