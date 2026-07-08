# Integrasi Pembayaran Online (Xendit) — SIMAKU

Fitur: wali murid bisa bayar tagihan langsung dari halaman "Cek Tagihan"
publik, pakai QRIS/Virtual Account/E-Wallet/dll (semua metode yang Anda
aktifkan di Dashboard Xendit).

## ⚠️ WAJIB dilakukan sebelum fitur ini bisa dipakai

### 1. Jalankan migrasi database
File `001_migrasi_xendit.sql` — jalankan SEKALI di database production Anda
(lewat phpMyAdmin, atau `mysql -u user -p nama_db < 001_migrasi_xendit.sql`).
Ini menambah tabel & kolom baru, tidak menghapus/mengubah data yang sudah ada.

### 2. Daftar/masuk ke Xendit Dashboard
Kalau belum punya akun: https://dashboard.xendit.co/register
Pastikan akun sudah selesai verifikasi (KYC) sebelum bisa terima uang sungguhan
— tapi untuk **uji coba dulu**, mode Test sudah bisa dipakai tanpa verifikasi.

### 3. Ambil Secret API Key
Dashboard Xendit → **Settings → API Keys → Generate Secret Key**
- Beri izin **Write** untuk produk **Invoice** (minimal ini saja yang dipakai fitur ini)
- Key mode **Test** diawali `xnd_development_...` (pakai ini dulu untuk coba-coba)
- Key mode **Live** diawali `xnd_production_...` (baru pakai ini kalau sudah siap terima uang sungguhan)

### 4. Ambil Webhook Verification Token
Dashboard Xendit → **Settings → Webhooks** → salin **Verification Token**
(token ini membedakan notifikasi ASLI dari Xendit vs yang palsu — WAJIB diisi,
tanpa ini semua webhook otomatis ditolak demi keamanan).

### 5. Daftarkan URL Webhook di Dashboard Xendit
Masih di halaman yang sama (**Settings → Webhooks**), tambahkan URL:
```
https://domain-sekolah-anda.com/xendit/webhook
```
Centang minimal event **"Invoice Paid"**.

### 6. Isi file `.env` di server Anda
Buka file `.env` di folder utama project (sejajar folder `app`, `public`),
tambahkan baris berikut (ganti dengan nilai Anda dari langkah 3 & 4):

```env
xendit.secretKey = xnd_development_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xendit.webhookToken = xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Selesai! Setelah file-file di paket ini terpasang + langkah di atas, fitur
langsung aktif — tidak perlu ubah kode apa pun lagi untuk pindah dari mode
Test ke Live, tinggal ganti nilai `xendit.secretKey` di `.env` saja nanti.

## Cara kerja fitur ini (ringkas)

1. Wali murid buka **Cek Tagihan** → **Lihat Rincian Tagihan** (halaman yang sudah ada)
2. Di bagian "Belum Lunas", sekarang ada **checkbox** di tiap tagihan
3. Setelah pilih 1 atau lebih, muncul bar di bawah layar → tombol **"Bayar Online"**
4. Diarahkan ke halaman pembayaran Xendit (QRIS/VA/E-wallet/dll, sesuai yang Anda aktifkan)
5. Setelah bayar, Xendit mengirim notifikasi ke server Anda (webhook) →
   tagihan otomatis jadi **Lunas**, tercatat sebagai pembayaran baru
6. Wali murid diarahkan kembali ke halaman status pembayaran

Di sisi **Admin**, pembayaran yang masuk lewat Xendit akan otomatis muncul di
halaman **Pembayaran** seperti pembayaran manual lainnya — cuma badge
metodenya beda (contoh: "QRIS" dengan ikon, bukan "Tunai"/"Transfer"), dan
kolom Petugas menampilkan "Otomatis (Xendit)" karena memang tidak ada staf
yang menginput manual.

## Cara uji coba (mode Test, aman, tidak pakai uang sungguhan)

1. Pastikan `.env` masih pakai key yang diawali `xnd_development_`
2. Coba alur pembayaran seperti biasa dari halaman Cek Tagihan
3. Di halaman checkout Xendit (mode Test), Anda bisa "bayar" pakai simulator
   bawaan Xendit tanpa uang sungguhan — cek dokumentasi Xendit soal
   "Test Mode Payment Simulation" untuk detail tiap metode pembayaran.
4. Setelah simulasi berhasil, cek halaman admin/pembayaran — harusnya sudah
   muncul otomatis dalam beberapa detik.

## Isi paket ini

**Baru dibuat:**
- `001_migrasi_xendit.sql` — migrasi database (jalankan dulu, lihat langkah 1)
- `Config/Xendit.php` — tempat pengaturan API key & webhook token
- `Models/XenditTransactionModel.php`
- `Services/XenditService.php` — logic inti: buat invoice, proses webhook
- `Controllers/Public/XenditController.php`
- `Views/public/pembayaran_selesai.php` — halaman status setelah bayar

**Diperbarui:**
- `Config/Routes.php` — 3 route baru (bayar, halaman selesai, webhook)
- `Models/PembayaranModel.php` — izinkan 2 kolom baru tersimpan
- `Views/public/detail.php` — checkbox + bar "Bayar Online"
- `Views/admin/pembayaran/index.php` — badge metode & filter baru
- `Views/admin/pembayaran/detail.php` — tampilkan channel & petugas otomatis

## Keamanan yang sudah diterapkan
- Secret API Key **tidak pernah** dikirim ke browser — semua panggilan ke
  Xendit terjadi di server (PHP), bukan JavaScript
- Setiap webhook yang masuk **wajib** cocok dengan Verification Token —
  kalau tidak cocok/kosong, otomatis ditolak (HTTP 401), tidak diproses
- Nominal yang benar-benar ditagihkan **dihitung ulang dari database**,
  bukan percaya begitu saja pada data dari browser (mencegah orang mengubah
  nominal lewat DevTools sebelum kirim)
- Webhook aman dipanggil berkali-kali dengan payload sama (idempotent) —
  Xendit kadang mengirim notifikasi yang sama lebih dari 1x, tidak akan
  tercatat sebagai pembayaran ganda
