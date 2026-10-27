# Audit technique initial — MIARADI-TRAVEL

Date : 2026-10-27  
Dépôt : https://github.com/zagarino0/miaradi-travel  
Branche de travail : `feat/php-mysql-foundation`

## 1. État actuel constaté

Le dépôt est une maquette statique HTML/CSS/JavaScript, sans outil de build.

Fichiers présents à la racine :
- `index.html` : page d'accueil et sections publiques, dont les circuits et le formulaire de contact/devis.
- `a-propos.html` : page éditoriale À propos.
- `site.css` : styles, responsive design et identité visuelle.
- `script.js` : interactions du navigateur, filtres, traductions FR/EN et comportement du formulaire.
- `assets/miaradi-logo.png` : logo.
- `README.md` : instructions de maquette et limites connues.

## 2. Limites confirmées

- Le formulaire affiche un message de démonstration ; il ne transmet pas de demande à un serveur et ne l'enregistre pas.
- Les circuits, contenus, durées et images sont définis dans le frontend ; aucune base de données n'est présente dans l'arborescence.
- Aucun backend PHP, schéma SQL, authentification administrateur ou interface d'administration n'est présent.
- Le README décrit encore le projet comme une maquette frontend et mentionne une palette ancienne. La feuille `site.css` contient déjà la palette actuelle rouge `#922D20` et ivoire `#FBF8F5`.
- Le dépôt est configuré avec GitHub Pages. GitHub Pages sert les fichiers statiques et ne peut pas exécuter PHP ; le backend de production devra donc être hébergé chez OVHcloud.

## 3. Décision technique proposée

Conserver le frontend et le design existants. Introduire progressivement PHP et MySQL sur une branche dédiée, puis tester le déploiement OVH séparément. Ne pas remplacer la branche `main` ni le site actuellement publié tant que les tests et la validation ne sont pas terminés.

Architecture visée :
- Frontend public existant, adapté progressivement pour charger les circuits depuis le backend.
- Backend PHP avec configuration séparée des secrets, validation serveur et réponses explicites.
- Base MySQL pour les circuits, les demandes de devis et les comptes administrateurs.
- Espace `/admin/` protégé par authentification et contrôle d'accès côté serveur.
- Déploiement de production sur OVHcloud ; GitHub reste le dépôt de code et l'historique.

## 4. Plan par étapes

1. Préparer la structure PHP et la configuration d'environnement sans exposer de secrets.
2. Définir le schéma MySQL et une procédure d'installation/migration.
3. Construire l'authentification admin sécurisée (hachage des mots de passe, sessions sécurisées, protection CSRF et contrôle d'accès).
4. Créer la gestion CRUD des circuits et la gestion des demandes de devis.
5. Connecter le formulaire public à l'API PHP avec validation serveur, protection anti-spam et confirmation claire à l'utilisateur.
6. Tester les flux principaux, la sécurité, le responsive et les erreurs avant le déploiement OVH.
7. Documenter l'installation, les sauvegardes, la configuration OVH et la procédure de retour arrière.

## 5. Points à confirmer avant la mise en production

- Version PHP et version MySQL/MariaDB disponibles dans l'offre OVH.
- Nom de domaine final et configuration SSL.
- Adresse e-mail de réception des demandes et méthode d'envoi disponible chez OVH.
- Contenus, photos, coordonnées, politique de confidentialité et durée de conservation des demandes.
- Création initiale de l'administrateur par une procédure sûre ; aucun mot de passe ne doit être commité dans Git.

## 6. Critère de fin

La version est prête à être proposée en production lorsque les circuits et demandes sont réellement enregistrés en base, que l'administration est inaccessible sans authentification, que les validations serveur et protections de sécurité sont vérifiées, et que le site a été testé sur l'hébergement OVH.
