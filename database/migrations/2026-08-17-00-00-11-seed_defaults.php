<?php

$sql = "
INSERT INTO tags (name) VALUES
    ('free'), ('paid'), ('video'), ('book'), ('interactive'),
    ('tool'), ('official'), ('exercises'), ('beginner'), ('advanced'),
    ('tutorial');

INSERT INTO categories (name, slug, icon, description, display_order) VALUES
    ('Programming',           'programming',           '💻', 'Languages, frameworks, and developer tools',        1),
    ('AI & Machine Learning', 'ai-machine-learning',   '🤖', 'Machine learning, deep learning, and AI tools',     2),
    ('System Design',         'system-design',          '🏗️', 'Architecture patterns and scalability concepts',     3),
    ('DevOps & Cloud',        'devops-cloud',           '☁️',  'CI/CD, containers, and cloud infrastructure',       4),
    ('Web Development',       'web-development',        '🌐', 'Frontend, backend, and full-stack web development',  5),
    ('Databases & Storage',   'databases-storage',      '🗄️', 'SQL, NoSQL, and data management fundamentals',      6);
";
