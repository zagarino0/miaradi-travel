-- MIARADI-TRAVEL — options administrables des filtres publics
CREATE TABLE IF NOT EXISTS miaradi_filter_options (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    option_type ENUM('region','theme','duration') NOT NULL,
    option_value VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    option_label VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_miaradi_filter_option (option_type, option_value),
    KEY idx_miaradi_filter_options (option_type, is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
