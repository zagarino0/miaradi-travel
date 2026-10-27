# Audit du socle PHP/MySQL — MIARADI-TRAVEL

Date : 2026-10-27  
Branche auditée : `main`  
Commit audité : `a4cf5db3f76e9e3ac1800c9311ffc9f48a0fb7ce`  
Périmètre : lecture du dépôt GitHub uniquement. Aucun accès direct au serveur OVH, aucune écriture dans phpMyAdmin et aucun déploiement.

## 1. Résumé

Le socle PHP/MySQL est présent dans `main`, mais il est encore minimal. Il ne constitue pas une application prête pour la production. Le frontend existant reste une maquette statique ; son design doit être conservé et raccordé progressivement au backend.

## 2. Constats

### Bloquant avant connexion à la base existante — noms des tables divergents

Le fichier `database/schema.sql` crée les tables `admin_users`, `circuits` et `quote_requests`.

Les captures phpMyAdmin partagées pour la base OVH montrent plutôt `miaradi_admin_users`, `miaradi_circuits` et `miaradi_quote_requests`, en plus des tables WordPress `mod141_*`.

Ces deux ensembles ne sont pas équivalents. Le schéma du dépôt ne doit pas être importé tel quel dans la base OVH avant vérification des colonnes, index et contraintes des trois tables `miaradi_*`. Les structures complètes n'ont pas été fournies dans les captures consultées.

### Backend PHP

- `backend/src/bootstrap.php` utilise PDO, les requêtes préparées étant activées par défaut via `PDO::ATTR_EMULATE_PREPARES => false`.
- La configuration est lue avec `getenv()`. Aucun chargeur de fichier `.env` n'est inclus ; l'environnement d'exécution devra fournir les variables ou un mécanisme privé de configuration.
- Le bootstrap définit certains en-têtes de sécurité et paramètres de cookie, mais n'implémente pas encore l'authentification, les autorisations, la protection CSRF ni les routes métier.
- Le paramètre `secure` du cookie de session dépend de `$_SERVER['HTTPS']`. Ce comportement devra être vérifié derrière la configuration réelle du proxy/hébergement OVH.
- `backend/public/api/health.php` vérifie la connexion à la base avec `SELECT 1` et ne renvoie pas les détails d'exception au client. C'est un contrôle de connectivité, pas une preuve que le schéma applicatif ou les flux métier fonctionnent.

### Schéma SQL

- Le schéma prévoit trois entités de base : administrateurs, circuits et demandes de devis.
- Les mots de passe sont prévus sous forme de hachage dans `password_hash`, mais aucun flux de création d'administrateur ou d'authentification n'est implémenté.
- Le schéma contient des contraintes de clé étrangère et de statut ; elles doivent être comparées aux tables déjà créées dans OVH avant toute migration.
- Les tables WordPress existantes doivent rester intactes.

### Frontend

- `index.html`, `a-propos.html`, `site.css` et `script.js` forment toujours le frontend statique.
- Le formulaire de devis affiche encore un message de démonstration et ne persiste pas les demandes dans MySQL.
- Les circuits et leurs détails sont codés dans le JavaScript, et ne sont pas encore chargés depuis l'API.
- La migration doit préserver l'identité visuelle existante, notamment le rouge `#922D20` et l'ivoire `#FBF8F5`.

### Documentation

Le README indique encore que `feat/php-mysql-foundation` est la branche de travail, alors que la fondation a été fusionnée dans `main`. Cette référence doit être actualisée.

## 3. Compatibilité observée

Les captures de l'espace OVH indiquent PHP global 8.2 et MySQL 8.4. Cette information provient des captures fournies ; la configuration réellement utilisée par le dossier web et la connexion SQL PHP n'a pas été testée. Le backend doit être validé dans un environnement isolé correspondant à PHP 8.2 et MySQL 8.4 avant toute mise en ligne.

## 4. Plan d'action priorisé

1. **Vérifier la structure réelle des tables existantes** : exporter uniquement leur structure depuis phpMyAdmin pour `miaradi_admin_users`, `miaradi_circuits` et `miaradi_quote_requests`. Ne pas supprimer, vider ou recréer ces tables.
2. **Réconcilier le schéma** : choisir explicitement les noms et colonnes de référence à partir de l'export. Écrire ensuite une migration idempotente et documentée ; ne pas lancer le schéma actuel directement sur la base existante.
3. **Tester le backend isolément** : vérifier la syntaxe PHP, la configuration PDO et l'endpoint de santé avec des identifiants de test, sans toucher à OVH.
4. **Implémenter l'API des circuits** : lecture publique des seuls circuits publiés, validation et réponses d'erreur cohérentes.
5. **Implémenter l'envoi de devis** : validation côté serveur, requêtes préparées, protection anti-spam, consentement de confidentialité et confirmation claire. Tester l'enregistrement avant de connecter le formulaire public.
6. **Créer l'administration** : authentification sécurisée, hachage des mots de passe, régénération d'identifiant de session, contrôle d'accès et protection CSRF avant les fonctions de gestion.
7. **Tester et documenter** : tests automatisés et manuels, sauvegarde vérifiée, procédure de retour arrière et guide de déploiement.
8. **Déployer uniquement après validation explicite** : aucune modification du site OVH ou de WordPress avant la fin des tests et l'accord de mise en production.

## 5. Décision

**État : fondation initiale, non prête pour la production.**

Aucune modification de la base OVH, aucun déploiement et aucune modification des fichiers web OVH ne sont inclus dans cet audit. La prochaine action requise côté données est de fournir un export SQL de structure (sans lignes ni données personnelles) des trois tables MIARADI existantes.
