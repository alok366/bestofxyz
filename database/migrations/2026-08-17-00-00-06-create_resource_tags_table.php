<?php

$sql = "
CREATE TABLE resource_tags (
    resource_id     INT UNSIGNED NOT NULL,
    tag_id          SMALLINT UNSIGNED NOT NULL,

    PRIMARY KEY (resource_id, tag_id),
    INDEX idx_resource_tags_reverse (tag_id, resource_id),

    CONSTRAINT fk_resource_tags_resource
        FOREIGN KEY (resource_id) REFERENCES resources(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_resource_tags_tag
        FOREIGN KEY (tag_id) REFERENCES tags(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
