-- =====================================================================
-- SIMAKU - Skrip Perbaikan Database
-- =====================================================================
-- WAJIB BACKUP DATABASE DULU SEBELUM MENJALANKAN SKRIP INI!
-- Di phpMyAdmin: Export > pilih database > Go, simpan file .sql-nya
-- sebagai cadangan sebelum lanjut.
--
-- Skrip ini AMAN dijalankan berkali-kali (tidak akan merusak data
-- yang sudah benar), tapi tetap disarankan backup dulu sebagai
-- kebiasaan baik sebelum mengubah struktur tabel produksi.
-- =====================================================================


-- ---------------------------------------------------------------------
-- BAGIAN 1: Perbaikan kolom status di tabel tahun_ajaran
-- ---------------------------------------------------------------------
-- Penyebab: kolom `status` di database hanya mengizinkan 'aktif' dan
-- 'closed', padahal seluruh kode aplikasi (Controller, Model, tampilan
-- halaman Tahun Ajaran) sudah lama memakai 3 status: 'aktif',
-- 'belum_aktif', dan 'selesai'. Akibatnya setiap kali ada tahun ajaran
-- baru yang tidak langsung diaktifkan, atau tahun ajaran lama yang
-- ditutup, nilai status-nya gagal tersimpan dan berubah jadi string
-- kosong ('').
--
-- Ini BUKAN cuma teori, sudah terbukti kejadian: baris "TA 2026/2027"
-- yang dibuat hari ini datanya sudah kosong di database yang diupload.
--
-- Perbaikan: perluas pilihan status di database supaya sama dengan
-- yang dipakai kode aplikasi. Tidak perlu ubah kode PHP sama sekali
-- untuk bagian ini, karena PHP-nya sudah benar sejak awal.
-- ---------------------------------------------------------------------

ALTER TABLE `tahun_ajaran`
  MODIFY COLUMN `status` ENUM('aktif','belum_aktif','selesai') NOT NULL DEFAULT 'belum_aktif';

-- Perbaiki baris yang sudah terlanjur rusak (status kosong) supaya
-- statusnya masuk akal lagi. Karena baris ini tidak dicentang "aktif"
-- saat dibuat, nilai yang paling sesuai adalah 'belum_aktif'.
UPDATE `tahun_ajaran` SET `status` = 'belum_aktif' WHERE `status` = '' OR `status` IS NULL;


-- ---------------------------------------------------------------------
-- BAGIAN 2: Perbaikan status tagihan yang 100% ditanggung beasiswa
-- ---------------------------------------------------------------------
-- Penyebab: saat generate tagihan, kalau seorang siswa sudah punya
-- beasiswa yang menanggung 100% dari sebuah tagihan (jadi nilai
-- akhirnya Rp 0), sistem tetap menandainya "belum_bayar" alih-alih
-- "lunas". Akibatnya tagihan yang sebenarnya sudah gratis/lunas malah
-- muncul di Laporan Tunggakan dan Dashboard seolah-olah masih nunggak.
--
-- Ditemukan 71 baris tagihan dengan kondisi ini di data yang diupload.
--
-- Perbaikan kode ada di Services/TagihanService.php (lihat file
-- terlampir) supaya tidak terjadi lagi ke depannya. Query di bawah ini
-- membereskan data yang sudah kadung salah dari sebelumnya.
-- ---------------------------------------------------------------------

UPDATE `tagihan`
SET `status_tagihan` = 'lunas'
WHERE `sisa_tagihan` = 0 AND `status_tagihan` != 'lunas';


-- ---------------------------------------------------------------------
-- VERIFIKASI HASIL (jalankan setelah dua bagian di atas)
-- ---------------------------------------------------------------------
-- Harusnya semua baris tahun_ajaran punya status 'aktif', 'belum_aktif',
-- atau 'selesai' -- tidak ada lagi yang kosong:
SELECT id_tahun_ajaran, nama_tahun_ajaran, status FROM tahun_ajaran;

-- Harusnya tidak ada lagi tagihan dengan sisa Rp 0 tapi status selain 'lunas':
SELECT COUNT(*) AS sisa_yang_masih_salah
FROM tagihan WHERE sisa_tagihan = 0 AND status_tagihan != 'lunas';
-- (angka di atas harus 0)
