# MIARADI-TRAVEL — Maquette statique

Première maquette du site vitrine de l'agence MIARADI-TRAVEL, développée en HTML, CSS et JavaScript natifs. Aucun outil de build n'est nécessaire pour cette version.

## Aperçu en local

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/zagarino0/miaradi-travel.git
   cd miaradi-travel
   ```
2. Ouvrez `index.html` dans un navigateur. Pour éviter les restrictions de navigateur autour des fichiers locaux, vous pouvez lancer un serveur HTTP local :
   ```bash
   python -m http.server 5500
   ```
   Puis ouvrir http://localhost:5500.

## Fichiers

- `index.html` : structure des pages/sections.
- `styles.css` : identité visuelle responsive.
- `script.js` : menu mobile, filtre de circuits, sélection du circuit et bascule FR/EN.

## Fonctionnalités de la maquette

- Sections Accueil, À propos, Circuits, Contact.
- Filtrage des cartes de circuits par Nature, Côte & îles et Culture.
- Bascule entre français et anglais.
- Formulaire côté navigateur avec validation et message de démonstration.
- Mise en page responsive et prise en compte de la préférence de réduction des animations.

## Limites actuelles

Ceci est une **maquette frontend seulement**. Le formulaire ne transmet aucun e-mail et n'enregistre aucune demande. Les circuits, durées, images et textes sont illustratifs et devront être confirmés par l'agence. Les images distantes et les polices Google nécessitent une connexion Internet.

Avant la mise en production :
- remplacer/valider les photos et descriptions ;
- confirmer les coordonnées professionnelles ;
- configurer un backend PHP/MySQL ou un service de formulaire compatible avec l'offre OVHcloud ;
- ajouter politique de confidentialité, traitement des données et protections anti-spam ;
- optimiser les images, valider le SEO et tester sur plusieurs navigateurs.

## Direction artistique

Palette : vert forêt `#173f35`, ivoire `#f5f1e8`, sable `#d6ae72`.
Typographies : DM Sans et Playfair Display.
