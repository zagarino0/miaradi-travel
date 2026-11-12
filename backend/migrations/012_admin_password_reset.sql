-- MIARADI-TRAVEL — jetons temporaires de réinitialisation du mot de passe admin
CREATE TABLE IF NOT EXISTS miaradi_admin_password_resets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id BIGINT UNSIGNED NOT NULL,
    email_hash CHAR(64) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    request_ip_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_miaradi_password_reset_token (token_hash),
    KEY idx_miaradi_password_reset_email_time (email_hash, created_at),
    KEY idx_miaradi_password_reset_ip_time (request_ip_hash, created_at),
    KEY idx_miaradi_password_reset_expiry (expires_at),
    CONSTRAINT fk_miaradi_password_reset_admin
      FOREIGN KEY (admin_id) REFERENCES miaradi_admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
