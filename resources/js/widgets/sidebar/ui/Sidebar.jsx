import { TrendingTopics } from './TrendingTopics';
import { HallOfFame } from './HallOfFame';
import { CommunityStats } from './CommunityStats';
import { SubmitCTA } from './SubmitCTA';

/**
 * Right sidebar — composed of stacked sidebar cards.
 */
export const Sidebar = () => (
    <aside>
        <TrendingTopics />
        <HallOfFame />
        <CommunityStats />
        <SubmitCTA />
    </aside>
);
