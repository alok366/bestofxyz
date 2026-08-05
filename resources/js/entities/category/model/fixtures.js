/**
 * Category entity — model / fixtures.
 *
 * Browsing categories with metadata.  When the real API is wired up,
 * this file becomes a Redux Toolkit slice instead.
 */

export const CATEGORIES = [
    { icon: '💻', name: 'Programming', description: 'Books, courses, tutorials, and coding tools.', count: 2840 },
    { icon: '🤖', name: 'Artificial Intelligence', description: 'LLMs, frameworks, research papers, and courses.', count: 1520 },
    { icon: '🏗️', name: 'System Design', description: 'Scalability, distributed systems, and architecture.', count: 960 },
    { icon: '🗄️', name: 'Databases', description: 'SQL, PostgreSQL, MySQL, indexing, and performance.', count: 780 },
    { icon: '🐧', name: 'Linux', description: 'Shell, administration, networking, and automation.', count: 640 },
    { icon: '⚡', name: 'Productivity', description: 'Note-taking, task management, and developer workflows.', count: 420 },
];
