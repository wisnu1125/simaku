-- ============================================================
-- MIGRASI: Fitur Batalkan Pembayaran (Cancel Invoice)
-- ============================================================
-- Jalankan SETELAH 001_migrasi_xendit.sql dan 002_migrasi_payment_sync.sql.

-- 1. Perluas status transaksi: tambah 'cancelled' (dibatalkan wali murid/admin)
--    dan 'needs_review' (webhook PAID masuk untuk invoice yang sudah cancelled -- butuh
--    ditinjau manual oleh admin, JANGAN otomatis dianggap lunas).
ALTER TABLE xendit_transaction 
    MODIFY status ENUM('pending','paid','expired','failed','cancelled','needs_review') NOT NULL DEFAULT 'pending';

-- 2. Kolom pencatatan pembatalan
ALTER TABLE xendit_transaction 
    ADD COLUMN cancelled_at DATETIME NULL COMMENT 'Kapan invoice dibatalkan' AFTER paid_at,
    ADD COLUMN cancelled_by VARCHAR(100) NULL COMMENT 'Siapa yang membatalkan: wali_murid, atau nama admin' AFTER cancelled_at;

-- 3. Tambah 'CANCEL' sebagai source baru di payment_logs (selain WEBHOOK/MANUAL/CRON yang sudah ada)
ALTER TABLE payment_logs 
    MODIFY source ENUM('WEBHOOK','MANUAL','CRON','CANCEL') NOT NULL;
