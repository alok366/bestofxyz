// MOCK DATA — Category Directory
// Top-level categories (team-owned) + subcategories (user-proposed)

export const mockTopCategories = [
  {
    id: 'cat_programming',
    name: 'Programming',
    slug: 'programming',
    description: 'Languages, frameworks, systems programming, and software craft.',
    icon: '⚡',
    resourceCount: 1420,
    subcategoryCount: 38,
    isFeatured: true,
    topSubcategories: [
      { id: 'sub_rust', name: 'Best Rust Courses', count: 42, votes: 1240, status: 'live' },
      { id: 'sub_react', name: 'Best React Component Libraries', count: 35, votes: 980, status: 'live' },
      { id: 'sub_go', name: 'Best Go Web Frameworks', count: 28, votes: 760, status: 'live' },
      { id: 'sub_sysdesign', name: 'Best System Design & Distributed Systems Engineering Resources for Senior Software Architects', count: 19, votes: 620, status: 'live' }, // Long title edge case
      { id: 'sub_zig', name: 'Best Zig Tutorials', count: 5, votes: 45, status: 'pending', progress: '5/5', thresholdMet: true }, // Threshold met edge case
      { id: 'sub_rust_learn', name: 'Best Ways to Learn Rust', count: 3, votes: 24, status: 'pending', progress: '3/5' }, // Pending progress
    ]
  },
  {
    id: 'cat_design',
    name: 'Design Tools',
    slug: 'design-tools',
    description: 'UI/UX design, prototyping, vector illustration, and 3D modeling tools.',
    icon: '🎨',
    resourceCount: 890,
    subcategoryCount: 22,
    isFeatured: true,
    topSubcategories: [
      { id: 'sub_figma', name: 'Best Figma Plugins for UI Designers', count: 54, votes: 1100, status: 'live' },
      { id: 'sub_color', name: 'Best Palette Generators for Dark Mode', count: 31, votes: 850, status: 'live' },
      { id: 'sub_icons', name: 'Best Open Source Icon Sets', count: 40, votes: 720, status: 'live' },
      { id: 'sub_spline', name: 'Best 3D Web Generators', count: 2, votes: 12, status: 'pending', progress: '2/5' }
    ]
  },
  {
    id: 'cat_datascience',
    name: 'Data Science & AI',
    slug: 'data-science',
    description: 'Machine learning, data pipelines, LLM fine-tuning, and Python analytics.',
    icon: '🧠',
    resourceCount: 1150,
    subcategoryCount: 29,
    isFeatured: true,
    topSubcategories: [
      { id: 'sub_llm', name: 'Best Local LLM Interfaces (Ollama/LM Studio)', count: 48, votes: 1450, status: 'live' },
      { id: 'sub_python_data', name: 'Best Python Data Visualization Libraries', count: 39, votes: 920, status: 'live' },
      { id: 'sub_vector_db', name: 'Best Vector Databases for RAG Applications', count: 27, votes: 810, status: 'live' },
      { id: 'sub_prompt_eng', name: 'Best Prompt Engineering Guides', count: 4, votes: 38, status: 'pending', progress: '4/5' }
    ]
  },
  {
    id: 'cat_devops',
    name: 'DevOps & Cloud',
    slug: 'devops',
    description: 'CI/CD pipelines, Docker, Kubernetes, infrastructure as code, and observability.',
    icon: '☁️',
    resourceCount: 640,
    subcategoryCount: 16,
    isFeatured: false,
    topSubcategories: [
      { id: 'sub_docker', name: 'Best Lightweight Container Runtimes', count: 22, votes: 510, status: 'live' },
      { id: 'sub_k8s', name: 'Best Kubernetes Local Tools (Minikube/Kind)', count: 31, votes: 640, status: 'live' },
      { id: 'sub_monitoring', name: 'Best Self-Hosted Observability Stacks', count: 18, votes: 430, status: 'live' }
    ]
  },
  {
    id: 'cat_gamedev',
    name: 'Game Development',
    slug: 'game-development',
    description: 'Game engines, asset creation, shaders, and game design theory.',
    icon: '🎮',
    resourceCount: 2, // Sparse edge case
    subcategoryCount: 1,
    isFeatured: false,
    topSubcategories: [
      { id: 'sub_godot_2d', name: 'Best 2D Pixel Art Shaders for Godot Engine', count: 2, votes: 9, status: 'pending', progress: '2/5' } // Sparse pending edge case
    ]
  }
];

export const mockStats = {
  totalCategories: 5,
  totalSubcategories: 106,
  totalResources: 4102,
  pendingProposals: 14
};
