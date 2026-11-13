-- MIARADI-TRAVEL — rôles d'accès multi-utilisateurs
-- Sauvegarder la base avant exécution. Aucun objet WordPress n'est modifié.
ALTER TABLE miaradi_admin_users
  ADD COLUMN role ENUM('super_admin','manager','editor') NOT NULL DEFAULT 'super_admin' AFTER email;
