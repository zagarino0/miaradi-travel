# MIARADI-TRAVEL

Site vitrine de l'agence MIARADI-TRAVEL, consacré aux voyages sur mesure à Madagascar. Le frontend existant est développé en HTML, CSS et JavaScript natifs afin de préserver la maquette et son identité visuelle.

## État du projet

- `main` contient le frontend statique et la fondation initiale PHP/MySQL.
- Le socle PHP/MySQL a été fusionné dans `main`, mais ne constitue pas encore une application prête pour la production.
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
- `database/schema.sql` : schéma SQL initial, à réconcilier avec les tables MIARADI déjà présentes sur OVH avant toute importation.
- `docs/AUDIT-TECHNIQUE.md` : audit initial de la maquette.
- `docs/AUDIT-PRODUCTION.md` : notes de préparation de production.
- `docs/AUDIT-SOCLE-PHP-MYSQL.md` : audit du socle PHP/MySQL après fusion dans `main`.

## Fonctionnalités frontend actuelles

- Sections Accueil, À propos, Circuits et Contact.
- Filtres de circuits et bascule de langue FR/EN.
- Fiches détaillées de circuits côté navigateur.
- Formulaire avec validation côté navigateur et message de démonstration.

Le formulaire actuel n'enregistre aucune demande et n'envoie aucun e-mail. Les circuits et contenus restent définis dans le frontend ; leur chargement depuis l'API n'est pas encore implémenté.

## Identité visuelle

Palette actuelle : rouge `#922D20`, ivoire `#FBF8F5`, tons sombres et neutres. Typographies : DM Sans et Playfair Display. Toute évolution backend doit préserver le design et le comportement responsive existants.

## Prochaines étapes avant la production

1. Exporter la structure (sans données) des tables existantes `miaradi_admin_users`, `miaradi_circuits` et `miaradi_quote_requests` depuis phpMyAdmin.
2. Comparer ces structures à `database/schema.sql` et préparer une migration adaptée. **Ne pas importer le schéma actuel directement dans la base OVH** avant cette vérification.
3. Tester le backend en environnement isolé avec une version compatible de PHP et MySQL.
4. Développer l'API des circuits, puis l'enregistrement réel des demandes de devis.
5. Développer l'authentification et l'administration avec contrôle d'accès, sessions sécurisées et protection CSRF.
6. Configurer les e-mails, les sauvegardes, SSL et les variables d'environnement privées.
7. Tester l'application et prévoir une procédure de retour arrière avant tout déploiement OVH.

**Sécurité :** ne jamais commiter un fichier `.env` réel, des identifiants de base de données, un dump de production ou un mot de passe administrateur. Le backend est un socle de développement et doit être complété et testé avant toute utilisation en production.
