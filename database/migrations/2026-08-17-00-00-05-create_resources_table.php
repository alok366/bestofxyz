<?php

$sql = "
CREATE TABLE resources (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subcategory_id  INT UNSIGNED NOT NULL,
    submitted_by    INT UNSIGNED NOT NULL,
    title           VARCHAR(200) NOT NULL,
    slug            VARCHAR(220) NOT NULL,
    url             VARCHAR(2048) NOT NULL,
    url_hash        BINARY(32) NOT NULL,
    host            VARCHAR(255) NOT NULL DEFAULT '',
    description     VARCHAR(500) NOT NULL DEFAULT '',
    score           INT NOT NULL DEFAULT 0,
    hot_score       DOUBLE NOT NULL DEFAULT 0.0,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_resources_sub_url (subcategory_id, url_hash),
    UNIQUE KEY uq_resources_sub_slug (subcategory_id, slug),
    INDEX idx_resources_sub_score (subcategory_id, score DESC),
    INDEX idx_resources_sub_created (subcategory_id, created_at DESC),
    INDEX idx_resources_sub_hot (subcategory_id, hot_score DESC),

    CONSTRAINT fk_resources_subcategory
        FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_resources_submitter
        FOREIGN KEY (submitted_by) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
