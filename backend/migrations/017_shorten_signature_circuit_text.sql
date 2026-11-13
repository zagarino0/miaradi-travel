-- MIARADI-TRAVEL — mettre à jour le voyage signature sur l'accueil.
-- À exécuter sur la base locale après sauvegarde.
UPDATE miaradi_page_content
SET content_json = JSON_SET(
  content_json,
  '$.introFeatureTitle', 'De Boeny Aranta aux',
  '$.introFeatureAccent', 'Tsingy sauvages de Namoroka',
  '$.introFeatureText', 'Explorez les trésors du Boeny entre patrimoine maritime, villages côtiers et Tsingy de Namoroka. Une aventure de 9 jours entre mer, culture et nature préservée.'
)
WHERE content_key = 'homepage_full'
  AND JSON_VALID(content_json);
