-- Add region assignment for regional homepage gallery sliders.
-- Apply after 001_gallery.sql and 002_gallery_circuit.sql.
ALTER TABLE miaradi_gallery_items
    ADD COLUMN region VARCHAR(80) COLLATE utf8mb4_unicode_ci NULL AFTER circuit_id,
    ADD KEY idx_gallery_region_public (region, is_published, sort_order, id);
