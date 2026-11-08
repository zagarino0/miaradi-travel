-- Associate each gallery item with an optional circuit.
-- Run after 001_gallery.sql on the local/test database.
ALTER TABLE miaradi_gallery_items
    ADD COLUMN circuit_id BIGINT UNSIGNED NULL AFTER id,
    ADD KEY idx_gallery_circuit_public (circuit_id, is_published, sort_order),
    ADD CONSTRAINT fk_gallery_circuit
        FOREIGN KEY (circuit_id) REFERENCES miaradi_circuits(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
