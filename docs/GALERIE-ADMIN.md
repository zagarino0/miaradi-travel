# Galerie multimédia — MIARADI-TRAVEL

## Installation locale

1. Vérifier que le serveur PHP utilise la base de test `miaradi_travel_test`.
2. Exécuter `backend/migrations/001_gallery.sql` si la table galerie n'existe pas encore.
3. Exécuter ensuite `backend/migrations/002_gallery_circuit.sql` pour associer chaque média à un circuit (association facultative pour les médias généraux).
4. Vérifier que PHP possède les extensions PDO MySQL et Fileinfo.
5. Pour les vidéos locales, configurer PHP avec `upload_max_filesize = 100M`, `post_max_size = 110M` ou davantage, et un `max_execution_time` adapté. Redémarrer Apache après toute modification de php.ini.
6. Ouvrir `/miaradi-travel/backend/public/admin/gallery.php` et se connecter avec le compte administrateur existant.

Les photos sont limitées à 10 Mo (JPG, PNG, WebP). Les vidéos locales sont limitées à 100 Mo (MP4, WebM). Les liens externes acceptés sont YouTube et Vimeo uniquement. Les médias sont privés par défaut (brouillon).

## API

- `GET /backend/public/api/admin/gallery.php` — liste complète (session administrateur requise).
- `POST /backend/public/api/admin/gallery.php` — création ; utiliser `multipart/form-data` pour les fichiers, ou JSON pour une vidéo YouTube/Vimeo.
- `PATCH /backend/public/api/admin/gallery.php` — modification des métadonnées / publication (JSON).
- `DELETE /backend/public/api/admin/gallery.php?id=123` — suppression.
- `GET /backend/public/api/gallery.php?lang=fr` — médias publiés en français.
- `GET /backend/public/api/gallery.php?lang=en` — médias publiés en anglais.
- `GET /backend/public/api/gallery.php?lang=fr&circuit_id=123` — médias publiés pour un circuit précis.

L'API d'administration renvoie également la liste des circuits dans la propriété `circuits`. Le champ `circuit_id` peut être laissé vide pour une photo/vidéo générale de Madagascar. L'API publique inclut un objet `circuit` (id, slug, titre) pour créer un lien vers la fiche du circuit.

Les requêtes d'écriture nécessitent une session administrateur et l'en-tête `X-CSRF-Token`. Les fichiers sont renommés aléatoirement, leur MIME est vérifié côté serveur, les types exécutables sont rejetés et l'exécution de scripts est désactivée dans le dossier d'upload.

## Vérifications manuelles

- Importer une photo en brouillon et vérifier qu'elle apparaît dans l'administration mais pas dans l'API publique.
- Publier la photo et vérifier les deux langues.
- Associer un média à un circuit, vérifier `circuit_id` dans l'administration et tester le filtre public avec `?circuit_id=ID`.
- Enregistrer un média sans circuit et vérifier que `circuit` vaut `null` dans la réponse publique.
- Ajouter un lien YouTube puis un lien Vimeo ; vérifier les URL d'intégration normalisées.
- Importer un MP4/WebM sous la limite ; vérifier la publication et la suppression.
- Essayer un fichier non autorisé et une URL provenant d'un domaine tiers : les deux doivent être refusés.
- Sans session, l'API d'administration doit retourner HTTP 401 ; sans jeton CSRF, une écriture doit retourner HTTP 403.

## Hébergement

Le dossier d'upload doit être persistant et inscriptible par PHP. Ajuster les limites d'upload selon les limites réelles du plan OVHcloud avant d'autoriser les fichiers vidéo. Ne pas utiliser un stockage temporaire ni exposer de secrets dans le dépôt.

L'API publique est prête à fournir les éléments de la galerie. Le branchement du bloc visuel du site public doit être validé selon l'emplacement réel de l'API sur l'hébergement ; GitHub Pages seul ne peut pas exécuter PHP.
