/**
 * Discussion entity — model / fixtures.
 *
 * These dummy records currently power the homepage.  When the real API
 * is wired up, this file becomes a Redux Toolkit slice instead.
 */

export const DISCUSSIONS = [
    {
        initials: 'AP',
        username: '@alokpanwar',
        timeAgo: '2 hours ago',
        body: "I started with K&R but switched to CS50 after chapter 3. The visual explanations made pointers much easier to understand.",
        upvotes: 62,
        replies: 14,
        tag: 'Learn C Programming',
    },
    {
        initials: 'RK',
        username: '@rahul_k',
        timeAgo: '5 hours ago',
        body: "For absolute beginners, I'd rank CS50 above K&R. K&R is amazing once you already know basic programming concepts.",
        upvotes: 41,
        replies: 9,
        tag: 'Books vs Video Courses',
    },
];
