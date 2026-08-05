import React from 'react';
import { SectionHeader } from '@shared/ui';
import { RESOURCES } from '@entities/resource';
import { DISCUSSIONS } from '@entities/discussion';
import { CATEGORIES } from '@entities/category';
import { Hero } from '@widgets/hero';
import { ResourceCard } from '@widgets/resource-card';
import { DiscussionCard } from '@widgets/discussion-card';
import { CategoryGrid } from '@widgets/category-grid';
import { Sidebar } from '@widgets/sidebar';
import styles from './HomePage.module.less';

/* ── Component ──────────────────────────────────────────────── */

const HomePage = () => (
    <>
        <Hero />

        <div className={styles.container}>
            <main className={styles.main}>
                {/* Left column — main content */}
                <section>
                    {/* Top resources */}
                    <div className={styles.section}>
                        <SectionHeader
                            title={'🏆 Top resources for "Learn C Programming"'}
                            linkText="View all →"
                            linkHref="#"
                        />
                        {RESOURCES.map((r) => (
                            <ResourceCard key={r.title} {...r} />
                        ))}
                    </div>

                    {/* Hot discussions */}
                    <div className={styles.section}>
                        <SectionHeader
                            title="💬 Hot discussions"
                            linkText="Browse all →"
                            linkHref="#"
                        />
                        {DISCUSSIONS.map((d) => (
                            <DiscussionCard key={d.username} {...d} />
                        ))}
                    </div>

                    {/* Explore categories */}
                    <div className={styles.section}>
                        <SectionHeader title="📂 Explore categories" />
                        <CategoryGrid categories={CATEGORIES} />
                    </div>
                </section>

                {/* Right sidebar */}
                <Sidebar />
            </main>
        </div>
    </>
);

export { HomePage };
export default HomePage;
