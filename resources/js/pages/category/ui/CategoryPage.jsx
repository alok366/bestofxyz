import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@shared/ui';
import { SubcategoryList } from '@widgets/subcategory-list';
import { MOCK_CATEGORY_DETAIL } from '../model/mockData';
import styles from './CategoryPage.module.less';

/**
 * CategoryPage — renders category detail view with breadcrumb, description,
 * community stats, and community-ranked subcategories.
 */
export const CategoryPage = () => {
    const category = MOCK_CATEGORY_DETAIL;

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
                    <div className={styles.sectionHeader}>
                        <h2 className={styles.sectionTitle}>Subcategories</h2>
                        <Button variant="default" className={styles.proposeBtn}>
                            + Propose a subcategory
                        </Button>
                    </div>

                    <SubcategoryList items={category.subcategories} />
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
