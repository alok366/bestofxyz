<?php

$sql = "
CREATE TABLE categories (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id           INT UNSIGNED NULL DEFAULT NULL,
    name                VARCHAR(150) NOT NULL,
    slug                VARCHAR(170) NOT NULL,
    icon                VARCHAR(10) NOT NULL DEFAULT '',
    description         TEXT,
    status              ENUM('pending', 'live', 'archived') NOT NULL DEFAULT 'live',
    proposed_by         INT UNSIGNED NULL DEFAULT NULL,
    resource_threshold  TINYINT UNSIGNED NOT NULL DEFAULT 5,
    promoted_at         TIMESTAMP NULL DEFAULT NULL,
    display_order       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_categories_slug (slug),
    INDEX idx_categories_parent_status (parent_id, status),
    INDEX idx_categories_status (status),

    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_categories_proposer
        FOREIGN KEY (proposed_by) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

