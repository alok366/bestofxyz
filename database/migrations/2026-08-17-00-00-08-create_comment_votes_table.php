<?php

$sql = "
CREATE TABLE comment_votes (
    comment_id      INT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    vote_type       TINYINT NOT NULL,
    ip_hash         BINARY(32) DEFAULT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (comment_id, user_id),

    CONSTRAINT chk_comment_votes_type CHECK (vote_type IN (1, -1)),

    CONSTRAINT fk_comment_votes_comment
        FOREIGN KEY (comment_id) REFERENCES comments(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_comment_votes_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
