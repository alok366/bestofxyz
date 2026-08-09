/**
 * Category Directory Mock Data
 *
 * Sourced from bestofxyz-experiment-pages/category-directory.html
 * Kept in a separate model fixture file per FSD guidelines.
 */

export const CATEGORY_DIRECTORY_HEADER = {
    eyebrow: 'Directory',
    title: 'Every category,\nranked by the people who use it.',
    description:
        'Top-level categories are curated by the bestofxyz team. Everything inside them — the "best of" lists — is proposed and ranked by the community.',
};

export const CATEGORY_BOARDS = [
    {
        id: 'programming',
        title: 'Programming',
        subcategoriesCount: 12,
        resourcesCount: 340,
        href: '/category/programming',
        subcategories: [
            {
                id: 'rust-courses',
                name: 'Best Rust Courses',
                topResource: 'The Rust Book',
                score: 214,
            },
            {
                id: 'js-frameworks',
                name: 'Best JavaScript Frameworks',
                topResource: 'React',
                score: 402,
            },
            {
                id: 'python-ides',
                name: 'Best Python IDEs',
                topResource: 'PyCharm',
                score: 178,
            },
            {
                id: 'rust-courses-beginners',
                name: 'Best Rust Courses for Beginners',
                badge: 'New',
                status: '2/5 resources',
            },
        ],
    },
    {
        id: 'design-tools',
        title: 'Design Tools',
        subcategoriesCount: 8,
        resourcesCount: 210,
        href: '#',
        subcategories: [
            {
                id: 'icon-libraries',
                name: 'Best Icon Libraries',
                topResource: 'Lucide',
                score: 187,
            },
            {
                id: 'figma-plugins',
                name: 'Best Figma Plugins',
                topResource: 'Autoflow',
                score: 143,
            },
            {
                id: 'color-palette-generators',
                name: 'Best Color Palette Generators',
                topResource: 'Coolors',
                score: 98,
            },
        ],
    },
    {
        id: 'data-science',
        title: 'Data Science',
        subcategoriesCount: 6,
        resourcesCount: 150,
        href: '#',
        subcategories: [
            {
                id: 'ml-courses',
                name: 'Best ML Courses',
                topResource: 'fast.ai',
                score: 203,
            },
            {
                id: 'free-dataset-sites',
                name: 'Best Free Dataset Sites',
                topResource: 'Kaggle',
                score: 156,
            },
            {
                id: 'python-libraries-data-viz',
                name: 'Best Python Libraries for Data Viz',
                topResource: 'Plotly',
                score: 121,
            },
        ],
    },
    {
        id: 'productivity',
        title: 'Productivity',
        subcategoriesCount: 9,
        resourcesCount: 265,
        href: '#',
        subcategories: [
            {
                id: 'note-taking-apps',
                name: 'Best Note-Taking Apps',
                topResource: 'Obsidian',
                score: 311,
            },
            {
                id: 'task-managers',
                name: 'Best Task Managers',
                topResource: 'Things 3',
                score: 178,
            },
            {
                id: 'time-blocking-tools',
                name: 'Best Time-Blocking Tools',
                topResource: 'Sunsama',
                score: 64,
            },
        ],
    },
    {
        id: 'marketing',
        title: 'Marketing',
        subcategoriesCount: 5,
        resourcesCount: 88,
        href: '#',
        subcategories: [
            {
                id: 'seo-auditing-tools',
                name: 'Best SEO Auditing Tools',
                topResource: 'Ahrefs',
                score: 95,
            },
            {
                id: 'email-newsletter-tools',
                name: 'Best Email Newsletter Tools',
                topResource: 'Buttondown',
                score: 72,
            },
            {
                id: 'landing-page-builders',
                name: 'Best Landing Page Builders',
                topResource: 'Framer',
                score: 58,
            },
        ],
    },
    {
        id: 'no-code-tools',
        title: 'No-Code Tools',
        subcategoriesCount: 7,
        resourcesCount: 132,
        href: '#',
        subcategories: [
            {
                id: 'no-code-database-tools',
                name: 'Best No-Code Database Tools',
                topResource: 'Airtable',
                score: 167,
            },
            {
                id: 'automation-platforms',
                name: 'Best Automation Platforms',
                topResource: 'Zapier',
                score: 144,
            },
            {
                id: 'website-builders',
                name: 'Best Website Builders',
                topResource: 'Webflow',
                score: 121,
            },
        ],
    },
];
