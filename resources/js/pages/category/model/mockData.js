/**
 * Category Page Mock Data — Programming
 *
 * Direct ranked resources list for the Programming category.
 * Maintained in model fixture file per FSD architecture.
 */

export const MOCK_CATEGORY_DETAIL = {
    id: 'programming',
    title: 'Programming',
    breadcrumb: [
        { label: 'Categories', path: '/categories' },
        { label: 'Programming', path: '/category/programming' },
    ],
    description:
        'Languages, frameworks, editors, and developer tools — ranked and reviewed by the community.',
    stats: '340 resources · 12 topics · re-ranked daily',
    sortOptions: ['Top', 'New', 'Rising'],
    filterTags: ['free', 'official', 'book', 'course', 'tool', 'interactive'],
    resources: [
        {
            id: 'rust-book',
            rank: 1,
            delta: { type: 'flat', label: '—' },
            votes: 402,
            title: 'The Rust Book',
            host: 'doc.rust-lang.org',
            description:
                'The official and definitive guide to the Rust programming language, written and maintained by the Rust team.',
            tags: ['free', 'official', 'book'],
            submitter: 'kdev',
            commentsCount: 38,
            href: '#',
        },
        {
            id: 'sicp',
            rank: 2,
            delta: { type: 'up', label: '▲ 2' },
            votes: 385,
            title: 'Structure and Interpretation of Computer Programs (SICP)',
            host: 'mitpress.mit.edu',
            description:
                'MIT’s foundational classic on computer programming, functional computational models, and abstraction techniques.',
            tags: ['free', 'book', 'course'],
            submitter: 'alank',
            commentsCount: 42,
            href: '#',
        },
        {
            id: 'crafting-interpreters',
            rank: 3,
            delta: { type: 'up', label: '▲ 1' },
            votes: 310,
            title: 'Crafting Interpreters',
            host: 'craftinginterpreters.com',
            description:
                'A handbook for making programming languages by Bob Nystrom, walking through tree-walk interpreters and bytecode virtual machines.',
            tags: ['free', 'book'],
            submitter: 'munin',
            commentsCount: 29,
            href: '#',
        },
        {
            id: 'vscode',
            rank: 4,
            delta: { type: 'down', label: '▼ 1' },
            votes: 278,
            title: 'Visual Studio Code',
            host: 'code.visualstudio.com',
            description:
                'Extensible, lightweight open-source code editor with a massive ecosystem for debugging, syntax, and Git integration.',
            tags: ['free', 'official', 'tool'],
            submitter: 'sarah_m',
            commentsCount: 54,
            href: '#',
        },
        {
            id: 'fullstack-open',
            rank: 5,
            delta: { type: 'up', label: '▲ 3' },
            votes: 245,
            title: 'Full Stack Open',
            host: 'fullstackopen.com',
            description:
                'University of Helsinki’s comprehensive modern web development course covering React, Node.js, TypeScript, and GraphQL.',
            tags: ['free', 'course', 'interactive'],
            submitter: 'helsinki_dev',
            commentsCount: 31,
            href: '#',
        },
        {
            id: 'exercism',
            rank: 6,
            delta: { type: 'flat', label: '—' },
            votes: 198,
            title: 'Exercism',
            host: 'exercism.org',
            description:
                'Code practice and human mentoring for 70+ programming languages. Completely free and community-powered.',
            tags: ['free', 'interactive', 'tool'],
            submitter: 'i_love_code',
            submitterTime: '1 day ago',
            commentsCount: 19,
            href: '#',
        },
    ],
};
