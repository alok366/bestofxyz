import styles from './Hero.module.less';

/**
 * Hero banner — large heading + subtitle for the homepage.
 * Includes a subtle gradient glow and fade-in-up animation on mount.
 */
export const Hero = () => (
    <section className={styles.hero}>
        <div className={styles.container}>
            <h1 className={styles.heading}>
                Find the <span className={styles.accent}>best resource</span> for anything
            </h1>
            <p className={styles.subtitle}>
                Community-curated rankings of books, courses, tutorials, tools, and products.
                Vote, discuss, and discover what developers actually recommend.
            </p>
        </div>
    </section>
);
