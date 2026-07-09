# Fitur Batalkan Pembayaran — SIMAKU

## 1. Cara Pasang
1. **Backup database dulu.**
2. Jalankan `004_batalkan_pembayaran.sql` — pastikan migrasi `001`, `002`, `003` sudah pernah dijalankan lebih dulu.
3. Timpa file PHP sesuai struktur folder di paket ini.
4. Tidak ada perubahan `.env` tambahan.

## 2. Struktur File
```
004_batalkan_pembayaran.sql              [BARU] Migrasi database
Models/XenditTransactionModel.php        [DIUBAH] +cancelled_at, +cancelled_by
Services/XenditService.php               [DIUBAH BESAR] +cancelInvoice, +findActivePendingInvoice, dst
Controllers/Public/XenditController.php  [DIUBAH] +cekPending(), +batalkan()
Controllers/Admin/RekonsiliasiController.php [DIUBAH] +resolve()
Config/Routes.php                        [DIUBAH] +4 route baru
Views/public/detail.php                  [DIUBAH] +kartu "Pembayaran Tertunda"
Views/admin/rekonsiliasi/index.php       [DIUBAH] +badge & panel "Perlu Ditinjau"
```

## 3. Pemetaan ke Permintaan Anda

**1. Fitur Batalkan Pembayaran** — Di halaman Cek Tagihan, kalau wali murid punya invoice
PENDING yang masih aktif, otomatis muncul kartu "Pembayaran Tertunda" dengan tombol
**Lanjutkan Pembayaran** dan **Batalkan Pembayaran**. Dialog konfirmasinya persis teks
yang Anda minta. Setelah dikonfirmasi: sistem coba `POST /invoices/{id}/expire!` ke
Xendit dulu (endpoint resmi mereka untuk ini) — **berhasil atau gagal, keduanya tetap
lanjut membatalkan di SIMAKU**, cuma hasil dari Xendit-nya dicatat ke log.

**2. Data tidak dihapus** — Status berubah jadi `cancelled`, kolom `cancelled_at` dan
`cancelled_by` (isinya `wali_murid` kalau dari halaman publik, atau nama admin kalau
dari panel Rekonsiliasi) terisi otomatis.

**3. Dashboard** — Setelah dibatalkan, halaman reload otomatis: kartu "Pembayaran
Tertunda" hilang, tombol "Bayar Online" yang sempat disembunyikan (supaya tidak dobel
selagi masih ada yang pending) muncul kembali.

**4. Bayar lagi** — `createInvoiceForSiswa()` (dipanggil tombol Bayar Online) sekarang
SELALU cek dulu apakah ada invoice pending yang masih aktif untuk siswa ini. Ada →
diarahkan ke invoice lama. Tidak ada (karena cancelled/expired) → invoice baru dibuat.
Invoice yang sudah cancelled/expired tidak pernah dipakai ulang.

**5 & 6. Webhook + Manual Review** — Ini bagian paling kritis, saya taruh sebagai
**guard universal di dalam `updatePaymentAsPaid()`** (method tunggal yang dipakai
webhook, sync manual, DAN cron) -- bukan cuma di jalur webhook saja. Jadi:
- Kalau info "PAID" datang dari MANAPUN untuk invoice yang sudah `cancelled` → status
  otomatis jadi `needs_review`, TIDAK pernah otomatis dianggap lunas.
- Persis skenario 09:00/09:05/09:06 yang Anda contohkan: teruji lewat kode, saat proses
  pelunasan menemukan status `cancelled` di database (dibaca ulang tepat sebelum
  diproses, bukan data lama), langsung dialihkan ke `needs_review`.

**7. Logging** — Semua poin yang Anda sebutkan tercatat ke tabel `payment_logs` yang
sudah ada (dari fitur sebelumnya): invoice dibuat, invoice dibatalkan, request+respons
ke Xendit, invoice expired, invoice paid, webhook diterima, dan kasus PAID-setelah-
cancelled. Webhook mentah (termasuk yang gagal validasi token) tetap tercatat di
`payment_webhook_logs` seperti sebelumnya.

**8. UX** — Dashboard/halaman Cek Tagihan HANYA menganggap invoice "aktif" kalau
statusnya `pending` DAN belum lewat `invoice_duration` sejak dibuat (`findActivePendingInvoice()`).
Cancelled/expired tidak pernah muncul sebagai "Pembayaran Tertunda". Wali murid cuma
lihat 2 tombol sederhana (Lanjutkan/Batalkan) atau tombol Bayar biasa -- tidak pernah
diperlihatkan istilah invoice/pending/cancelled sama sekali.

## 4. Sisi Admin — Halaman Rekonsiliasi
Ada kartu statistik baru **"Perlu Ditinjau"** (ungu, warna beda dari status lain biar
mencolok) + filter status `needs_review`. Setiap transaksi begini punya tombol palu
(<i class="fa-solid fa-gavel"></i>) yang buka panel keputusan:
- **Terima sebagai Lunas** — proses seperti pembayaran biasa (lewat `updatePaymentAsPaid()`
  yang sama, jadi tidak ada logic ganda)
- **Tolak (Batalkan Permanen)** — status jadi `cancelled` lagi, perlu tindak lanjut manual
  (refund dll) di luar sistem

Ada kolom catatan opsional yang ikut tersimpan ke `payment_logs`.

## 5. Keamanan Idempotency (poin krusial lain di luar daftar Anda, tapi penting)
Ditambahkan 2 lapis guard supaya tidak ada race condition:
- `updatePaymentAsPaid()` membaca ULANG status transaksi LANGSUNG dari database tepat
  sebelum diproses (bukan data yang sudah di-fetch sebelumnya) -- baik status `paid`
  MAUPUN `cancelled` sama-sama jadi guard di titik ini.
- Query UPDATE pembatalan & pelunasan sama-sama pakai klausa `WHERE status = '...'`
  sebagai pengaman tambahan di level database.

## 6. Sudah Diuji
- Migrasi database dijalankan & diverifikasi
- Semua file PHP tervalidasi sintaksnya
- Simulasi browser: kartu "Pembayaran Tertunda" muncul/tersembunyi dengan benar,
  tombol Bayar Online ikut disembunyikan saat ada yang pending, alur konfirmasi+batalkan
  jalan lewat fetch yang benar, panel "Selesaikan Peninjauan" di admin (buka panel →
  isi keputusan → submit → panel tertutup) semua teruji tanpa error
- 32 kondisi halaman (termasuk fitur-fitur sebelumnya) tetap lolos render

**Belum bisa diuji** (tidak ada API key sungguhan): panggilan nyata ke endpoint
`/invoices/{id}/expire!`. Formatnya sudah saya verifikasi ke dokumentasi resmi Xendit
(SDK Node.js, PHP, Go semua menunjukkan pola yang sama), tapi sarankan dites dulu di
mode Test sebelum dipakai dengan uang sungguhan.
