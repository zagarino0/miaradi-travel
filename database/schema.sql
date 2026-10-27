-- MIARADI-TRAVEL — schéma de référence
-- Cible observée dans les exports de structure OVH du 27/10/2026.
-- MySQL 8.4 / InnoDB / utf8mb4.
--
-- ATTENTION :
-- Ce fichier décrit les tables MIARADI avec leurs noms réels.
-- Ne pas l'importer aveuglément dans une base existante : CREATE TABLE
-- IF NOT EXISTS ne met pas à jour les tables déjà présentes.
-- Comparer toute évolution avec une sauvegarde avant de migrer.
-- Les tables WordPress mod141_* ne sont pas concernées.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS miaradi_admin_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    password_hash VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_miaradi_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS miaradi_circuits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(180) COLLATE utf8mb4_unicode_ci NOT NULL,
    title_fr VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    title_en VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    summary_fr TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    summary_en TEXT COLLATE utf8mb4_unicode_ci,
    region VARCHAR(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    theme VARCHAR(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    duration_min SMALLINT UNSIGNED DEFAULT NULL,
    duration_max SMALLINT UNSIGNED DEFAULT NULL,
    image_path VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_miaradi_circuits_slug (slug),
    KEY idx_miaradi_circuits_public (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS miaradi_quote_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(180) COLLATE utf8mb4_unicode_ci NOT NULL,
    email VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    desired_departure_date DATE DEFAULT NULL,
    traveler_count SMALLINT UNSIGNED DEFAULT NULL,
    circuit_id BIGINT UNSIGNED DEFAULT NULL,
    message TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    preferred_language CHAR(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fr',
    status VARCHAR(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
    privacy_consent_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_miaradi_quotes_status (status, created_at),
    KEY idx_miaradi_quotes_email (email),
    KEY fk_miaradi_quotes_circuit (circuit_id),
    CONSTRAINT fk_miaradi_quotes_circuit
        FOREIGN KEY (circuit_id) REFERENCES miaradi_circuits(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
