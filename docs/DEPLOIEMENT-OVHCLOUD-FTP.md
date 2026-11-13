# Déploiement semi-automatique — MIARADI-TRAVEL / OVHcloud

## Principe
GitHub Actions prépare une archive de production à télécharger. Le transfert FTP reste manuel : aucun accès OVHcloud n'est nécessaire dans GitHub et aucun déploiement n'est déclenché à chaque push.

## Préparer l'archive
1. Ouvrir l'onglet Actions du dépôt.
2. Sélectionner **Prepare OVHcloud FTP package**.
3. Cliquer **Run workflow** et choisir la branche/tag à empaqueter.
4. Attendre la réussite du workflow.
5. Dans le résultat de l'exécution, télécharger l'artifact **miaradi-travel-ovhcloud**.
6. Extraire l'archive localement et vérifier le manifeste ainsi que les fichiers avant tout transfert.

## Exclusions de sécurité
L'archive exclut notamment `.git`, `.github`, la documentation interne, les exports/sources de base de données, les migrations SQL, les tests, les environnements `.env`, la configuration locale, les logs et les fichiers d'upload runtime.

## Transfert FTP
1. Sauvegarder les fichiers actuels du dossier `www` via FTP avant le premier déploiement.
2. Envoyer le contenu du dossier `miaradi-travel/` extrait dans le dossier racine `www` de l'hébergement associé à `miaradi-travel.com`.
3. Ne pas envoyer le dossier parent lui-même : les fichiers du projet doivent arriver directement dans `www`.
4. Ne pas écraser ni supprimer les uploads ou autres données gérées par le site.
5. Tester l'accueil, les pages circuits, la galerie, l'administration et les formulaires.

## Base de données de production — règle stricte
- Aucun workflow ne se connecte à MySQL/MariaDB.
- Ne pas importer de dump local en production lors d'un déploiement de code.
- Ne jamais transférer les fichiers de données MariaDB depuis XAMPP par FTP.
- Les migrations SQL sont exclues de l'archive. Toute migration de schéma doit faire l'objet d'une procédure distincte, d'une sauvegarde et d'une validation explicite.
- Les identifiants de production doivent rester dans la configuration privée sur le serveur ou dans le gestionnaire de secrets adapté, jamais dans Git.

## Limites connues
- Ce workflow prépare l'archive, il ne vérifie pas encore la compatibilité de l'offre OVHcloud, de la version PHP, des chemins ni de la configuration de production.
- Il ne supprime aucun fichier du serveur et ne fournit pas de rollback automatique.
- Avant un premier déploiement, comparer la configuration réelle du serveur et le contenu attendu. Si le site dépend de fichiers exclus (par exemple des uploads persistants), les conserver séparément sur le serveur.
