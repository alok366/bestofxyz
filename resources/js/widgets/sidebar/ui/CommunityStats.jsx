import React, { useState, useEffect, useRef } from 'react';
import { SidebarCard } from './SidebarCard';
import styles from './CommunityStats.module.less';

const STATS = [
    { value: 12481, label: 'Resources submitted' },
    { value: 48902, label: 'Votes cast this month' },
    { value: 6314, label: 'Active discussions' },
    { value: 3842, label: 'Contributors' },
];

/**
 * Animated counter that counts up from 0 to target value.
 */
function AnimatedCount({ target }) {
    const [count, setCount] = useState(0);
    const ref = useRef(null);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;
                observer.disconnect();

                const duration = 1200;
                const start = performance.now();

                function tick(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    // ease-out cubic
                    const eased = 1 - Math.pow(1 - progress, 3);
                    setCount(Math.round(eased * target));
                    if (progress < 1) requestAnimationFrame(tick);
                }

                requestAnimationFrame(tick);
            },
            { threshold: 0.3 }
        );

        if (ref.current) observer.observe(ref.current);
        return () => observer.disconnect();
    }, [target]);

    return <span ref={ref}>{count.toLocaleString()}</span>;
}

export const CommunityStats = () => (
    <SidebarCard title="📊 Community">
        <div className={styles.list}>
            {STATS.map((stat) => (
                <div className={styles.item} key={stat.label}>
                    <strong><AnimatedCount target={stat.value} /></strong>
                    <small>{stat.label}</small>
                </div>
            ))}
        </div>
    </SidebarCard>
);
