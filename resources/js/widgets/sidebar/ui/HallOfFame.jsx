import { SidebarCard } from './SidebarCard';
import styles from './HallOfFame.module.less';

const ITEMS = [
    { name: 'Clean Code', label: 'Top book in Programming' },
    { name: 'CS50', label: 'Top video course overall' },
    { name: 'Exercism', label: 'Top interactive learning platform' },
    { name: 'System Design Primer', label: 'Top free system design resource' },
];

export const HallOfFame = () => (
    <SidebarCard title="🏅 Hall of fame">
        <div className={styles.list}>
            {ITEMS.map((item) => (
                <div className={styles.item} key={item.name}>
                    <strong>{item.name}</strong>
                    <small>{item.label}</small>
                </div>
            ))}
        </div>
    </SidebarCard>
);
