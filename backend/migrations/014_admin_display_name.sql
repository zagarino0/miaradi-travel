-- MIARADI-TRAVEL — nom affiché du compte administrateur
-- À exécuter après avoir sauvegardé la base, avant d'utiliser register.php.
-- Ne modifie aucune table WordPress.
ALTER TABLE miaradi_admin_users
    ADD COLUMN display_name VARCHAR(180) NOT NULL DEFAULT '' AFTER id;
