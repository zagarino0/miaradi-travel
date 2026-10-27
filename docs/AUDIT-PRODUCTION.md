# Audit technique — préparation de la version réelle

Date : 2026-10-27

## Objectif
Faire évoluer la maquette existante vers un site de production hébergé sur OVHcloud sans remplacement prématuré du site actuel.

## Références
- Dépôt : https://github.com/zagarino0/miaradi-travel
- Branche de référence : `main`
- Branche de préparation : `feat/php-production-foundation`
- Environnement observé dans les captures : OVH Web Hosting, PHP global 8.2, MySQL 8.4
- Répertoire web vu dans FileZilla : `/home/oxdinuv/www`, contenant WordPress.

## État réellement présent dans la branche lors de la vérification du 27 octobre 2026
Arborescence vérifiée via l'API Git Trees au commit `3d81b882c7e8bcc9286787b8f4155dbd2b2525f5` :
- `README.md`
- `a-propos.html`
- `assets/miaradi-logo.png`
- `docs/AUDIT-PRODUCTION.md`
- `index.html`
- `script.js`
- `site.css`

Les dossiers `backend/` et `database/` ne sont pas dans cet arbre et les requêtes GitHub Contents pour ces dossiers renvoient 404. Une capture fournie montre une arborescence différente et un état à 7 commits devant `main`, alors que l'API résout actuellement la branche à 1 commit devant `main`. Cette incohérence doit être considérée explicitement ; ne pas supposer que les fichiers PHP/SQL préparés précédemment se trouvent encore sur cette branche.

## Maquette réutilisable
- `index.html` : accueil, hero, filtres, cartes de circuits, galeries et formulaire.
- `a-propos.html` : philosophie MIARADI, engagement local, partenaires et FAQ.
- `site.css` : identité visuelle rouge `#922D20` et ivoire `#FBF8F5`.
- `script.js` : interactions du menu, filtres, galerie et traductions FR/EN.
- `assets/miaradi-logo.png` : logo.
- Aucun framework/build frontend requis.

## Limites de la maquette
- Le formulaire `#quote-form` est traité par JavaScript comme une démonstration ; il n'enregistre pas les demandes côté serveur.
- Les circuits affichés sont intégrés au frontend.
- Le README est obsolète : il évoque `styles.css` et une palette vert/sable alors que le code charge `site.css` et utilise rouge/ivoire.
- Les textes et images doivent être validés avant publication.

## Base MySQL de production déjà manipulée manuellement
Les captures phpMyAdmin montrent les tables `miaradi_admin_users`, `miaradi_circuits` et `miaradi_quote_requests` dans la base `oxdinuv808`, aux côtés des tables WordPress `mod141_*`. Les trois tables MIARADI étaient affichées avec zéro ligne. Leur structure détaillée (colonnes, contraintes, index) n'est pas encore vérifiée.
**Ne pas importer de nouveau schéma, vider ou supprimer de table avant d'avoir exporté la base et vérifié la structure existante.** Toute migration doit utiliser des noms préfixés MIARADI et être compatible avec les tables existantes.

## Sécurité et séparation des environnements
- Ne pas modifier `main`, l'hébergement OVH ou les fichiers WordPress durant l'audit.
- Ne jamais versionner des identifiants, un `.env` réel, un dump de base ou `wp-config.php`.
- Configuration de production privée, stockée en dehors des fichiers web accessibles publiquement si l'hébergement le permet.
- Le ZIP source ne doit pas rester téléchargeable dans la racine web.
- Sauvegarder les fichiers web et exporter la base avant déploiement.

## Prochaines étapes
1. Régler la divergence de l'arborescence Git et confirmer la branche réellement utilisée.
2. Exporter uniquement la structure des trois tables MIARADI depuis phpMyAdmin et vérifier la sauvegarde de la base WordPress.
3. Ajouter sur une branche dédiée `backend/` (configuration PDO, validation d'entrées) et `database/` (schéma/documentation alignés sur les tables actuelles).
4. Implémenter l'enregistrement réel des devis avec validation serveur, requêtes SQL préparées, contrôle anti-spam et retour utilisateur explicite.
5. Construire l'administration : authentification avec mots de passe hachés, sessions sécurisées, contrôle d'accès, gestion des circuits et demandes.
6. Tester en environnement isolé (PHP 8.2/MySQL 8.4) avant toute copie vers OVH.
7. Déployer seulement après sauvegarde vérifiée et validation explicite.

## Limites
Cet audit s'appuie sur les fichiers effectivement listés via GitHub à la date du rapport et les captures fournies par l'utilisateur. Le domaine final, SSL, mail sortant OVH, privilèges SQL, structure exacte des tables et le contenu actuel du répertoire web ne sont pas entièrement validés.
