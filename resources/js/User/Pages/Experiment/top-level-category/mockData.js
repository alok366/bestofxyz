// MOCK DATA — Top-Level Category Page ("Programming")

export const mockCategoryInfo = {
  id: 'cat_programming',
  name: 'Programming',
  slug: 'programming',
  description: 'The definitive hub for programming languages, frameworks, system architecture, and modern software development tutorials.',
  icon: '⚡',
  totalResources: 1420,
  liveSubcategoryCount: 28,
  pendingSubcategoryCount: 6,
  curator: 'bestofxyz team',
  lastUpdated: '2 hours ago'
};

export const mockSubcategories = [
  // Live Subcategories
  {
    id: 'sub_rust',
    name: 'Best Rust Courses',
    slug: 'best-rust-courses',
    description: 'Top-rated courses, books, and tutorials for learning Rust, from beginner borrow-checker basics to async Tokio.',
    resourceCount: 42,
    upvotes: 1240,
    status: 'live',
    proposedBy: 'kdev',
    createdAt: '3 months ago',
    topTags: ['beginner-friendly', 'book', 'video', 'async']
  },
  {
    id: 'sub_react',
    name: 'Best React Component Libraries',
    slug: 'best-react-component-libraries',
    description: 'Production-ready UI kits, design systems, and headless component primitives for modern React apps.',
    resourceCount: 35,
    upvotes: 980,
    status: 'live',
    proposedBy: 'sarah_ui',
    createdAt: '2 months ago',
    topTags: ['design-system', 'tailwind', 'headless', 'accessible']
  },
  {
    id: 'sub_sysdesign',
    name: 'Best System Design & Distributed Systems Engineering Resources for Senior Software Architects', // Long title edge case
    slug: 'best-system-design-resources',
    description: 'High-scale architecture guides, database internals, consistency models, and real-world postmortems.',
    resourceCount: 19,
    upvotes: 620,
    status: 'live',
    proposedBy: 'arch_master',
    createdAt: '1 month ago',
    topTags: ['advanced', 'distributed-systems', 'architecture']
  },
  {
    id: 'sub_go',
    name: 'Best Go Web Frameworks',
    slug: 'best-go-web-frameworks',
    description: 'Fast, lightweight web frameworks and HTTP routers for high-performance Golang backend microservices.',
    resourceCount: 28,
    upvotes: 760,
    status: 'live',
    proposedBy: 'gopher_guy',
    createdAt: '4 months ago',
    topTags: ['backend', 'microservices', 'performance']
  },

  // Pending Subcategories (Threshold Progress)
  {
    id: 'sub_zig',
    name: 'Best Zig Tutorials', // Threshold Met edge case (5/5 resources, 12 upvotes)
    slug: 'best-zig-tutorials',
    description: 'Resources for manual memory management, comptime metaprogramming, and replacing C with Zig.',
    resourceCount: 5,
    requiredCount: 5,
    upvotes: 14,
    requiredVotes: 10,
    status: 'pending',
    thresholdMet: true,
    proposedBy: 'ziggy_stardust',
    createdAt: '4 days ago',
    topTags: ['c-replacement', 'comptime', 'embedded']
  },
  {
    id: 'sub_rust_learn',
    name: 'Best Ways to Learn Rust', // Pending 3/5 progress edge case
    slug: 'best-ways-to-learn-rust',
    description: 'Interactive exercises, code katas, and mental models for mastering ownership and lifetimes.',
    resourceCount: 3,
    requiredCount: 5,
    upvotes: 24,
    requiredVotes: 10,
    status: 'pending',
    thresholdMet: false,
    proposedBy: 'alex_dev',
    createdAt: '1 day ago',
    topTags: ['interactive', 'katas', 'lifetimes']
  },
  {
    id: 'sub_cpp_micro',
    name: 'Best Modern C++ Core Guidelines & Concurrency Patterns',
    slug: 'best-cpp-concurrency',
    description: 'Modern C++20/C++23 lock-free data structures, atomics, and coroutines.',
    resourceCount: 2,
    requiredCount: 5,
    upvotes: 6,
    requiredVotes: 10,
    status: 'pending',
    thresholdMet: false,
    proposedBy: 'cpp_guru',
    createdAt: '5 hours ago',
    topTags: ['cpp20', 'concurrency', 'multithreading']
  }
];
