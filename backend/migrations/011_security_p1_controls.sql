-- MIARADI-TRAVEL — protections de sécurité P1
-- À exécuter une seule fois après les migrations précédentes.
ALTER TABLE miaradi_admin_users
    ADD COLUMN auth_version BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER is_active;

CREATE TABLE IF NOT EXISTS miaradi_api_rate_limits (
    bucket_key CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    scope VARCHAR(32) NOT NULL,
    hits INT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL,
    blocked_until DATETIME DEFAULT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (bucket_key),
    KEY idx_miaradi_rate_limits_updated (updated_at),
    KEY idx_miaradi_rate_limits_scope (scope, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
