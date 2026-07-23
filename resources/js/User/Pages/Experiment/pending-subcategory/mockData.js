// MOCK DATA — Pending Subcategory State

export const mockPendingProposal = {
  id: 'sub_rust_async_guides',
  name: 'Best Async Rust Guides',
  slug: 'best-async-rust-guides',
  parentCategory: { name: 'Programming', slug: 'programming' },
  description: 'Guides, tutorials, and deep-dives specifically focused on Tokio, async/await mechanics, Futures, and async IO patterns in Rust.',
  proposedBy: 'alex_dev',
  createdAt: '2 days ago',
  status: 'pending',
  currentResources: 2,
  requiredResources: 5,
  currentVotes: 8,
  requiredVotes: 10,
  tags: ['async', 'tokio', 'concurrency', 'advanced'],
  submittedResources: [
    {
      id: 'p_res_1',
      title: 'Async Rust: What is a Waker?',
      url: 'https://rust-lang.github.io/async-book/02_execution/03_wakers.html',
      domain: 'rust-lang.github.io',
      submittedBy: 'alex_dev',
      votes: 5,
      submittedAt: '2 days ago'
    },
    {
      id: 'p_res_2',
      title: 'Understanding Async/Await in Rust with Tokio',
      url: 'https://tokio.rs/tokio/tutorial',
      domain: 'tokio.rs',
      submittedBy: 'alex_dev',
      votes: 3,
      submittedAt: '1 day ago'
    }
  ]
};
