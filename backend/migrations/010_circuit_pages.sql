-- MIARADI-TRAVEL — pages détaillées administrables pour les circuits
CREATE TABLE IF NOT EXISTS miaradi_circuit_pages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    circuit_id BIGINT UNSIGNED NOT NULL,
    hero_title_fr VARCHAR(220) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    hero_title_en VARCHAR(220) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    intro_fr TEXT COLLATE utf8mb4_unicode_ci,
    intro_en TEXT COLLATE utf8mb4_unicode_ci,
    hero_image VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    itinerary_json LONGTEXT COLLATE utf8mb4_bin NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_miaradi_circuit_pages_circuit (circuit_id),
    CONSTRAINT fk_miaradi_circuit_pages_circuit
      FOREIGN KEY (circuit_id) REFERENCES miaradi_circuits(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
