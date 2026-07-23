// MOCK DATA — Subcategory Page ("Best Rust Courses")

export const mockSubcategoryMeta = {
  id: 'sub_rust',
  title: 'Best Rust Courses',
  slug: 'best-rust-courses',
  parentCategory: { name: 'Programming', slug: 'programming' },
  description: 'Community-voted rankings for the best books, interactive tutorials, and video masterclasses to learn Rust programming in 2026.',
  totalVotes: 1240,
  resourceCount: 6,
  status: 'live',
  tags: ['all', 'beginner-friendly', 'book', 'video', 'async', 'free', 'interactive', 'advanced'],
  submittedBy: 'kdev',
  createdAt: 'March 2026'
};

export const mockResourcesList = [
  {
    id: 'res_1',
    rank: 1,
    title: 'Rust in Action by Tim McNamara',
    url: 'https://manning.com/books/rust-in-action',
    domain: 'manning.com',
    description: 'A hands-on guide that introduces the Rust language through real-world systems programming topics such as memory management, networking, and kernel-level code.',
    votes: 342,
    userVote: 0, // 0 = none, 1 = upvoted, -1 = downvoted
    commentCount: 48,
    submittedBy: 'kdev',
    submittedAt: '2 months ago',
    tags: ['beginner-friendly', 'book', 'paid', 'systems'],
    isFeatured: true
  },
  {
    id: 'res_2',
    rank: 2,
    title: 'The Comprehensive 2026 Masterclass on Advanced Systems Programming in Rust & Asynchronous Tokio Internals', // Unusually long title edge case
    url: 'https://rust-masterclass-2026.dev/tokio-deep-dive',
    domain: 'rust-masterclass-2026.dev',
    description: 'An exhaustive 40-hour deep dive into Rust memory ordering, zero-cost abstractions, Pin/Future mechanics, and custom Tokio async executor scheduling.',
    votes: 215,
    userVote: 0,
    commentCount: 31,
    submittedBy: 'tokio_fanatic',
    submittedAt: '3 weeks ago',
    tags: ['advanced', 'video', 'async'],
    isFeatured: false
  },
  {
    id: 'res_3',
    rank: 3,
    title: 'Zero To Production In Rust',
    url: 'https://zero2prod.com',
    domain: 'zero2prod.com',
    description: 'Great for backend developers. Covers building production-grade web APIs in Rust with Actix-web, SQLx, telemetry, and Docker deployment.',
    votes: 189,
    userVote: 1, // User already upvoted
    commentCount: 22,
    submittedBy: 'luca_p',
    submittedAt: '1 month ago',
    tags: ['book', 'backend', 'async'],
    isFeatured: false
  },
  {
    id: 'res_4',
    rank: 4,
    title: 'Comprehensive Rust by Google Android Team',
    url: 'https://google.github.io/comprehensive-rust/',
    domain: 'google.github.io',
    description: 'Free multi-day Rust course developed by Google. Includes slide decks, code exercises, and Android/bare-metal deep dives.',
    votes: 98,
    userVote: 0,
    commentCount: 15,
    submittedBy: 'android_dev',
    submittedAt: '2 weeks ago',
    tags: ['free', 'interactive', 'official'],
    isFeatured: false
  },
  {
    id: 'res_5',
    rank: 5,
    title: 'Rustlings Interactive Exercises', // 0-vote fresh item edge case
    url: 'https://github.com/rust-lang/rustlings',
    domain: 'github.com',
    description: 'Small exercises to get you used to reading and writing Rust code! Great companion while reading The Rust Programming Language book.',
    votes: 0, // 0 votes edge case!
    userVote: 0,
    commentCount: 2,
    submittedBy: 'fresh_coder',
    submittedAt: '1 hour ago',
    tags: ['free', 'interactive', 'beginner-friendly'],
    isFeatured: false
  },
  {
    id: 'res_6',
    rank: 6,
    title: 'Outdated Rust 2015 Legacy Syntax Tutorial', // Downvoted edge case
    url: 'https://legacy-tutorials-archive.org/rust-2015',
    domain: 'legacy-tutorials-archive.org',
    description: 'Old guide referencing pre-edition 2018 macro syntax and deprecated try! macros.',
    votes: -4, // Negative downvoted edge case!
    userVote: -1,
    commentCount: 5,
    submittedBy: 'troll_user',
    submittedAt: '5 months ago',
    tags: ['outdated'],
    isFeatured: false
  }
];
