# Audit du socle PHP/MySQL — MIARADI-TRAVEL

Date : 2026-10-27  
Branche : `main`  
Périmètre : dépôt GitHub et trois exports SQL de structure transmis. Aucun accès direct au serveur OVH, aucune écriture dans phpMyAdmin et aucun déploiement.

## 1. Résumé

Le socle PHP/MySQL est minimal et n'est pas prêt pour la production. Les exports SQL ont permis de comparer les tables déjà créées avec `database/schema.sql`. Le schéma du dépôt a été corrigé pour reprendre les noms, colonnes, index et relation observés dans les exports.

## 2. Comparaison du schéma SQL

### `miaradi_admin_users`

Colonnes observées : `id`, `email`, `password_hash`, `is_active`, `last_login_at`, `created_at`, `updated_at`.  
Contraintes/index : clé primaire sur `id`, index unique `uq_miaradi_admin_email` sur `email`.

### `miaradi_circuits`

Colonnes observées : `id`, `slug`, `title_fr`, `title_en`, `summary_fr`, `summary_en`, `region`, `theme`, `duration_min`, `duration_max`, `image_path`, `is_published`, `sort_order`, `created_at`, `updated_at`.  
Contraintes/index : clé primaire sur `id`, index unique `uq_miaradi_circuits_slug` sur `slug`, index `idx_miaradi_circuits_public` sur `is_published, sort_order`.

### `miaradi_quote_requests`

Colonnes observées : `id`, `full_name`, `email`, `desired_departure_date`, `traveler_count`, `circuit_id`, `message`, `preferred_language`, `status`, `privacy_consent_at`, `created_at`, `updated_at`.  
Contraintes/index : clé primaire sur `id`, index `idx_miaradi_quotes_status` sur `status, created_at`, index `idx_miaradi_quotes_email` sur `email`, index sur `circuit_id`, clé étrangère `fk_miaradi_quotes_circuit` vers `miaradi_circuits(id)` avec `ON DELETE SET NULL`.

Les trois exports utilisent InnoDB, `utf8mb4_unicode_ci`, et indiquent MySQL 8.4.11. Les exports ne contiennent pas de données métier.

### Correction appliquée dans le dépôt

`database/schema.sql` a été mis à jour pour utiliser les noms préfixés exacts (`miaradi_*`) et les noms d'index observés. Le `CHECK` sur `status` présent dans l'ancien schéma du dépôt a été retiré, car il ne figure pas dans l'export de la table existante. Le schéma correspond maintenant aux structures fournies, à la différence des commentaires et du contexte d'installation.

**Important :** `CREATE TABLE IF NOT EXISTS` n'altère pas les tables existantes. Ce fichier est une référence de structure, pas une migration à exécuter en production. Ne pas l'importer aveuglément sur OVH. Les tables WordPress `mod141_*` ne doivent pas être modifiées.

## 3. Audit PHP

- `backend/src/bootstrap.php` utilise PDO avec `PDO::ERRMODE_EXCEPTION`, résultats associatifs et requêtes préparées natives (`PDO::ATTR_EMULATE_PREPARES = false`).
- La configuration est lue par `getenv()`. Aucun chargeur de fichier `.env` n'est implémenté ; les variables doivent être fournies par l'environnement ou une configuration privée appropriée.
- Le bootstrap ne contient pas de requêtes métier vers les tables MIARADI : aucune route PHP de circuits, devis ou administration n'a encore été créée.
- `backend/public/api/health.php` exécute seulement `SELECT 1`. Il teste la connectivité, pas l'existence ni la compatibilité des tables.
- Aucun flux de connexion admin, contrôle d'accès, gestion des circuits, enregistrement de devis ou envoi d'e-mail n'est implémenté.

## 4. Frontend

Le site reste une maquette statique HTML/CSS/JavaScript. Le formulaire de devis utilise encore un retour de démonstration côté navigateur ; il n'enregistre pas les demandes dans MySQL. Les circuits restent définis dans le frontend. La suite devra préserver le design rouge `#922D20` et ivoire `#FBF8F5`.

## 5. Étapes suivantes

1. Tester la syntaxe et la connectivité PHP dans un environnement local isolé avec une base de test.
2. Ajouter un endpoint public en lecture seule pour les circuits publiés, en utilisant `miaradi_circuits`.
3. Ajouter l'enregistrement des devis dans `miaradi_quote_requests`, avec validation serveur, requêtes préparées, limitation anti-spam et consentement.
4. Ajouter ensuite l'authentification et les opérations d'administration sur `miaradi_admin_users`, avec mots de passe hachés, sessions sécurisées, protection CSRF et contrôle d'accès.
5. Créer des tests automatisés couvrant les colonnes et les comportements de ces tables.
6. Tester en environnement isolé avant de préparer une procédure de déploiement OVH avec sauvegarde et retour arrière.

## 6. État de sécurité et déploiement

**Statut : fondation initiale, non prête pour la production.** Aucun déploiement OVH, aucune modification de la base OVH et aucune modification des fichiers web n'ont été effectués. Les identifiants de production ne doivent jamais être committés dans GitHub.
