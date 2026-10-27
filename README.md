# MIARADI-TRAVEL

Site vitrine de l'agence MIARADI-TRAVEL, consacré aux voyages sur mesure à Madagascar. Le frontend existant est développé en HTML, CSS et JavaScript natifs afin de préserver la maquette et son identité visuelle.

## État du projet

- `main` : maquette frontend publiée via GitHub Pages.
- `feat/php-mysql-foundation` : branche de travail pour la migration progressive vers PHP/MySQL et l'administration OVH.
- Le backend et le schéma SQL de cette branche sont une fondation initiale ; ils ne constituent pas encore une application prête pour la production.
- GitHub Pages ne peut pas exécuter PHP. La version complète devra être déployée sur un hébergement compatible, prévu chez OVHcloud.

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

Le frontend ne nécessite pas d'outil de build.

## Structure

- `index.html` : page d'accueil et sections publiques.
- `a-propos.html` : page À propos.
- `site.css` : styles responsive et identité visuelle.
- `script.js` : interactions, filtres de circuits et bascule FR/EN.
- `assets/miaradi-logo.png` : logo.
- `docs/AUDIT-TECHNIQUE.md` : audit initial et plan de migration.
- `database/schema.sql` : schéma initial MySQL.
- `backend/src/bootstrap.php` : connexion PDO et en-têtes HTTP communs.
- `backend/public/api/health.php` : vérification de disponibilité API/base de données.
- `backend/.env.example` : exemple de variables d'environnement, sans identifiants réels.

## Fonctionnalités frontend actuelles

- Sections Accueil, À propos, Circuits et Contact.
- Filtres de circuits et bascule de langue FR/EN.
- Formulaire avec validation côté navigateur et message de démonstration.

Le formulaire actuel n'enregistre aucune demande et n'envoie aucun e-mail. Les circuits et contenus sont encore définis dans le frontend.

## Identité visuelle

Palette actuelle : rouge `#922D20`, ivoire `#FBF8F5`, tons sombres et neutres. Typographies : DM Sans et Playfair Display.

## Prochaines étapes avant la production

1. Confirmer la version PHP et MySQL/MariaDB disponible chez OVHcloud.
2. Créer la base de données et importer `database/schema.sql`.
3. Développer l'authentification admin, la gestion des circuits et des demandes de devis.
4. Connecter le formulaire public au backend avec validation serveur et protections anti-spam.
5. Configurer l'envoi d'e-mails, les sauvegardes, SSL et les variables d'environnement privées.
6. Tester l'application sur OVH avant toute bascule du site public.

**Sécurité :** ne jamais commiter un fichier `.env` réel, des identifiants de base de données ou un mot de passe administrateur. Le squelette backend actuel est un travail en cours et doit être complété et testé avant une utilisation en production.
