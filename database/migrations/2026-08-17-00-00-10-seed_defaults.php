<?php

$sql = "
INSERT INTO tags (name) VALUES
    ('free'), ('paid'), ('video'), ('book'), ('interactive'),
    ('tool'), ('official'), ('exercises'), ('beginner'), ('advanced'),
    ('tutorial');

INSERT INTO categories (parent_id, name, slug, icon, description, status, display_order) VALUES
    (NULL, 'Programming',           'programming',           '💻', 'Languages, frameworks, and developer tools',        'live', 1),
    (NULL, 'Design Tools',          'design-tools',          '🎨', 'UI/UX, icons, color palettes, and assets',          'live', 2),
    (NULL, 'Data Science',          'data-science',          '📊', 'Machine learning, datasets, and analytics',         'live', 3),
    (NULL, 'Productivity',          'productivity',          '⚡', 'Note-taking, task managers, and time tracking',     'live', 4),
    (NULL, 'Marketing',             'marketing',             '📢', 'SEO, newsletters, and landing page builders',       'live', 5),
    (NULL, 'No-Code Tools',         'no-code-tools',         '🧩', 'Automation, databases, and website builders',       'live', 6);
";

