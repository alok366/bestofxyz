import React from 'react';
import { CategoryDirectoryGrid } from '@widgets/category-directory';
import { CATEGORY_BOARDS, CATEGORY_DIRECTORY_HEADER } from '../model/mockData';
import styles from './AllCategoriesPage.module.less';

/**
 * AllCategoriesPage — directory page displaying all curated categories
 * and their community-ranked subcategory lists.
 */
export const AllCategoriesPage = () => (
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
                <CategoryDirectoryGrid categories={CATEGORY_BOARDS} />
            </section>

            <footer className={styles.backNav}>
                <a href="/" className={styles.backLink}>
                    ← Back to home
                </a>
            </footer>
        </div>
    </div>
);

export default AllCategoriesPage;
