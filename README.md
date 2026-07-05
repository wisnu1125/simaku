# SIMAKU — Audit Menyeluruh Sebelum Deploy

## Cara memasang
Salin 3 file ke lokasi yang sama di server.

## Bug yang ditemukan & diperbaiki di audit ini

### 1. `Views/admin/pembayaran/detail.php` — terlewat dari redesain sejak awal
Halaman ini (dibuka lewat ikon mata di daftar Pembayaran) ternyata **belum
pernah ikut diperbarui sama sekali** sepanjang sesi kita — masih pakai
font & warna lama, dan modal "Batalkan Pembayaran"-nya pakai pola yang
beda sendiri dari sistem desain yang sekarang. Fungsinya tetap jalan,
tapi tampilannya "loncat" dari halaman lain. Sudah saya tulis ulang
dengan sistem desain yang sama (Roboto, kartu kwitansi, panel inline
untuk pembatalan) — logic-nya (termasuk field alasan pembatalan wajib
diisi) saya pertahankan persis.

### 2. `Views/admin/pembayaran/kwitansi_pdf.php` — PHP Deprecated Warning
Fungsi `terbilang()` (angka jadi teks, buat kwitansi) memakai hasil
pembagian sebagai index array. Di PHP 8.1+, itu memicu warning
"Implicit conversion from float to int" — sekarang baru sekadar
peringatan, tapi berpotensi jadi **error fatal** di versi PHP yang
lebih baru. Sudah diperbaiki pakai `intdiv()`. Sudah saya tes dengan
angka ganjil (Rp 1.450.750) untuk pastikan tidak muncul lagi.

### 3. `Views/public/home.php` — celah keamanan (XSS)
Di halaman publik cek tagihan, kalau validasi form gagal, nilai
"Tanggal Lahir" yang tadi diketik user ditampilkan lagi tanpa disaring
(`old()` tanpa `esc()`). Ini celah **Cross-Site Scripting** — orang
jahat bisa menyisipkan kode berbahaya lewat field ini. Sudah
diperbaiki, sekarang nilainya disaring dulu sebelum ditampilkan.

## Yang sudah dicek dan AMAN (tidak perlu tindakan)
- Sintaks seluruh 123 file PHP di project — valid semua
- Seluruh 4 perbaikan Tahap 1 (status tahun ajaran, role user, status
  tagihan otomatis lunas, Operasional) — masih utuh, tidak ada yang
  ke-revert selama proses redesain
- Tidak ada pola SQL Injection (semua query pakai query builder,
  bukan string mentah)
- Semua halaman aktif (termasuk kartu pembayaran & kwitansi PDF)
  sudah dites render dengan data kosong maupun terisi — bersih

## File LAMA yang aman dihapus (sudah 100% tidak terpakai)
Ini bukan bug, cuma beres-beres — file-file ini sudah tidak
direferensikan route mana pun karena fungsinya sudah dipindah jadi
modal/panel inline di halaman index masing-masing:
```
Views/admin/operasional/ (seluruh folder)
Views/admin/tagihan/generate.php
Views/admin/jenis_tagihan/form.php
Views/admin/siswa/detail.php
Views/admin/siswa/form.php
Views/admin/kenaikan_kelas/kelulusan.php
Views/admin/kenaikan_kelas/form.php
Views/admin/tahun_ajaran/form.php
Views/admin/kelas/form.php
Views/admin/beasiswa/form.php
Views/admin/skema_tagihan/form.php
Controllers/Admin/OperasionalController.php
```
Boleh dihapus manual dari server kalau mau beres-beres, atau
dibiarkan saja — tidak mengganggu apa pun karena sudah tidak
ada yang mengarah ke sana.

## Checklist sebelum benar-benar deploy (di luar kode, di server Anda)
Ini bukan sesuatu yang bisa saya perbaiki lewat file, tapi **penting**
dicek langsung di server tujuan deploy:

1. **File `.env`**: pastikan `CI_ENVIRONMENT = production` (bukan
   `development`) — supaya pesan error PHP yang detail tidak
   tertampil ke pengunjung kalau ada masalah, cuma tercatat di log.
2. **`app.baseURL`** di `.env`: samakan protokolnya (`http://` atau
   `https://`) dengan yang benar-benar dipakai server produksi Anda —
   ini akar masalah CORS yang kita temukan sebelumnya.
3. Folder **`writable/`** harus bisa ditulis oleh web server (izin
   akses/permission) — dipakai untuk cache, log, dan session.
4. Database produksi: pastikan sudah menjalankan SQL migrasi dari
   Tahap 1 (`001_perbaikan_data.sql`) kalau belum pernah, dan SQL
   perbaikan tagihan Rp 0 (`perbaikan_tagihan_nol.sql`) kalau ada data
   lama yang bermasalah.
