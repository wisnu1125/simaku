-- ============================================================
-- MIGRASI: Payment Sync, Audit Log, Webhook Log, Rekonsiliasi
-- ============================================================
-- Jalankan SEKALI di database production, SETELAH migrasi
-- 001_migrasi_xendit.sql (integrasi Xendit dasar) sudah jalan.

-- 1. Tambah kolom pelacakan tambahan di xendit_transaction
ALTER TABLE xendit_transaction 
    ADD COLUMN xendit_payment_id VARCHAR(100) NULL COMMENT 'ID payment dari Xendit (beda dari invoice_id -- ini reference transaksi pembayarannya sendiri)' AFTER payment_channel,
    ADD COLUMN last_synced_at DATETIME NULL COMMENT 'Kapan terakhir kali status disinkronkan (manual/cron), NULL kalau belum pernah' AFTER paid_at;

-- 2. Tabel audit log -- mencatat SETIAP perubahan status pembayaran, dari sumber manapun
CREATE TABLE IF NOT EXISTS payment_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_transaction INT(11) NULL COMMENT 'FK ke xendit_transaction, boleh NULL kalau transaksi tidak ketemu saat proses',
    id_pembayaran INT(11) NULL COMMENT 'FK ke pembayaran, diisi kalau proses ini menghasilkan record pembayaran',
    invoice_id VARCHAR(100) NULL,
    external_id VARCHAR(100) NULL,
    source ENUM('WEBHOOK','MANUAL','CRON') NOT NULL,
    old_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NULL,
    response_json TEXT NULL COMMENT 'Snapshot data dari Xendit saat perubahan ini terjadi',
    message VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transaction (id_transaction),
    KEY idx_external_id (external_id),
    KEY idx_created (created_at),
    CONSTRAINT fk_paymentlog_transaction FOREIGN KEY (id_transaction) REFERENCES xendit_transaction(id_transaction) ON DELETE SET NULL,
    CONSTRAINT fk_paymentlog_pembayaran FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel webhook log -- mencatat SETIAP request webhook yang masuk (valid maupun ditolak), buat debugging
CREATE TABLE IF NOT EXISTS payment_webhook_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_headers TEXT NULL COMMENT 'JSON seluruh header request (token disamarkan)',
    signature_token VARCHAR(255) NULL COMMENT 'Nilai X-Callback-Token yang diterima (bukan yang seharusnya)',
    payload TEXT NULL COMMENT 'Body request mentah',
    validation_result ENUM('VALID','INVALID_TOKEN','INVALID_PAYLOAD','ERROR') NOT NULL,
    response_code INT(11) NULL,
    response_body TEXT NULL,
    error_message VARCHAR(500) NULL,
    received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_received (received_at),
    KEY idx_validation (validation_result)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
