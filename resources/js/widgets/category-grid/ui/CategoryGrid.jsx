import { CategoryCard } from './CategoryCard';
import styles from './CategoryGrid.module.less';

/**
 * Responsive grid of category cards.
 * @param {{ icon: string, name: string, description: string, count?: number }[]} categories
 */
export const CategoryGrid = ({ categories }) => (
    <div className={styles.grid}>
        {categories.map((cat) => (
            <CategoryCard key={cat.name} {...cat} />
        ))}
    </div>
);
