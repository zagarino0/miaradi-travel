-- Store the last customer-service reply and the confirmed submission timestamp.
-- Run once against the existing MIARADI database before deploying the reply feature.
ALTER TABLE miaradi_quote_requests
    ADD COLUMN reply_subject VARCHAR(200) NULL AFTER message,
    ADD COLUMN reply_message TEXT NULL AFTER reply_subject,
    ADD COLUMN replied_at DATETIME NULL AFTER privacy_consent_at;
