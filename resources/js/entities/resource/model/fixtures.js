/**
 * Resource entity — model / fixtures.
 *
 * These dummy records currently power the homepage.  When the real API
 * is wired up, this file becomes a Redux Toolkit slice instead.
 */

export const RESOURCES = [
    {
        title: 'The C Programming Language (K&R)',
        description:
            'The classic book written by Brian Kernighan and Dennis Ritchie. Concise, authoritative, and still one of the most recommended introductions to C.',
        type: 'book',
        typeLabel: 'Book',
        rating: 9.6,
        discussions: 84,
        voters: '1.9k',
        votes: 428,
        rank: 'Rank #1 overall',
    },
    {
        title: 'Harvard CS50 (C Weeks)',
        description:
            'Excellent video-based introduction with hands-on problem sets and visual explanations.',
        type: 'video',
        typeLabel: 'Video Course',
        rating: 9.4,
        discussions: 121,
        voters: '3.1k',
        votes: 381,
        rank: 'Best for beginners',
    },
    {
        title: 'Learn-C.org',
        description:
            'Browser-based interactive tutorial where you can write and run C code without installing anything.',
        type: 'interactive',
        typeLabel: 'Interactive',
        rating: 8.9,
        discussions: 37,
        voters: '940',
        votes: 246,
        rank: 'Best free option',
    },
];
