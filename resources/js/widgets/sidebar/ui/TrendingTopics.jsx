import { SidebarCard } from './SidebarCard';
import styles from './TrendingTopics.module.less';

const TOPICS = [
    { icon: '🚀', name: 'Learn Rust', votes: '+128 new votes today' },
    { icon: '🤖', name: 'Best local LLM', votes: '+96 new votes today' },
    { icon: '⚛️', name: 'React roadmap', votes: '+74 new votes today' },
    { icon: '🗄️', name: 'PostgreSQL performance', votes: '+51 new votes today' },
];

export const TrendingTopics = () => (
    <SidebarCard title="🔥 Trending topics">
        <div className={styles.list}>
            {TOPICS.map((topic, i) => (
                <a className={styles.item} href="#" key={topic.name} style={{ animationDelay: `${i * 0.08}s` }}>
                    <span className={styles.icon}>{topic.icon}</span>
                    <div>
                        <strong>{topic.name}</strong>
                        <br />
                        <span className={styles.votes}>{topic.votes}</span>
                    </div>
                </a>
            ))}
        </div>
    </SidebarCard>
);
