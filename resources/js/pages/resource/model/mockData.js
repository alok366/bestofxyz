/**
 * Resource Detail Page Mock Data
 *
 * Sourced from bestofxyz-experiment-pages/resource-detail.html
 * Maintained in model fixture file per FSD architecture.
 */

export const MOCK_RESOURCE_DETAIL = {
    id: 'rust-book',
    title: 'The Rust Book',
    host: 'doc.rust-lang.org',
    hostUrl: 'https://doc.rust-lang.org/book/',
    rankBadge: '#1 in Best Rust Courses',
    category: 'Programming',
    categoryPath: '/category/programming',
    topic: 'Best Rust Courses',
    topicPath: '/category/programming',
    breadcrumb: [
        { label: 'Categories', path: '/categories' },
        { label: 'Programming', path: '/category/programming' },
        { label: 'Best Rust Courses', path: '/category/programming' },
        { label: 'The Rust Book', path: '/resource/rust-book' },
    ],
    votes: 214,
    description:
        'The definitive guide to Rust, written and maintained by the Rust team itself. Starts from zero and builds up through ownership, lifetimes, and the trickier parts of the type system, with runnable examples throughout.',
    tags: ['free', 'official', 'comprehensive'],
    submitter: 'kdev',
    submittedTimeAgo: '4 months ago',
    commentsCount: 38,
    commentsSortOptions: ['Top', 'New'],
    currentUser: {
        initials: 'Y',
        name: 'You',
    },
    comments: [
        {
            id: 'c1',
            author: 'ana_b',
            timeAgo: '3 weeks ago',
            votes: 12,
            body: 'Genuinely the best intro out there — the ownership chapter is where it finally clicked for me. Read three other tutorials before this one and none of them made it stick.',
            replies: [
                {
                    id: 'c1-1',
                    author: 'kdev',
                    timeAgo: '3 weeks ago',
                    votes: 4,
                    body: 'Same, chapter 4 did it for me too. Ended up re-reading it twice before it actually stuck.',
                    replies: [],
                },
            ],
        },
        {
            id: 'c2',
            author: 'throwaway99',
            timeAgo: '1 month ago',
            votes: 3,
            body: "Good, but chapter 8 (collections) assumes you already know a fair bit. Wouldn't call it a true zero-to-hero path on its own.",
            replies: [],
        },
        {
            id: 'c3',
            author: 'x',
            timeAgo: '2 months ago',
            votes: -2,
            body: 'meh',
            replies: [],
        },
        {
            id: 'c4',
            author: 'priya_k',
            timeAgo: '2 minutes ago',
            votes: 0,
            body: 'Just started this today, wish me luck!',
            replies: [],
        },
    ],
};
