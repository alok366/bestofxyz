<?php

$sql = "
CREATE TABLE resource_rank_snapshots (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id         INT UNSIGNED NOT NULL,
    subcategory_id      INT UNSIGNED NOT NULL,
    rank                SMALLINT UNSIGNED NOT NULL,
    score_at_snapshot   INT NOT NULL,
    snapshot_date       DATE NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_snapshots_resource_date (resource_id, snapshot_date),
    INDEX idx_snapshots_sub_date (subcategory_id, snapshot_date),

    CONSTRAINT fk_snapshots_resource
        FOREIGN KEY (resource_id) REFERENCES resources(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_snapshots_subcategory
        FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
