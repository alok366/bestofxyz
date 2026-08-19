<?php

$sql = "
CREATE TABLE votes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id     INT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    vote_type       TINYINT NOT NULL,
    ip_hash         BINARY(32) DEFAULT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_votes_resource_user (resource_id, user_id),
    INDEX idx_votes_user_resource (user_id, resource_id),

    CONSTRAINT chk_votes_type CHECK (vote_type IN (1, -1)),

    CONSTRAINT fk_votes_resource
        FOREIGN KEY (resource_id) REFERENCES resources(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_votes_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
