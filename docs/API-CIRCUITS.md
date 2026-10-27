# API publique des circuits

## Endpoint

`GET /api/circuits.php`

Le fichier se trouve dans `backend/public/api/circuits.php`. Le serveur web doit exposer `backend/public` comme racine publique du backend PHP, ou configurer une route équivalente.

### Paramètre

- `lang` facultatif : `fr` (par défaut) ou `en`.
- Toute autre valeur renvoie HTTP `400`.

### Réponse de succès

```json
{
  "data": [
    {
      "id": 1,
      "slug": "ouest-sauvage",
      "title": "L'Ouest sauvage",
      "summary": "Baobabs majestueux, pistes rouges et horizons infinis.",
      "region": "Ouest",
      "theme": "Nature",
      "duration": { "min": 5, "max": 8 },
      "image": "assets/circuits/ouest.jpg",
      "updatedAt": "2026-10-27 12:00:00"
    }
  ],
  "meta": { "count": 1, "language": "fr" }
}
```

L'exemple est illustratif : les valeurs retournées proviennent des lignes publiées de `miaradi_circuits`. Le endpoint ne renvoie jamais les circuits dont `is_published = 0`. Si le titre ou le résumé anglais est vide, le champ correspondant retombe sur le français.

### Codes HTTP

- `200` : catalogue lu, y compris si aucun circuit n'est publié.
- `400` : paramètre de langue invalide.
- `405` : méthode autre que GET.
- `503` : base ou requête indisponible. Le détail technique est uniquement écrit dans le journal serveur.

## Sécurité et limites

- Requête SQL préparée, même si cet endpoint ne prend aucun filtre SQL fourni par le client.
- Aucun secret ni détail de connexion n'est renvoyé au navigateur.
- En-têtes JSON, protection MIME et politique de cache courte.
- Aucune configuration CORS permissive n'est ajoutée. En production, privilégier une API servie sur la même origine que le site.
- Cet endpoint n'enregistre pas les devis et n'authentifie pas les administrateurs.
- Le frontend reste inchangé dans cette étape. GitHub Pages ne peut pas exécuter PHP : ne pas appeler ce endpoint depuis la maquette statique tant que l'API n'est pas hébergée et que son URL n'est pas configurée.
- Avant mise en production, vérifier la disponibilité de la table et les colonnes sur une base de test, puis tester les réponses HTTP sur l'hébergement PHP cible.
