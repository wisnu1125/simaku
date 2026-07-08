-- ============================================================
-- MIGRASI: Integrasi Pembayaran Online via Xendit
-- ============================================================
-- Jalankan SEKALI di database production sebelum memasang kode.

-- 1. Izinkan id_user kosong (pembayaran otomatis dari Xendit tidak diinput oleh petugas manapun)
ALTER TABLE pembayaran MODIFY id_user INT(11) NULL COMMENT 'User yang input pembayaran (NULL jika otomatis dari Xendit)';

-- 2. Tambah 'xendit' sebagai metode pembayaran baru (mendampingi tunai & transfer yang sudah ada)
ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('tunai','transfer','xendit') NOT NULL;

-- 3. Kolom tambahan untuk detail channel spesifik & lacak invoice Xendit
ALTER TABLE pembayaran ADD COLUMN payment_channel VARCHAR(50) NULL COMMENT 'Channel spesifik: QRIS, BCA, OVO, dst (khusus metode=xendit)' AFTER metode_pembayaran;
ALTER TABLE pembayaran ADD COLUMN xendit_invoice_id VARCHAR(100) NULL COMMENT 'ID invoice Xendit terkait (khusus metode=xendit)' AFTER payment_channel;

-- 4. Tabel baru: lacak siklus transaksi Xendit dari dibuat sampai selesai/gagal/kedaluwarsa
CREATE TABLE IF NOT EXISTS xendit_transaction (
    id_transaction INT(11) NOT NULL AUTO_INCREMENT,
    xendit_invoice_id VARCHAR(100) NOT NULL,
    external_id VARCHAR(100) NOT NULL,
    id_siswa INT(11) NOT NULL,
    tagihan_ids TEXT NOT NULL COMMENT 'JSON array id_tagihan yang sedang dibayar',
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending','paid','expired','failed') NOT NULL DEFAULT 'pending',
    invoice_url VARCHAR(500) NULL,
    payment_channel VARCHAR(50) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME NULL,
    PRIMARY KEY (id_transaction),
    UNIQUE KEY uniq_external_id (external_id),
    UNIQUE KEY uniq_invoice_id (xendit_invoice_id),
    KEY idx_siswa (id_siswa),
    KEY idx_status (status),
    CONSTRAINT fk_xendit_siswa FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
