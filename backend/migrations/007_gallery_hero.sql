-- MIARADI-TRAVEL: allow administrators to select published photos for the homepage hero.
-- Apply after 006_gallery_region.sql. Run once on the same database used by the site.
ALTER TABLE miaradi_gallery_items
    ADD COLUMN is_hero TINYINT(1) NOT NULL DEFAULT 0 AFTER is_published,
    ADD KEY idx_gallery_hero_public (is_hero, is_published, media_type, sort_order, id);
