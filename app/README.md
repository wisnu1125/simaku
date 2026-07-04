# SIMAKU — Redesign Tahap 7 (Kenaikan Kelas, Laporan) — TERAKHIR

Modul terakhir dari redesain total. Setelah ini, seluruh 13 bagian admin
sudah konsisten satu sistem desain.

## Cara memasang
Salin 6 file ke lokasi yang sama di server. Tidak ada perubahan database.

## Kenaikan Kelas
3 file lama (index, form, kelulusan) digabung jadi **1 halaman**: pilih
kelas → langsung tampil checklist siswa di halaman yang sama → centang
siswa → klik "Naikkan Kelas" atau "Luluskan" → modal konfirmasi kecil.
Sebelumnya tiap aksi buka halaman terpisah dengan checklist yang harus
diisi ulang; sekarang satu checklist dipakai untuk kedua aksi.

## Laporan
Karena sifatnya beda (lihat & ekspor, bukan tambah/ubah data), pola
modalnya tidak dipakai di sini — saya modernisasi tampilannya saja
(font Roboto, warna konsisten, tabel jadi kartu di HP) dan pertahankan
cara kerja filter & ekspor Excel yang sudah ada apa adanya, supaya
risiko error di bagian ekspor (yang menghasilkan file .xlsx) seminimal
mungkin.

Khusus **Laporan Per Kelas** (file paling kompleks, ~980 baris, tabel
pivot per-jenis-tagihan): saya sengaja **tidak menulis ulang total**,
hanya menyamakan warna & fontnya ke sistem desain baru. Strukturnya
lumayan rumit (rowspan/colspan dinamis mengikuti jumlah jenis tagihan)
dan sudah saya periksa aman (pengecekan kondisinya sudah benar sejak
awal), jadi menulis ulang total dari nol berisiko menimbulkan bug baru
tanpa manfaat tambahan yang sepadan — sesuai arahan Anda untuk
"seefisien mungkin dan tanpa eror".

## Pengujian
Semua 6 file sudah lolos: cek sintaks PHP, render nyata dengan data
kosong maupun terisi (termasuk kondisi "belum pilih apa-apa" yang
sebelumnya sempat ketahuan bikin warning di Kenaikan Kelas — sudah
saya perbaiki sebelum masuk paket ini), dan audit semua link/route
yang dipakai sudah dicocokkan satu-satu ke `Config/Routes.php`.

---

**Itu semua 13 modul admin.** Kalau ada halaman yang menurut Anda perlu
disempurnakan lagi setelah dicoba langsung di server, atau ada bug baru
yang ditemukan, tinggal kasih tahu.
