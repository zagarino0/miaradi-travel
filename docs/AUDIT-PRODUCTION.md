# Audit technique — préparation de la version réelle

Date : 2026-10-27

## Objectif

Faire évoluer progressivement la maquette existante vers un site de production hébergé sur OVHcloud, sans remplacer ni modifier le site actuellement en ligne pendant la phase de préparation.

## Périmètre observé

- Dépôt : `zagarino0/miaradi-travel`
- Branche de référence : `main`
- Branche de préparation : `feat/php-production-foundation`
- Frontend : HTML, CSS et JavaScript natifs, sans outil de build obligatoire.
- Pages inspectées : `index.html`, `a-propos.html`.
- Styles : `site.css`.
- Interactions : `script.js`.
- Logo référencé : `assets/miaradi-logo.png`.
- Hébergement cible : OVHcloud Web Hosting, PHP global 8.2, MySQL 8.4 selon les captures du compte.
- Répertoire web observé dans FileZilla : `/home/oxdinuv/www`, contenant actuellement une installation WordPress.

## Constats

### Points réutilisables

1. La page d'accueil comporte déjà le hero, les sections éditoriales, les filtres, les cartes de circuits, les galeries et le formulaire de devis.
2. La page À propos contient déjà la philosophie de MIARADI, son engagement local et des sections responsive.
3. Le CSS porte déjà l'identité visuelle rouge `#922D20` et ivoire `#FBF8F5`.
4. Le JavaScript gère notamment le menu, les filtres, les galeries et la bascule FR/EN.
5. Le formulaire `#quote-form` est intercepté par JavaScript et affiche un message de démonstration : il n'enregistre donc pas encore de demande côté serveur.

### Points à corriger ou à vérifier

1. Le README ne correspond plus entièrement au code actuel : il mentionne notamment `styles.css` alors que la page d'accueil charge `site.css`, et annonce une ancienne palette vert/sable.
2. Les textes, durées, photos et circuits de démonstration doivent être confirmés par l'agence avant publication.
3. La validation navigateur seule ne suffit pas pour un formulaire public. Il faut valider côté PHP, contrôler les données, limiter le spam et utiliser des requêtes SQL préparées.
4. Les tables `miaradi_admin_users`, `miaradi_circuits` et `miaradi_quote_requests` ont été visibles dans phpMyAdmin selon les captures fournies. Leur structure, index, contraintes, encodage et privilèges doivent être inspectés avant toute migration. Ne pas les vider ni les supprimer.
5. Il faut confirmer le domaine final, la configuration SSL, le comportement de PHP 8.2 et la méthode d'envoi d'e-mails disponible sur l'offre OVH.
6. Le site actuellement dans `/home/oxdinuv/www` contient WordPress. Aucun remplacement, suppression ou écrasement de ses fichiers ne doit être effectué pendant cette phase.
7. Ne jamais enregistrer les mots de passe MySQL ou administrateur dans GitHub, ni partager `wp-config.php`. Les secrets de production seront stockés dans une configuration non versionnée et protégée.

## Architecture cible proposée

Conserver les pages et styles actuels, puis ajouter un backend PHP structuré :

- `index.html` et `a-propos.html` : frontend existant, avec adaptations progressives.
- `api/quote.php` : réception et validation des demandes de devis.
- `includes/config.php` : chargement d'une configuration privée, hors dépôt public.
- `includes/db.php` : connexion PDO à MySQL avec erreurs non exposées aux visiteurs.
- `admin/login.php` : authentification administrateur.
- `admin/index.php` : tableau de bord protégé.
- `admin/circuits.php` : gestion des circuits.
- `admin/quotes.php` : consultation et suivi des demandes de devis.
- `database/schema.sql` : schéma documenté, à comparer d'abord avec les tables OVH existantes.
- `docs/DEPLOIEMENT-OVH.md` : procédure de déploiement et de retour arrière.

Cette arborescence est une proposition de travail ; elle ne signifie pas que ces fichiers backend existent déjà.

## Plan de réalisation sans risque

### Étape 1 — Audit et sauvegardes
- Examiner tous les fichiers et les scripts existants.
- Exporter une sauvegarde de la base OVH et sauvegarder les fichiers WordPress.
- Inspecter la structure SQL existante avant de rédiger une migration.
- Documenter les URLs, le domaine et la racine web utilisés.

### Étape 2 — Fondation backend en environnement de préparation
- Ajouter la configuration privée et la connexion PDO.
- Vérifier la connexion sans afficher les identifiants.
- Écrire des tests de validation pour les données du formulaire.
- Ne pas modifier le contenu du dossier web de production.

### Étape 3 — Formulaire de devis réel
- Envoyer les données au endpoint PHP avec POST.
- Ajouter validation serveur, protection anti-spam et réponses d'erreur compréhensibles.
- Enregistrer les demandes en base avec des requêtes préparées.
- Configurer l'e-mail après confirmation des paramètres d'envoi OVH.

### Étape 4 — Administration
- Authentification sécurisée avec mots de passe hachés et sessions protégées.
- Gestion des circuits et consultation des devis.
- Contrôle des autorisations pour toutes les opérations.
- Ne pas utiliser de compte ou mot de passe de démonstration en production.

### Étape 5 — Préproduction et mise en ligne
- Tester sur une copie ou un emplacement de préparation non public.
- Vérifier mobile, FR/EN, formulaires, base, e-mails, erreurs et permissions.
- Déployer après validation explicite, avec sauvegarde et plan de retour arrière.

## Règles de sécurité et de déploiement

- Aucune modification du site OVH en production pendant l'audit.
- Aucun effacement ou remplacement de WordPress sans sauvegarde vérifiée et décision explicite.
- Aucun secret, dump de base ou fichier `wp-config.php` dans Git.
- Ne pas laisser d'archive ZIP contenant le code ou la configuration privée téléchargeable depuis la racine web.
- Garder le code source et l'historique dans GitHub ; déployer uniquement les fichiers nécessaires sur OVH.
- Vérifier les tables existantes avant toute création ou migration SQL.

## Limites de cet audit

Cet audit est basé sur les fichiers consultés dans GitHub et les captures d'écran fournies. La totalité des fichiers du dépôt, les structures SQL détaillées, la configuration effective de PHP, les paramètres e-mail, le domaine final et les droits MySQL n'ont pas encore été vérifiés.
