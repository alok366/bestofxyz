/**
 * Pending Category Page Mock Data
 *
 * Sourced from bestofxyz-experiment-pages/pending-category.html
 * Maintained in model fixture file per FSD architecture.
 */

export const MOCK_PENDING_CATEGORY = {
    id: 'best-rust-courses-for-beginners',
    title: 'Best Rust Courses for Beginners',
    badge: 'New — not yet public',
    breadcrumb: [
        { label: 'Categories', path: '/categories' },
        {
            label: 'Best Rust Courses for Beginners',
            path: '/pending/best-rust-courses-for-beginners',
        },
    ],
    description:
        'A new category for people with zero prior programming experience, waiting for enough resources to go live.',
    threshold: {
        currentCount: 2,
        targetCount: 5,
        percentage: 40,
        subtext: 'needed before this category goes live',
        explanation:
            "Once it clears the threshold, this shows up in the category directory and becomes searchable. Until then it's only reachable by direct link — anyone who lands here can still browse and vote on what's already in it.",
        ctaText: 'Submit a resource to help it go live →',
        ctaPath: '/submit',
    },
    resources: [
        {
            id: 'rust-for-absolute-beginners',
            rank: 1,
            delta: { type: 'flat', label: 'new' },
            votes: 18,
            title: 'Rust for Absolute Beginners',
            host: 'freecodecamp.org',
            description:
                'Assumes no prior programming knowledge at all — starts with what a variable even is.',
            tags: ['free', 'video'],
            submitter: 'moss',
            commentsCount: 0,
            href: '/resource/rust-for-absolute-beginners',
        },
        {
            id: 'learn-rust-the-slow-way',
            rank: 2,
            delta: { type: 'flat', label: 'new' },
            votes: 9,
            title: 'Learn Rust the Slow Way',
            host: 'indie blog series',
            description:
                'A weekly blog series aimed squarely at people learning to program for the first time.',
            tags: ['free', 'beginner-friendly'],
            submitter: 'priya_k',
            commentsCount: 0,
            href: '/resource/learn-rust-the-slow-way',
        },
    ],
};
