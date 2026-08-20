<?php

$sql = <<<'SQL'
INSERT INTO tags (name) VALUES
    ('free'), ('paid'), ('video'), ('book'), ('interactive'),
    ('tool'), ('official'), ('exercises'), ('beginner'), ('advanced'),
    ('tutorial'), ('course');

-- Users
INSERT INTO users (id, username, email, password_hash, role) VALUES
    (1, 'community', 'community@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (2, 'kdev', 'kdev@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (3, 'alank', 'alank@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (4, 'munin', 'munin@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (5, 'sarah_m', 'sarah_m@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (6, 'helsinki_dev', 'helsinki_dev@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (7, 'i_love_code', 'i_love_code@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (8, 'moss', 'moss@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (9, 'priya_k', 'priya_k@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (10, 'ana_b', 'ana_b@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user'),
    (11, 'throwaway99', 'throwaway99@bestofxyz.local', '$2y$12$e0MYzX12458019348912389124801923849012389012389012389', 'user');

-- Root domains (parent_id = NULL)
INSERT INTO categories (id, parent_id, name, slug, icon, description, status, display_order) VALUES
    (1, NULL, 'Programming',           'programming',           '💻', 'Languages, frameworks, editors, and developer tools — ranked and reviewed by the community.',        'live', 1),
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

-- Resources for Programming Category (category_id = 1)
INSERT INTO resources (id, category_id, submitted_by, title, slug, url, url_hash, host, description, score, hot_score, created_at) VALUES
    (100, 1, 2, 'The Rust Book', 'rust-book', 'https://doc.rust-lang.org/book/', UNHEX(SHA2('https://doc.rust-lang.org/book/', 256)), 'doc.rust-lang.org', 'The official and definitive guide to the Rust programming language, written and maintained by the Rust team.', 402, 402.0, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
    (101, 1, 3, 'Structure and Interpretation of Computer Programs (SICP)', 'sicp', 'https://mitpress.mit.edu/sicp/', UNHEX(SHA2('https://mitpress.mit.edu/sicp/', 256)), 'mitpress.mit.edu', 'MIT’s foundational classic on computer programming, functional computational models, and abstraction techniques.', 385, 385.0, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
    (102, 1, 4, 'Crafting Interpreters', 'crafting-interpreters', 'https://craftinginterpreters.com/', UNHEX(SHA2('https://craftinginterpreters.com/', 256)), 'craftinginterpreters.com', 'A handbook for making programming languages by Bob Nystrom, walking through tree-walk interpreters and bytecode virtual machines.', 310, 310.0, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
    (103, 1, 5, 'Visual Studio Code', 'vscode', 'https://code.visualstudio.com/', UNHEX(SHA2('https://code.visualstudio.com/', 256)), 'code.visualstudio.com', 'Extensible, lightweight open-source code editor with a massive ecosystem for debugging, syntax, and Git integration.', 278, 278.0, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
    (104, 1, 6, 'Full Stack Open', 'fullstack-open', 'https://fullstackopen.com/', UNHEX(SHA2('https://fullstackopen.com/', 256)), 'fullstackopen.com', 'University of Helsinki’s comprehensive modern web development course covering React, Node.js, TypeScript, and GraphQL.', 245, 245.0, DATE_SUB(NOW(), INTERVAL 2 WEEK)),
    (105, 1, 7, 'Exercism', 'exercism', 'https://exercism.org/', UNHEX(SHA2('https://exercism.org/', 256)), 'exercism.org', 'Code practice and human mentoring for 70+ programming languages. Completely free and community-powered.', 198, 198.0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Resources for Best Rust Courses (category_id = 10)
INSERT INTO resources (id, category_id, submitted_by, title, slug, url, url_hash, host, description, score, hot_score, created_at) VALUES
    (1, 10, 2, 'The Rust Programming Language (The Book)', 'the-rust-programming-language-the-book', 'https://doc.rust-lang.org/book/', UNHEX(SHA2('https://doc.rust-lang.org/book/', 256)), 'doc.rust-lang.org', 'The official Rust book by Steve Klabnik and Carol Nichols. The definitive guide to learning Rust from the ground up.', 214, 214.0, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
    (2, 10, 1, 'Rust by Example', 'rust-by-example', 'https://doc.rust-lang.org/rust-by-example/', UNHEX(SHA2('https://doc.rust-lang.org/rust-by-example/', 256)), 'doc.rust-lang.org', 'A collection of runnable examples that exercise various Rust concepts and standard libraries.', 186, 186.0, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
    (3, 10, 1, 'Rustlings', 'rustlings', 'https://github.com/rust-lang/rustlings', UNHEX(SHA2('https://github.com/rust-lang/rustlings', 256)), 'github.com', 'Small exercises to get you used to reading and writing Rust code. Great hands-on practice companion to The Book.', 152, 152.0, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
    (4, 10, 1, 'Comprehensive Rust', 'comprehensive-rust', 'https://google.github.io/comprehensive-rust/', UNHEX(SHA2('https://google.github.io/comprehensive-rust/', 256)), 'google.github.io', 'A multi-day Rust course developed by the Android team at Google.', 98, 98.0, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
    (5, 10, 1, 'Zero To Production In Rust', 'zero-to-production-in-rust', 'https://www.zero2prod.com/', UNHEX(SHA2('https://www.zero2prod.com/', 256)), 'zero2prod.com', 'An opinionated guide to backend development in Rust by Luca Palmieri.', 74, 74.0, DATE_SUB(NOW(), INTERVAL 2 WEEK));

-- Resources for Pending Category: Best Rust Courses for Beginners (category_id = 13)
INSERT INTO resources (id, category_id, submitted_by, title, slug, url, url_hash, host, description, score, hot_score, created_at) VALUES
    (6, 13, 8, 'Rust for Absolute Beginners', 'rust-for-absolute-beginners', 'https://freecodecamp.org/news/rust-for-absolute-beginners', UNHEX(SHA2('https://freecodecamp.org/news/rust-for-absolute-beginners', 256)), 'freecodecamp.org', 'Assumes no prior programming knowledge at all — starts with what a variable even is.', 18, 18.0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (7, 13, 9, 'Learn Rust the Slow Way', 'learn-rust-the-slow-way', 'https://blog.example.com/rust-slow-way', UNHEX(SHA2('https://blog.example.com/rust-slow-way', 256)), 'indie blog series', 'A weekly blog series aimed squarely at people learning to program for the first time.', 9, 9.0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Resources for other child categories
INSERT INTO resources (id, category_id, submitted_by, title, slug, url, url_hash, host, description, score, hot_score) VALUES
    (200, 11, 1, 'React', 'react', 'https://react.dev/', UNHEX(SHA2('https://react.dev/', 256)), 'react.dev', 'The library for web and native user interfaces.', 402, 402.0),
    (201, 12, 1, 'PyCharm', 'pycharm', 'https://www.jetbrains.com/pycharm/', UNHEX(SHA2('https://www.jetbrains.com/pycharm/', 256)), 'jetbrains.com', 'The Python IDE for professional developers by JetBrains.', 178, 178.0),
    (202, 20, 1, 'Lucide', 'lucide', 'https://lucide.dev/', UNHEX(SHA2('https://lucide.dev/', 256)), 'lucide.dev', 'Beautiful & consistent icons made by the community.', 187, 187.0),
    (203, 21, 1, 'Autoflow', 'autoflow', 'https://www.figma.com/community/plugin/733902569503222218/Autoflow', UNHEX(SHA2('https://www.figma.com/community/plugin/733902569503222218/Autoflow', 256)), 'figma.com', 'Automate flow charts and connections directly in Figma.', 143, 143.0),
    (204, 22, 1, 'Coolors', 'coolors', 'https://coolors.co/', UNHEX(SHA2('https://coolors.co/', 256)), 'coolors.co', 'The super fast color palettes generator.', 98, 98.0),
    (205, 30, 1, 'fast.ai', 'fast-ai', 'https://www.fast.ai/', UNHEX(SHA2('https://www.fast.ai/', 256)), 'fast.ai', 'Practical Deep Learning for Coders.', 203, 203.0),
    (206, 31, 1, 'Kaggle', 'kaggle', 'https://www.kaggle.com/datasets', UNHEX(SHA2('https://www.kaggle.com/datasets', 256)), 'kaggle.com', 'Explore, analyze, and share quality open data.', 156, 156.0),
    (207, 40, 1, 'Obsidian', 'obsidian', 'https://obsidian.md/', UNHEX(SHA2('https://obsidian.md/', 256)), 'obsidian.md', 'Sharpen your thinking with markdown notes in a local vault.', 311, 311.0),
    (208, 41, 1, 'Things 3', 'things-3', 'https://culturedcode.com/things/', UNHEX(SHA2('https://culturedcode.com/things/', 256)), 'culturedcode.com', 'The award-winning personal task manager for Apple devices.', 178, 178.0),
    (209, 50, 1, 'Ahrefs', 'ahrefs', 'https://ahrefs.com/', UNHEX(SHA2('https://ahrefs.com/', 256)), 'ahrefs.com', 'All-in-one SEO toolset for search traffic growth.', 95, 95.0),
    (210, 60, 1, 'Airtable', 'airtable', 'https://airtable.com/', UNHEX(SHA2('https://airtable.com/', 256)), 'airtable.com', 'Connect databases, workflows, and teams with ease.', 167, 167.0);

-- Tags associations
INSERT INTO resource_tags (resource_id, tag_id) VALUES
    -- Programming page resources
    (100, 1), (100, 4), (100, 7),  -- free, book, official
    (101, 1), (101, 4), (101, 12), -- free, book, course
    (102, 1), (102, 4),            -- free, book
    (103, 1), (103, 6), (103, 7),  -- free, tool, official
    (104, 1), (104, 5), (104, 12), -- free, interactive, course
    (105, 1), (105, 5), (105, 6),  -- free, interactive, tool
    -- Best Rust Courses resources
    (1, 1), (1, 4), (1, 7), (1, 9),
    (2, 1), (2, 5), (2, 7),
    (3, 1), (3, 8), (3, 9),
    (4, 1), (4, 7), (4, 9),
    (5, 2), (5, 4), (5, 10),
    -- Pending category resources
    (6, 1), (6, 3), (6, 9),        -- free, video, beginner
    (7, 1), (7, 9);                -- free, beginner

-- Comments for Resource #100 and #1
INSERT INTO comments (id, resource_id, parent_id, user_id, body, score, depth, created_at) VALUES
    (1, 100, NULL, 10, 'Genuinely the best intro out there — the ownership chapter is where it finally clicked for me. Read three other tutorials before this one and none of them made it stick.', 12, 0, DATE_SUB(NOW(), INTERVAL 3 WEEK)),
    (2, 100, 1,    2,  'Same, chapter 4 did it for me too. Ended up re-reading it twice before it actually stuck.', 4, 1, DATE_SUB(NOW(), INTERVAL 3 WEEK)),
    (3, 100, NULL, 11, 'Good, but chapter 8 (collections) assumes you already know a fair bit. Wouldn\'t call it a true zero-to-hero path on its own.', 3, 0, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
    (4, 100, NULL, 9,  'Just started this today, wish me luck!', 0, 0, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
    (5, 1,   NULL, 10, 'Genuinely the best intro out there — the ownership chapter is where it finally clicked for me. Read three other tutorials before this one and none of them made it stick.', 12, 0, DATE_SUB(NOW(), INTERVAL 3 WEEK)),
    (6, 1,   5,    2,  'Same, chapter 4 did it for me too. Ended up re-reading it twice before it actually stuck.', 4, 1, DATE_SUB(NOW(), INTERVAL 3 WEEK)),
    (7, 1,   NULL, 11, 'Good, but chapter 8 (collections) assumes you already know a fair bit. Wouldn\'t call it a true zero-to-hero path on its own.', 3, 0, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
    (8, 1,   NULL, 9,  'Just started this today, wish me luck!', 0, 0, DATE_SUB(NOW(), INTERVAL 1 HOUR));
SQL;



