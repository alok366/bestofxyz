<?php

$sql = "
CREATE TABLE comments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id     INT UNSIGNED NOT NULL,
    parent_id       INT UNSIGNED DEFAULT NULL,
    user_id         INT UNSIGNED NOT NULL,
    body            TEXT NOT NULL,
    score           INT NOT NULL DEFAULT 0,
    depth           TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_comments_resource (resource_id, created_at),
    INDEX idx_comments_parent (parent_id),

    CONSTRAINT fk_comments_resource
        FOREIGN KEY (resource_id) REFERENCES resources(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_comments_parent
        FOREIGN KEY (parent_id) REFERENCES comments(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT chk_comments_depth CHECK (depth <= 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
