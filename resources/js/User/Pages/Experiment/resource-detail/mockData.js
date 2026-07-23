// MOCK DATA — Resource Detail Page

export const mockResourceDetail = {
  id: 'res_2',
  title: 'The Comprehensive 2026 Masterclass on Advanced Systems Programming in Rust & Asynchronous Tokio Internals', // Unusually long title edge case
  url: 'https://rust-masterclass-2026.dev/tokio-deep-dive',
  domain: 'rust-masterclass-2026.dev',
  category: 'Programming',
  subcategory: 'Best Rust Courses',
  description: 'An exhaustive 40-hour deep dive into Rust memory ordering, zero-cost abstractions, Pin/Future mechanics, and custom Tokio async executor scheduling. Designed for experienced engineers transitioning from C++ or Go.',
  votes: 215,
  userVote: 0,
  submittedBy: 'tokio_fanatic',
  submittedAt: '3 weeks ago',
  tags: ['advanced', 'video', 'async', 'tokio', 'concurrency'],
  authorAvatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=tokio'
};

export const mockComments = [
  {
    id: 'c_1',
    author: 'ana_b',
    avatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=ana_b',
    votes: 42,
    userVote: 1, // Upvoted
    timeAgo: '2 weeks ago',
    body: 'Genuinely the single best resource on async Rust available online. Section 4 explaining Waker registration and Epoll integration saved our engineering team weeks of debugging. Worth every penny!',
    replies: [
      {
        id: 'c_1_1',
        author: 'dev_marcus',
        avatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=marcus',
        votes: 12,
        userVote: 0,
        timeAgo: '10 days ago',
        body: 'Did you complete the custom threadpool assignment at the end of module 6? Struggled a bit with atomic memory orderings there.',
        replies: [
          {
            id: 'c_1_1_1',
            author: 'ana_b',
            avatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=ana_b',
            votes: 8,
            userVote: 0,
            timeAgo: '9 days ago',
            body: 'Yes! Use `Ordering::Acquire` when loading the task state flag and `Ordering::Release` on unlock. Don’t use `Ordering::Relaxed` or you will get race conditions on ARM architecture.',
            replies: []
          }
        ]
      }
    ]
  },
  {
    id: 'c_2',
    author: 'gopher_transplant',
    avatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=gopher',
    votes: 18,
    userVote: 0,
    timeAgo: '1 week ago',
    body: 'Coming from Go, Tokio pin/unpin mechanics felt super complex at first compared to goroutines. But this course breaks down Future polling in a way that finally clicked.',
    replies: []
  },
  {
    id: 'c_3',
    author: 'troll_critic', // Downvoted comment edge case
    avatar: 'https://api.dicebear.com/7.x/identicon/svg?seed=troll',
    votes: -7, // Downvoted edge case!
    userVote: -1,
    timeAgo: '3 days ago',
    body: 'Way too long and talks too much about hardware cache lines. Just use Node.js instead.',
    replies: []
  }
];
