import React from 'react';
import { Link } from 'react-router-dom';
import { SubmitResourceForm } from '@features/submit-resource';
import { MOCK_SUBMIT_DATA } from '../model/mockData';
import styles from './SubmitResourcePage.module.less';

/**
 * SubmitResourcePage — form page allowing users to submit new resources
 * into existing categories or propose brand new categories.
 */
export const SubmitResourcePage = () => {
    const data = MOCK_SUBMIT_DATA;

    return (
        <div className={styles.page}>
            <div className={styles.container}>
                <header className={styles.pageHead}>
                    <div className={styles.eyebrow}>{data.eyebrow}</div>
                    <h1 className={styles.title}>{data.title}</h1>
                    <p className={styles.description}>{data.description}</p>
                </header>

                <section className={styles.formSection}>
                    <SubmitResourceForm
                        domains={data.domains}
                        existingCategories={data.existingCategories}
                        defaultTags={data.defaultTags}
                    />
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
