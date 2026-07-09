-- ============================================================
-- MIGRASI: Virtual Account tidak lagi wajib (sudah pakai Xendit)
-- ============================================================
ALTER TABLE siswa MODIFY virtual_account VARCHAR(20) NULL;
