# Devis et administration PHP

## Endpoints

- `POST /api/quotes.php` : valide puis enregistre une demande de devis dans `miaradi_quote_requests`.
- `GET /api/admin/session.php` : indique si une session administrateur existe et retourne son jeton CSRF.
- `POST /api/admin/session.php` : authentification avec email et mot de passe.
- `POST /api/admin/session.php` avec `{"action":"logout"}` : fermeture de session (jeton CSRF requis).
- `GET|POST|PATCH|DELETE /api/admin/circuits.php` : lecture et gestion des circuits, uniquement pour un administrateur connecté.
- `GET|PATCH /api/admin/quotes.php` : consultation des 200 demandes les plus récentes et changement de statut.
- Interface : `/admin/index.php`.

## Contrat d'une demande de devis

Exemple de corps JSON :

```json
{
  "fullName": "Nom du voyageur",
  "email": "voyageur@example.com",
  "departureDate": "2027-05-12",
  "travelerCount": 2,
  "circuitId": 1,
  "message": "Nous souhaitons découvrir l'ouest de Madagascar.",
  "language": "fr",
  "privacyConsent": true
}
```

`departureDate`, `travelerCount` et `circuitId` peuvent être `null`. `privacyConsent` doit explicitement valoir `true`. Un circuit indiqué doit exister et être publié. La réponse `201` signifie que la demande a été enregistrée ; l'envoi d'email est une notification complémentaire et son échec ne supprime pas la demande. Le formulaire public actuel n'est pas encore connecté à cet endpoint : il faut attendre la mise en ligne de l'API, configurer son URL et ajouter la case de consentement avant activation.

## Initialiser le premier compte administrateur

Ne créez pas de mot de passe en clair dans SQL et ne placez aucun secret dans Git.

1. Sur un terminal sécurisé de l'environnement PHP, générer un hash en saisissant le mot de passe via l'entrée standard :

   ```sh
   read -s -p "Mot de passe admin: " ADMIN_PASSWORD; echo
   printf '%s' "$ADMIN_PASSWORD" | php -r '$p = stream_get_contents(STDIN); echo password_hash($p, PASSWORD_DEFAULT), PHP_EOL;'
   unset ADMIN_PASSWORD
   ```

2. Copier le hash généré dans une requête SQL exécutée depuis phpMyAdmin ou un client SQL privé :

   ```sql
   INSERT INTO miaradi_admin_users (email, password_hash, is_active)
   VALUES ('adresse-admin@example.com', 'COLLER_ICI_LE_HASH', 1);
   ```

3. Vérifier que le compte a été créé, puis ouvrir `/admin/index.php` sur le serveur qui héberge le PHP.

## Sécurité à finaliser avant production

- Le bootstrap utilise des cookies de session `HttpOnly`, `SameSite=Lax`, et `Secure` lorsque PHP détecte HTTPS. La session est régénérée après une authentification réussie.
- Les actions administrateur qui modifient des données requièrent un jeton CSRF.
- Les requêtes SQL utilisent PDO et des paramètres préparés. Les erreurs détaillées vont dans les logs serveur, pas dans les réponses publiques.
- Configurer une limitation des tentatives de connexion au niveau serveur/WAF avant production ; ne pas considérer le délai de réponse comme une protection anti-bruteforce suffisante.
- Forcer HTTPS, définir les variables DB uniquement dans l'environnement privé, désactiver l'affichage des erreurs PHP et conserver les logs hors de la racine publique.
- Configurer et tester la livraison email de l'hébergement. L'API utilise `mail()` si `MAIL_TO` et `MAIL_FROM` sont renseignés et valides ; l'acceptation réelle dépend du serveur mail.
- Vérifier la politique de confidentialité et la durée de conservation des demandes avant d'activer le formulaire.
- L'interface d'administration et les endpoints n'ont pas encore été validés sur l'environnement OVH réel. Ne pas considérer cette branche comme prête pour la production avant les tests d'intégration.
