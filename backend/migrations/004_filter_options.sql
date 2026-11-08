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

-- Valeurs de départ équivalentes aux filtres déjà présents sur la page publique.
INSERT IGNORE INTO miaradi_filter_options (option_type, option_value, option_label, sort_order) VALUES
('region', 'west', 'Ouest', 10),
('region', 'north', 'Nord & îles', 20),
('region', 'east', 'Est', 30),
('region', 'south', 'Sud', 40),
('region', 'highlands', 'Hautes Terres', 50),
('theme', 'nature', 'Nature', 10),
('theme', 'coast', 'Côte & îles', 20),
('theme', 'culture', 'Culture', 30),
('duration', '2-4', '2–4 jours', 10),
('duration', '3-5', '3–5 jours', 20),
('duration', '3-6', '3–6 jours', 30),
('duration', '5-8', '5–8 jours', 40),
('duration', '7-12', '7–12 jours', 50),
('duration', 'custom', 'Sur mesure', 60);
