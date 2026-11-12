-- MIARADI-TRAVEL — paramètres de la politique de confidentialité
CREATE TABLE IF NOT EXISTS miaradi_privacy_policy (
    id TINYINT UNSIGNED NOT NULL,
    title_fr VARCHAR(220) NOT NULL DEFAULT 'Politique de confidentialité',
    title_en VARCHAR(220) NOT NULL DEFAULT 'Privacy policy',
    content_fr MEDIUMTEXT NOT NULL,
    content_en MEDIUMTEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT chk_miaradi_privacy_policy_singleton CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO miaradi_privacy_policy (id, title_fr, title_en, content_fr, content_en, is_published)
VALUES (
  1,
  'Politique de confidentialité',
  'Privacy policy',
  'MIARADI-TRAVEL utilise les informations transmises via le formulaire de demande de devis uniquement pour comprendre votre projet, vous contacter et préparer une proposition de voyage. Les informations demandées peuvent inclure votre nom, votre adresse e-mail, vos préférences de voyage et le contenu de votre message.\n\nL’accès à ces informations est limité aux personnes autorisées qui en ont besoin pour traiter votre demande. Elles ne sont pas vendues. Elles peuvent être conservées pendant la durée nécessaire au suivi de la demande et aux obligations légales applicables.\n\nVous pouvez demander l’accès, la rectification ou la suppression de vos informations en contactant MIARADI-TRAVEL via les coordonnées indiquées sur le site. Cette politique doit être vérifiée et complétée avec les coordonnées officielles et les règles applicables avant publication.',
  'MIARADI-TRAVEL uses the information submitted through the quote request form only to understand your project, contact you and prepare a travel proposal. The information may include your name, email address, travel preferences and message.\n\nAccess is limited to authorized people who need the information to handle your request. The information is not sold. It may be retained for as long as necessary to follow up on the request and comply with applicable legal obligations.\n\nYou may request access to, correction of or deletion of your information by contacting MIARADI-TRAVEL using the contact details published on the website. This policy should be reviewed and completed with official contact details and applicable requirements before publication.',
  0
)
ON DUPLICATE KEY UPDATE id = VALUES(id);
