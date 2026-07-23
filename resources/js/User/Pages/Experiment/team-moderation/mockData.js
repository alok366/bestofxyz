// MOCK DATA — Team Moderation View

export const mockPendingQueue = [
  {
    id: 'prop_1',
    name: 'Best Ways to Learn Rust',
    slug: 'best-ways-to-learn-rust',
    parentCategory: 'Programming',
    proposedBy: 'rustacean_99',
    createdAt: '2 days ago',
    resourceCount: 3,
    votes: 14,
    hasPotentialDuplicate: true,
    suggestedDuplicateTarget: { id: 'sub_rust', name: 'Best Rust Courses' },
    resources: [
      { id: 'r1', title: 'The Rust Programming Language Book', votes: 12 },
      { id: 'r2', title: 'Rust by Example Interactive', votes: 8 },
      { id: 'r3', title: 'Rustlings Exercises Repo', votes: 5 }
    ]
  },
  {
    id: 'prop_2',
    name: 'Best Zig Tutorials for C Developers',
    slug: 'best-zig-tutorials',
    parentCategory: 'Programming',
    proposedBy: 'ziggy_stardust',
    createdAt: '4 days ago',
    resourceCount: 5,
    votes: 18,
    hasPotentialDuplicate: false,
    resources: [
      { id: 'r4', title: 'Zig in 100 Seconds', votes: 15 },
      { id: 'r5', title: 'Learning Zig - Systems Programming', votes: 9 },
      { id: 'r6', title: 'Comptime Metaprogramming Guide', votes: 7 },
      { id: 'r7', title: 'Memory Management in Zig vs C', votes: 6 },
      { id: 'r8', title: 'Zig Standard Library Walkthrough', votes: 4 }
    ]
  },
  {
    id: 'prop_3',
    name: 'Best 3D Web Generators',
    slug: 'best-3d-web-generators',
    parentCategory: 'Design Tools',
    proposedBy: 'spline_artist',
    createdAt: '1 week ago',
    resourceCount: 2,
    votes: 4,
    hasPotentialDuplicate: false,
    resources: [
      { id: 'r9', title: 'Spline 3D Web Designer', votes: 10 },
      { id: 'r10', title: 'Three.js Journey Course', votes: 6 }
    ]
  }
];

export const mockLiveTargetSubcategories = [
  { id: 'sub_rust', name: 'Best Rust Courses', parent: 'Programming' },
  { id: 'sub_react', name: 'Best React Component Libraries', parent: 'Programming' },
  { id: 'sub_go', name: 'Best Go Web Frameworks', parent: 'Programming' },
  { id: 'sub_figma', name: 'Best Figma Plugins for UI Designers', parent: 'Design Tools' }
];
