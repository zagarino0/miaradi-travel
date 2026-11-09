-- Images de la section « Derrière chaque voyage, des rencontres »
CREATE TABLE IF NOT EXISTS miaradi_partner_media (
    slot_key VARCHAR(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    title_fr VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    description_fr VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    alt_text VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL,
    image_path VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (slot_key),
    KEY idx_miaradi_partner_media_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO miaradi_partner_media (slot_key,title_fr,description_fr,alt_text,sort_order)
VALUES
 ('feature','Paysages de Madagascar','Découvrir Madagascar avec celles et ceux qui le connaissent.','Paysage naturel de Madagascar',0),
 ('guide','Guides locaux','Partager les richesses du territoire.','Guide local à Madagascar',1),
 ('lodging','Hébergements','Des étapes choisies avec soin.','Hébergement au cœur de la nature malgache',2),
 ('crafts','Artisans & savoir-faire','Découvrir les traditions vivantes.','Artisanat malgache traditionnel',3)
ON DUPLICATE KEY UPDATE slot_key=VALUES(slot_key);
