import { Card } from '@shared/ui';
import styles from './CategoryCard.module.less';

/**
 * Single category card with gradient accent line and hover lift.
 */
export const CategoryCard = ({ icon, name, description, count }) => (
    <Card as="a" lift className={styles.card} href="#">
        <div className={styles.icon}>{icon}</div>
        <Card.Body>
            <h4 className={styles.name}>{name}</h4>
            <p className={styles.desc}>{description}</p>
            {count && <small className={styles.count}>{count} resources</small>}
        </Card.Body>
    </Card>
);
