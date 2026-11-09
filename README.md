# MIARADI-TRAVEL

Site vitrine de l'agence MIARADI-TRAVEL, consacré aux voyages sur mesure à Madagascar. Le frontend existant est développé en HTML, CSS et JavaScript natifs afin de préserver la maquette et son identité visuelle.

## État du projet

- `main` contient le frontend statique et la fondation initiale PHP/MySQL.
- Le socle PHP/MySQL n'est pas encore une application prête pour la production.
- Le site publié via GitHub Pages reste statique. GitHub Pages ne peut pas exécuter PHP ; la future version complète devra être déployée sur un hébergement compatible, prévu chez OVHcloud.
- Aucun déploiement OVH n'est effectué par le dépôt ou par cette documentation.

## Aperçu local du frontend

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/zagarino0/miaradi-travel.git
   cd miaradi-travel
   ```
2. Lancer un serveur HTTP local :
   ```bash
   python -m http.server 5500
   ```
3. Ouvrir http://localhost:5500.

Le frontend statique ne nécessite pas d'outil de build. Ce serveur Python ne permet pas d'exécuter le backend PHP.

## Structure

- `index.html` : page d'accueil et sections publiques.
- `a-propos.html` : page À propos.
- `site.css` : styles responsive et identité visuelle.
- `script.js` : interactions, filtres, fiches circuits et bascule FR/EN.
- `assets/miaradi-logo.png` : logo.
- `backend/src/bootstrap.php` : socle PDO et en-têtes HTTP communs.
- `backend/public/api/health.php` : contrôle de connectivité API/base de données.
- `backend/.env.example` : exemple de variables d'environnement, sans identifiants réels.
- `database/schema.sql` : schéma de référence aligné sur les structures SQL fournies pour les trois tables MIARADI existantes.
- `docs/AUDIT-TECHNIQUE.md` : audit initial de la maquette.
- `docs/AUDIT-PRODUCTION.md` : notes de préparation de production.
- `docs/AUDIT-SOCLE-PHP-MYSQL.md` : comparaison du socle PHP/MySQL avec les exports de structure.

## Fonctionnalités frontend actuelles

- Sections Accueil, À propos, Circuits et Contact.
- Filtres de circuits et bascule de langue FR/EN.
- Fiches détaillées de circuits côté navigateur.
- Formulaire avec validation côté navigateur et message de démonstration.

Le formulaire actuel n'enregistre aucune demande et n'envoie aucun e-mail. Les circuits et contenus restent définis dans le frontend ; leur chargement depuis l'API n'est pas encore implémenté.

## Identité visuelle

Palette actuelle : rouge `#922D20`, ivoire `#FBF8F5`, tons sombres et neutres. Typographies : DM Sans et Playfair Display. Toute évolution backend doit préserver le design et le comportement responsive existants.

## Prochaines étapes avant la production

1. Tester le backend dans un environnement isolé avec une version compatible de PHP et MySQL.
2. Développer l'API des circuits en lecture seule pour les circuits publiés.
3. Développer l'enregistrement des demandes de devis, avec validation serveur et protection anti-spam.
4. Développer l'authentification et l'administration avec contrôle d'accès, sessions sécurisées et protection CSRF.
5. Configurer les e-mails, les sauvegardes, SSL et les variables d'environnement privées.
6. Tester l'application et prévoir une procédure de retour arrière avant tout déploiement OVH.

**Attention :** `database/schema.sql` est un schéma de référence, pas une migration de production. `CREATE TABLE IF NOT EXISTS` ne modifie pas les tables déjà existantes. Ne pas l'importer aveuglément dans OVH. Les tables WordPress `mod141_*` ne sont pas concernées.

**Sécurité :** ne jamais commiter un fichier `.env` réel, des identifiants de base de données, un dump de production ou un mot de passe administrateur. Le backend est un socle de développement et doit être complété et testé avant toute utilisation en production.


## Photos de la section « Réseau local »

- Administration : `backend/public/admin/network.php` (accessible après connexion admin).
- API publique : `backend/public/api/partners.php`.
- Migration SQL : `backend/migrations/005_partner_media.sql`.
- Formats autorisés : JPG, PNG, WebP ; 8 Mo maximum par image.

Appliquer la migration `005_partner_media.sql` dans la base MIARADI avant d'utiliser l'écran d'administration. GitHub Pages ne peut pas exécuter les endpoints PHP : cette fonction nécessite l'hébergement PHP/MySQL ou un environnement local XAMPP.

## Galerie régionale panoramique

La section d'accueil « Madagascar en images » est alimentée par les médias publiés dans la galerie admin. Pour l'activer :

1. Appliquer les migrations dans l'ordre : `001_gallery.sql`, `002_gallery_circuit.sql`, puis `006_gallery_region.sql` (la migration `005_partner_media.sql` reste indépendante pour la section des rencontres).
2. Dans l'administration, ouvrir **Galerie médias**, puis ajouter ou modifier une photo.
3. Choisir une **région** parmi les valeurs actives de **Paramètres du catalogue**, définir l'ordre d'affichage et cocher **Publier sur le site**.
4. Répéter pour plusieurs photos d'une même région afin d'activer le slider automatique (intervalle de 6,5 secondes). Les flèches permettent également de naviguer manuellement.

L'API publique `backend/public/api/gallery.php` conserve le fonctionnement de la galerie complète et accepte également `?region=<option_value>` pour filtrer une région. Les panoramas de la page d'accueil affichent jusqu'à quatre régions disposant d'images publiées. Les médias non publiés et les vidéos ne sont pas utilisés dans les panoramas.
