# SIMAKU — Fitur Impor Data Siswa dari Excel

## Cara memasang
Salin 3 file (Controller, Routes, View) ke lokasi yang sama di server.
Tidak ada perubahan database. Pastikan library **PhpSpreadsheet** sudah
terpasang di server (kemungkinan besar sudah, karena fitur ekspor
Laporan di aplikasi Anda sudah memakainya).

## Cara pakai
1. Buka halaman **Data Siswa**, klik tombol **"Impor Excel"** di sebelah
   "Tambah Siswa".
2. Klik **"Unduh Template Excel"** — file `Contoh_Template_Import_Siswa.xlsx`
   di paket ini adalah contoh persis seperti apa yang akan terunduh
   (dibuat otomatis oleh sistem, jadi begitu Anda pasang fiturnya,
   daftar kelas di dalamnya otomatis mengikuti kelas yang benar-benar
   ada di sistem Anda, bukan contoh ini).
3. Isi data siswa di template tersebut, simpan.
4. Unggah kembali file itu lewat modal yang sama.
5. Sistem melaporkan berapa baris berhasil & baris mana yang bermasalah
   (kalau ada), lengkap dengan alasannya per baris.

## Yang ada di template
- Kolom wajib ditandai `*`: NIS, Nama Lengkap, Tanggal Lahir, Jenis Kelamin
- Dropdown otomatis untuk **Jenis Kelamin** (L/P) dan **Kelas** (diambil
  dari kelas yang benar-benar ada — kalau nama kelas tidak dikenali,
  siswa tetap masuk tapi tanpa kelas, dan dilaporkan sebagai catatan)
- Kolom NIS, NISN, No. Telepon, dan Virtual Account sengaja diformat
  sebagai **Teks**, supaya angka yang diawali "0" (seperti nomor HP)
  tidak kehilangan angka nolnya — ini kesalahan umum kalau isi Excel
  manual tanpa format ini
- Baris contoh (dicetak miring) sebagai panduan, tinggal dihapus atau
  ditimpa

## Bagaimana kalau ada baris yang salah?
Sistem **tidak membatalkan semua-atau-tidak-sama-sekali**. Tiap baris
diproses sendiri-sendiri:
- Baris yang datanya lengkap & benar → langsung tersimpan
- Baris yang salah (NIS kosong, jenis kelamin bukan L/P, tanggal tidak
  valid, NIS sudah terdaftar) → dilewati, dan dilaporkan alasannya per
  baris supaya gampang diperbaiki
- Baris dengan nama kelas yang tidak dikenali → **tetap disimpan**
  (cuma tanpa kelas), karena itu bukan kesalahan fatal
- Virtual Account boleh dikosongkan — akan dibuatkan otomatis sama
  seperti kalau tambah siswa satu-satu

## Bagaimana saya menguji ini
Karena fitur ini menyentuh pembacaan file dari pengguna (bukan sekadar
tampilan), saya pasang PhpSpreadsheet sungguhan di lingkungan kerja saya
(bukan cuma baca kode) dan menguji:
- Pembuatan template menghasilkan file yang benar-benar valid & bisa
  dibuka, dengan dropdown dan sheet referensi yang bekerja
- Pembacaan tanggal dalam 3 bentuk berbeda: teks "2012-05-14", format
  "15/03/2012", dan **tanggal asli Excel** (bukan teks) — yang paling
  sering terjadi kalau pengguna mengetik tanggal langsung di Excel
- NIS/NISN yang kebetulan tersimpan sebagai angka murni (bukan teks)
- Baris duplikat di dalam file yang sama, kelas tak dikenal, jenis
  kelamin salah, tanggal rusak, baris kosong di akhir file — semua
  tertangani sesuai yang diharapkan

Satu-satunya bagian yang **tidak** bisa saya uji langsung adalah
koneksi ke database MySQL yang sesungguhnya (model `insert()` dan cek
duplikat terhadap data asli Anda) karena saya tidak punya akses ke
server Anda — tapi logic-nya sama persis dengan proses tambah-siswa
satu-satu yang sudah berjalan sekarang, jadi risikonya rendah.
