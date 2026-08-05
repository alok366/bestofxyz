import { Card } from '@shared/ui';
import styles from './SidebarCard.module.less';

/**
 * Reusable glass-panel card wrapper for sidebar widgets.
 */
export const SidebarCard = ({ title, children }) => (
    <Card className={styles.card}>
        {title && <Card.Header className={styles.title}>{title}</Card.Header>}
        <Card.Body>{children}</Card.Body>
    </Card>
);
