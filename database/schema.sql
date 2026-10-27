-- MIARADI-TRAVEL initial schema
-- MySQL 8+ / compatible MariaDB versions. Review the OVH database version before production.
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS admin_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS circuits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(180) NOT NULL,
    title_fr VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) NULL,
    summary_fr TEXT NOT NULL,
    summary_en TEXT NULL,
    region VARCHAR(80) NULL,
    theme VARCHAR(80) NULL,
    duration_min SMALLINT UNSIGNED NULL,
    duration_max SMALLINT UNSIGNED NULL,
    image_path VARCHAR(500) NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_circuits_slug (slug),
    KEY idx_circuits_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(180) NOT NULL,
    email VARCHAR(254) NOT NULL,
    desired_departure_date DATE NULL,
    traveler_count SMALLINT UNSIGNED NULL,
    circuit_id BIGINT UNSIGNED NULL,
    message TEXT NOT NULL,
    preferred_language CHAR(2) NOT NULL DEFAULT 'fr',
    status VARCHAR(24) NOT NULL DEFAULT 'new',
    privacy_consent_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_quote_requests_status_created (status, created_at),
    KEY idx_quote_requests_email (email),
    CONSTRAINT fk_quote_requests_circuit
        FOREIGN KEY (circuit_id) REFERENCES circuits(id)
        ON DELETE SET NULL,
    CONSTRAINT chk_quote_requests_status
        CHECK (status IN ('new', 'in_progress', 'answered', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
