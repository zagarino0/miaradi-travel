-- MIARADI-TRAVEL gallery media
-- Apply to the local/test database before using the gallery endpoints.
CREATE TABLE IF NOT EXISTS miaradi_gallery_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    media_type ENUM('image','video') NOT NULL,
    source_type ENUM('upload','youtube','vimeo') NOT NULL DEFAULT 'upload',
    title_fr VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) NULL,
    description_fr TEXT NULL,
    description_en TEXT NULL,
    alt_text VARCHAR(250) NULL,
    file_path VARCHAR(500) NULL,
    external_url VARCHAR(1000) NULL,
    thumbnail_path VARCHAR(500) NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_gallery_public (is_published, sort_order, id),
    KEY idx_gallery_type (media_type, source_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
