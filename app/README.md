# Payment Sync, Rekonsiliasi & Audit Log — SIMAKU

Fitur lanjutan integrasi Xendit: sinkronisasi status manual/otomatis, audit log,
webhook log, dan halaman rekonsiliasi pembayaran.

## 1. Cara Pasang

1. **Backup database dulu** (fitur ini mengubah struktur tabel).
2. Jalankan `002_migrasi_payment_sync.sql` — **pastikan migrasi Xendit dasar
   (`001_migrasi_xendit.sql`) sudah pernah dijalankan lebih dulu**, karena
   file ini menambah kolom ke tabel `xendit_transaction` yang dibuat di situ.
3. Timpa semua file PHP sesuai struktur folder di paket ini.
4. Tidak ada perubahan `.env` tambahan — masih pakai `xendit.secretKey` dan
   `xendit.webhookToken` yang sudah Anda isi sebelumnya.

## 2. Struktur File Baru/Diubah

```
002_migrasi_payment_sync.sql          [BARU] Migrasi database
Config/
  Xendit.php                          (tidak berubah, ikut disertakan buat referensi)
  Routes.php                          [DIUBAH] +5 route Rekonsiliasi
Models/
  XenditTransactionModel.php          [DIUBAH] +2 kolom baru
  PaymentLogModel.php                 [BARU]
  PaymentWebhookLogModel.php          [BARU]
Services/
  XenditService.php                   [DITULIS ULANG] +sync, +logging, +unified update
Controllers/
  Public/XenditController.php         [DIUBAH] webhook jadi lebih tipis + logging
  Admin/RekonsiliasiController.php    [BARU]
  Admin/DashboardController.php       [DIUBAH] +data Payment Monitoring
Commands/
  PaymentSyncCommand.php              [BARU] php spark payment
Views/admin/
  rekonsiliasi/index.php              [BARU] Halaman Rekonsiliasi Pembayaran
  dashboard/index.php                 [DIUBAH] +widget Payment Monitoring
  layouts/header.php                  [DIUBAH] +menu Rekonsiliasi, +toast.info
  layouts/footer.php                  [DIUBAH] +showToast()
```

## 3. Penjelasan Tiap Bagian

### Payment Status Sync (poin 1)
Semuanya ada di `XenditService.php`:
- `getInvoiceStatus($invoiceId)` — `GET /v2/invoices/{id}` langsung ke Xendit
- `getInvoiceByExternalId($externalId)` — cari invoice pakai external_id kalau invoice_id belum diketahui
- `syncInvoice($idTransaction, $source)` — orkestrasi: tanya status ke Xendit, bandingkan dengan database, panggil `updatePaymentAsPaid()` kalau ternyata sudah PAID

**Method tunggal `updatePaymentAsPaid()`** — ini jantung dari seluruh sistem.
Baik webhook, sync manual (tombol), maupun cron (`php spark payment`) **semua
memanggil method yang sama persis ini**. Tidak ada logic pelunasan yang
diduplikasi di 3 tempat berbeda — kalau nanti perlu ubah cara pencatatan
pembayaran, cukup ubah 1 method ini saja.

### Tombol Sinkronkan & Sinkronkan Semua (poin 2 & 3)
Halaman baru: **admin/rekonsiliasi**. Setiap transaksi pending ada tombol
sinkronkan sendiri-sendiri. Tombol "Sinkronkan Semua Pending" akan:
1. Ambil daftar ID transaksi pending
2. Proses **satu-per-satu** (bukan 1 request besar) — supaya progress bar
   bisa jalan bertahap dan tidak berisiko timeout kalau pending-nya banyak
3. Setelah selesai, tampilkan ringkasan: berapa berhasil, berapa berubah
   statusnya, berapa gagal

### Auto Retry — `php spark payment` (poin 4)
Ada di `Commands/PaymentSyncCommand.php`. Fitur keamanannya:
- **Cuma proses transaksi yang dibuat LEBIH dari 2 menit lalu** (bisa diatur
  lewat `--older-than`) — supaya tidak "berebut" dengan webhook yang mungkin
  baru saja dikirim Xendit untuk transaksi yang sama
- Kalau 1 transaksi error saat diproses, **tidak menghentikan seluruh proses**
  — lanjut ke transaksi berikutnya, dicatat sebagai gagal
- Aman dijalankan berkali-kali (idempotent, lihat poin 8)

**Contoh isi crontab** (jalan tiap 10 menit):
```
*/10 * * * * cd /path/ke/project && php spark payment >> writable/logs/payment-cron.log 2>&1
```
Ganti `/path/ke/project` dengan lokasi folder project di server Anda. Kalau
pakai cPanel/hPanel, biasanya ada menu "Cron Jobs" — tinggal isi command yang
sama di sana.

### Webhook Improvement (poin 5)
**Soal "signature"**: Xendit (beda dari Stripe/GitHub) tidak pakai HMAC
signature — mekanisme resminya adalah **token pembanding** lewat header
`X-Callback-Token`. Ini yang sudah dipakai sejak integrasi awal, dan itu
memang cara yang BENAR sesuai dokumentasi Xendit — bukan disederhanakan.

Yang ditambahkan sekarang:
- **Semua request webhook dicatat** ke `payment_webhook_logs` — valid maupun
  ditolak, buat keperluan debugging
- **Database transaction** dengan rollback otomatis kalau ada bagian yang gagal
- Response ke Xendit sesuai ekspektasi mereka (HTTP 200 untuk sukses/sudah
  diproses, supaya Xendit tidak terus mengirim ulang notifikasi yang sama)
- **Controller dibuat tipis** — cuma validasi token, panggil Service, catat
  log, return response. Tidak ada logic pelunasan tagihan di Controller sama
  sekali, semua di `XenditService::processWebhookPayload()` →
  `updatePaymentAsPaid()`

### Audit Log — `payment_logs` (poin 6)
Setiap kali ada percobaan perubahan status (berhasil maupun tidak), tercatat
dengan: sumbernya (WEBHOOK/MANUAL/CRON), status lama → status baru, snapshot
data dari Xendit, dan pesan penjelasan. Bisa dilihat lewat tombol "Lihat Log"
di halaman Rekonsiliasi.

### Webhook Log — `payment_webhook_logs` (poin 7)
Mencatat **request mentahnya** (bukan hasil prosesnya) — header, token yang
diterima, payload, hasil validasi (VALID/INVALID_TOKEN/INVALID_PAYLOAD/ERROR).
Kalau suatu saat ada laporan "kok pembayaran X tidak otomatis lunas", tabel
ini yang pertama dicek — apakah webhook-nya memang sampai atau tidak.

### Idempotency (poin 8)
Dijamin oleh 2 lapis di `updatePaymentAsPaid()`:
1. **Baca ulang status TERKINI** dari database tepat sebelum memproses (bukan
   pakai data yang sudah di-fetch sebelumnya) — kalau ternyata statusnya
   SUDAH `paid` (diproses proses lain barusan), langsung berhenti, tidak
   diproses ulang
2. **Guard di level query UPDATE**: `WHERE status != 'paid'` — jaga-jaga
   ekstra kalau ada 2 proses jalan hampir bersamaan persis di detik yang sama

Hasilnya: dipanggil 1x atau 100x dengan data yang sama, hasilnya identik —
tidak ada pembayaran dobel, tidak ada saldo tagihan dikurangi 2x.

### Database Transaction (poin 9)
Seluruh isi `updatePaymentAsPaid()` (insert pembayaran + update tagihan +
update status transaksi + insert audit log) dibungkus `$db->transStart()` /
`$db->transComplete()`. Kalau salah satu langkah gagal, SEMUANYA di-rollback
— tidak akan ada kondisi "tagihan sudah lunas tapi catatan pembayarannya
hilang" atau sebaliknya.

### Error Handling (poin 10)
Di `XenditService::httpRequest()`, semua kondisi berikut ditangani dengan
pesan yang jelas (bukan cuma "Error"):
timeout, gagal koneksi, invoice tidak ditemukan (404), rate limit (429),
error di server Xendit (5xx). Semua tercatat ke `log_message()` bawaan CI4
juga, bisa dicek di `writable/logs/`.

### Dashboard Monitoring (poin 11)
Widget baru di Dashboard: Pending, Lunas Hari Ini, Webhook Gagal (24 jam
terakhir), Perlu Sync (pending yang belum pernah disinkronkan sama sekali),
dan waktu sinkronisasi terakhir.

### UI — Badge & Tombol (poin 12)
Badge: Pending (kuning), Lunas (hijau), Kedaluwarsa (abu), Gagal (merah),
plus badge tambahan oranye "Perlu Sync" untuk pending yang belum pernah
dicek. Tombol Sinkronkan/Sinkronkan Semua/Lihat Log semua pakai komponen
desain yang SAMA dengan yang sudah dipakai di seluruh aplikasi Anda (bukan
Bootstrap terpisah — karena aplikasi ini sejak awal memang tidak memakai
Bootstrap, tapi sistem desain custom sendiri yang sudah konsisten di semua
halaman; saya ikuti itu supaya tidak ada 2 gaya berbeda di 1 aplikasi).

### Keamanan (poin 13)
- Semua route Rekonsiliasi ada di dalam grup `admin` yang sudah otomatis
  butuh login (filter `auth`) — sama seperti halaman admin lainnya
- Webhook TIDAK butuh login (memang tidak bisa, karena yang mengakses adalah
  server Xendit) — tapi WAJIB token cocok, ditolak (401) kalau tidak
- Secret API Key **tidak pernah** ada di kode manapun — cuma dibaca dari
  `.env` lewat `Config/Xendit.php`

### Kualitas Kode (poin 14)
- **Service Layer**: seluruh logic bisnis di `XenditService`, Controller
  cuma jembatan tipis (terima request → panggil Service → kembalikan respons)
- **DRY**: 1 method (`updatePaymentAsPaid`) dipakai 3 sumber berbeda
- Tidak ada query database langsung di Controller — semua lewat Model/Service
- Repository pattern TIDAK ditambahkan karena project ini memang sejak awal
  memakai pola Model bawaan CodeIgniter, bukan Repository terpisah — supaya
  konsisten dengan seluruh kode yang sudah ada, bukan malah bikin 2 gaya
  arsitektur berbeda dalam 1 aplikasi

## 4. Yang TIDAK Diubah (Kompatibilitas)
Fitur pembayaran online yang sudah jalan (buat invoice, redirect ke Xendit,
webhook dasar) **tidak ada yang dihapus** — cuma diperkuat. Alur wali murid
bayar dari halaman "Cek Tagihan" sama sekali tidak berubah.

## 5. Sudah Diuji
- Migrasi database dijalankan & diverifikasi ke database uji
- Seluruh file PHP tervalidasi sintaksnya
- Seluruh interaksi JS di halaman Rekonsiliasi disimulasikan (muat daftar,
  sync 1, sync semua dengan progress, lihat log, notifikasi toast) — semua
  lolos tanpa error
- 32 kondisi halaman (termasuk yang sudah ada sebelumnya) tetap lolos render

**Yang BELUM bisa saya uji** (karena tidak ada akses API key sungguhan):
panggilan nyata ke `GET /v2/invoices/{id}` di Xendit. Endpoint dan format
respons sudah saya verifikasi ke dokumentasi resmi Xendit, tapi tetap
sarankan uji di mode Test dulu (`xnd_development_...`) sebelum dipakai
dengan uang sungguhan.
