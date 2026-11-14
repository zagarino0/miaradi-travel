-- Add an explicit nights count while preserving the existing day range fields.
ALTER TABLE miaradi_circuits
    ADD COLUMN duration_nights SMALLINT UNSIGNED NULL AFTER duration_max;
