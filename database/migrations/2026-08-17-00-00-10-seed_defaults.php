<?php

$sql = "
INSERT INTO tags (name) VALUES
    ('free'), ('paid'), ('video'), ('book'), ('interactive'),
    ('tool'), ('official'), ('exercises'), ('beginner'), ('advanced'),
    ('tutorial');

-- Root domains (parent_id = NULL)
INSERT INTO categories (id, parent_id, name, slug, icon, description, status, display_order) VALUES
    (1, NULL, 'Programming',           'programming',           '💻', 'Languages, frameworks, and developer tools',        'live', 1),
    (2, NULL, 'Design Tools',          'design-tools',          '🎨', 'UI/UX, icons, color palettes, and assets',          'live', 2),
    (3, NULL, 'Data Science',          'data-science',          '📊', 'Machine learning, datasets, and analytics',         'live', 3),
    (4, NULL, 'Productivity',          'productivity',          '⚡', 'Note-taking, task managers, and time tracking',     'live', 4),
    (5, NULL, 'Marketing',             'marketing',             '📢', 'SEO, newsletters, and landing page builders',       'live', 5),
    (6, NULL, 'No-Code Tools',         'no-code-tools',         '🧩', 'Automation, databases, and website builders',       'live', 6);

-- Live & Pending Categories under domains
INSERT INTO categories (id, parent_id, name, slug, icon, description, status, resource_threshold, display_order) VALUES
    (10, 1, 'Best Rust Courses',                  'best-rust-courses',                  '', 'The top courses and resources for learning Rust.', 'live', 5, 1),
    (11, 1, 'Best JavaScript Frameworks',         'best-javascript-frameworks',         '', 'Top frontend and fullstack JS frameworks.', 'live', 5, 2),
    (12, 1, 'Best Python IDEs',                   'best-python-ides',                   '', 'The most powerful IDEs and editors for Python.', 'live', 5, 3),
    (13, 1, 'Best Rust Courses for Beginners',     'best-rust-courses-for-beginners',     '', 'Curated beginner-friendly Rust tutorials and books.', 'pending', 5, 4),
    (20, 2, 'Best Icon Libraries',                'best-icon-libraries',                '', 'Modern SVG icon libraries for web and mobile.', 'live', 5, 1),
    (21, 2, 'Best Figma Plugins',                 'best-figma-plugins',                 '', 'Plugins to supercharge your Figma workflow.', 'live', 5, 2),
    (22, 2, 'Best Color Palette Generators',      'best-color-palette-generators',      '', 'Tools to build accessible color schemes.', 'live', 5, 3),
    (30, 3, 'Best ML Courses',                    'best-ml-courses',                    '', 'Machine learning and deep learning courses.', 'live', 5, 1),
    (31, 3, 'Best Free Dataset Sites',            'best-free-dataset-sites',            '', 'Repositories of open datasets for research and projects.', 'live', 5, 2),
    (40, 4, 'Best Note-Taking Apps',              'best-note-taking-apps',              '', 'Top apps for capturing notes and knowledge.', 'live', 5, 1),
    (41, 4, 'Best Task Managers',                 'best-task-managers',                 '', 'Manage to-dos, projects, and personal tasks.', 'live', 5, 2),
    (50, 5, 'Best SEO Auditing Tools',            'best-seo-auditing-tools',            '', 'Audit technical SEO, backlinks, and search rankings.', 'live', 5, 1),
    (60, 6, 'Best No-Code Database Tools',        'best-no-code-database-tools',        '', 'Visual databases, Airtable alternatives, and backends.', 'live', 5, 1);

-- Default community user
INSERT INTO users (id, username, email, password, role, is_active) VALUES
    (1, 'community', 'community@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user', 1);

-- Sample Resources for Best Rust Courses
INSERT INTO resources (id, category_id, submitter_id, title, slug, url, url_hash, host, description, score, hot_score, status) VALUES
    (1, 10, 1, 'The Rust Programming Language (The Book)', 'the-rust-programming-language-the-book', 'https://doc.rust-lang.org/book/', SHA2('https://doc.rust-lang.org/book/', 256), 'doc.rust-lang.org', 'The official Rust book by Steve Klabnik and Carol Nichols. The definitive guide to learning Rust from the ground up.', 214, 214.0, 'active'),
    (2, 10, 1, 'Rust by Example', 'rust-by-example', 'https://doc.rust-lang.org/rust-by-example/', SHA2('https://doc.rust-lang.org/rust-by-example/', 256), 'doc.rust-lang.org', 'A collection of runnable examples that exercise various Rust concepts and standard libraries.', 186, 186.0, 'active'),
    (3, 10, 1, 'Rustlings', 'rustlings', 'https://github.com/rust-lang/rustlings', SHA2('https://github.com/rust-lang/rustlings', 256), 'github.com', 'Small exercises to get you used to reading and writing Rust code. Great hands-on practice companion to The Book.', 152, 152.0, 'active'),
    (4, 10, 1, 'Comprehensive Rust', 'comprehensive-rust', 'https://google.github.io/comprehensive-rust/', SHA2('https://google.github.io/comprehensive-rust/', 256), 'google.github.io', 'A multi-day Rust course developed by the Android team at Google.', 98, 98.0, 'active'),
    (5, 10, 1, 'Zero To Production In Rust', 'zero-to-production-in-rust', 'https://www.zero2prod.com/', SHA2('https://www.zero2prod.com/', 256), 'zero2prod.com', 'An opinionated guide to backend development in Rust by Luca Palmieri.', 74, 74.0, 'active');

-- Sample Resources for Pending Category (Best Rust Courses for Beginners)
INSERT INTO resources (id, category_id, submitter_id, title, slug, url, url_hash, host, description, score, hot_score, status) VALUES
    (6, 13, 1, 'Tour of Rust', 'tour-of-rust', 'https://tourofrust.com/', SHA2('https://tourofrust.com/', 256), 'tourofrust.com', 'An interactive step-by-step tour through the fundamental building blocks of Rust.', 12, 12.0, 'active'),
    (7, 13, 1, 'Rust for Beginners - FreeCodeCamp', 'rust-for-beginners-freecodecamp', 'https://www.freecodecamp.org/news/rust-crash-course/', SHA2('https://www.freecodecamp.org/news/rust-crash-course/', 256), 'freecodecamp.org', 'A beginner-friendly crash course into Rust programming basics and syntax.', 8, 8.0, 'active');

-- Resource Tags
INSERT INTO resource_tags (resource_id, tag_id) VALUES
    (1, 1), (1, 4), (1, 7), (1, 9),
    (2, 1), (2, 5), (2, 7),
    (3, 1), (3, 8), (3, 9),
    (4, 1), (4, 7), (4, 9),
    (5, 2), (5, 4), (5, 10),
    (6, 1), (6, 5), (6, 9),
    (7, 1), (7, 11), (7, 9);
";


