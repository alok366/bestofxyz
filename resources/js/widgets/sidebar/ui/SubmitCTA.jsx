import { Button } from '@shared/ui';
import { SidebarCard } from './SidebarCard';
import styles from './SubmitCTA.module.less';

export const SubmitCTA = () => (
    <SidebarCard title="🚀 Submit a resource">
        <p className={styles.desc}>
            Share a book, course, tutorial, or tool that helped you.
            The community will vote and discuss it.
        </p>
        <Button variant="primary" className={styles.btn}>
            Submit resource
        </Button>
    </SidebarCard>
);
