-- Editable copy for the homepage "Une aventure humaine" section.
CREATE TABLE IF NOT EXISTS miaradi_page_content (
  content_key VARCHAR(100) NOT NULL,
  content_json JSON NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (content_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO miaradi_page_content (content_key, content_json)
VALUES ('homepage_local_partners', '{"eyebrow":"UNE AVENTURE HUMAINE","title":"Derrière chaque voyage, des rencontres.","paragraph1":"Notre connaissance de Madagascar s’enrichit des liens tissés avec les acteurs locaux : guides, hébergeurs, artisans, transporteurs et initiatives de terrain.","paragraph2":"Ces collaborations contribuent à créer des expériences plus humaines, adaptées aux réalités locales et attentives aux personnes qui accueillent les voyageurs.","linkText":"Découvrir notre réseau local","caption":"Découvrir Madagascar avec celles et ceux qui le connaissent.","guideTitle":"Guides locaux","guideText":"Partager les richesses du territoire.","lodgingTitle":"Hébergements","lodgingText":"Des étapes choisies avec soin.","craftsTitle":"Artisans & savoir-faire","craftsText":"Découvrir les traditions vivantes."}')
ON DUPLICATE KEY UPDATE content_key = VALUES(content_key);
