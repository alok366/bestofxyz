<?php

$sql = "
CREATE TABLE subcategories (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id         SMALLINT UNSIGNED NOT NULL,
    name                VARCHAR(150) NOT NULL,
    slug                VARCHAR(170) NOT NULL,
    description         TEXT,
    status              ENUM('pending', 'live', 'archived') NOT NULL DEFAULT 'pending',
    proposed_by         INT UNSIGNED NOT NULL,
    resource_threshold  TINYINT UNSIGNED NOT NULL DEFAULT 5,
    promoted_at         TIMESTAMP NULL DEFAULT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_subcategories_slug (slug),
    INDEX idx_subcategories_cat_status (category_id, status),

    CONSTRAINT fk_subcategories_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_subcategories_proposer
        FOREIGN KEY (proposed_by) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
