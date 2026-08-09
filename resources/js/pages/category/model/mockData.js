/**
 * Category Page Mock Data
 *
 * Sourced from bestofxyz-experiment-pages/category-page.html
 * Model fixture maintained separately per FSD rules.
 */

export const MOCK_CATEGORY_DETAIL = {
    id: 'programming',
    title: 'Programming',
    breadcrumb: [
        { label: 'Categories', path: '/categories' },
        { label: 'Programming', path: '/category/programming' },
    ],
    description:
        'Languages, frameworks, editors and everything else developers argue about. 12 subcategories, ranked and re-ranked by the people who use them daily.',
    stats: '12 subcategories · 340 resources · 28 added this week',
    subcategories: [
        {
            id: 'rust-courses',
            title: 'Best Rust Courses',
            description: 'Structured courses and books for learning Rust from the ground up.',
            topPick: 'The Rust Book',
            topPickVotes: 214,
            commentsThisWeek: 6,
            resourceCount: 24,
            href: '#',
        },
        {
            id: 'js-frameworks',
            title: 'Best JavaScript Frameworks',
            description: 'Frontend frameworks and meta-frameworks, from the well-worn to the new and loud.',
            topPick: 'React',
            topPickVotes: 402,
            commentsThisWeek: 19,
            resourceCount: 31,
            href: '#',
        },
        {
            id: 'python-ides',
            title: 'Best Python IDEs',
            description: 'Editors and IDEs people actually keep open all day for Python work.',
            topPick: 'PyCharm',
            topPickVotes: 178,
            commentsThisWeek: 3,
            resourceCount: 15,
            href: '#',
        },
        {
            id: 'git-gui-clients',
            title: 'Best Git GUI Clients',
            description: "For when the terminal isn't the point — visual tools for branches, diffs and history.",
            topPick: 'GitKraken',
            topPickVotes: 96,
            commentsThisWeek: 1,
            resourceCount: 9,
            href: '#',
        },
        {
            id: 'rust-courses-beginners',
            title: 'Best Rust Courses for Beginners',
            badge: 'New',
            description: 'Proposed as a narrower split from Best Rust Courses, for people with zero prior experience.',
            isPending: true,
            currentCount: 2,
            targetCount: 5,
            progressPercentage: 40,
            countLabel: 'to go live',
            href: '#',
        },
    ],
};
