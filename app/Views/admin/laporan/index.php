<?= $this->include('admin/layouts/header') ?>

<style>
.report-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
@media (min-width: 768px) { .report-grid { grid-template-columns: repeat(3, 1fr); } }
.report-card { padding: 26px 22px; text-decoration: none; display: block; transition: transform .15s, box-shadow .15s; }
.report-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.report-card .icon { width: 48px; height: 48px; border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 16px; }
.report-card h3 { font-size: 16px; margin-bottom: 6px; }
.report-card p { font-size: 12.5px; color: var(--muted); line-height: 1.6; margin-bottom: 16px; }
.report-card .go { font-size: 12.5px; font-weight: 700; color: var(--brand); }
</style>

<div class="page-title" style="margin-bottom:4px;">Laporan</div>
<div class="page-subtitle" style="margin-bottom:22px;">Pilih laporan yang ingin dilihat atau diekspor ke Excel.</div>

<div class="report-grid">
    <a href="<?= base_url('admin/laporan/pembayaran') ?>" class="card report-card">
        <div class="icon" style="background:var(--success-bg); color:var(--success);"><i class="fa-solid fa-money-bill-trend-up"></i></div>
        <h3>Laporan Pembayaran</h3>
        <p>Rekap seluruh transaksi pembayaran dalam rentang tanggal tertentu, lengkap dengan metode dan petugas yang menerima.</p>
        <span class="go">Buka laporan <i class="fa-solid fa-arrow-right"></i></span>
    </a>
    <a href="<?= base_url('admin/laporan/tunggakan') ?>" class="card report-card">
        <div class="icon" style="background:var(--danger-bg); color:var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3>Laporan Tunggakan</h3>
        <p>Daftar siswa dengan tagihan belum lunas, dikelompokkan per siswa dan diurutkan dari tunggakan terbesar.</p>
        <span class="go">Buka laporan <i class="fa-solid fa-arrow-right"></i></span>
    </a>
    <a href="<?= base_url('admin/laporan/per-kelas') ?>" class="card report-card">
        <div class="icon" style="background:var(--info-bg); color:var(--info);"><i class="fa-solid fa-chalkboard"></i></div>
        <h3>Laporan Per Kelas</h3>
        <p>Rincian tagihan &amp; pembayaran seluruh siswa dalam satu kelas, per jenis tagihan, dalam satu tampilan.</p>
        <span class="go">Buka laporan <i class="fa-solid fa-arrow-right"></i></span>
    </a>
</div>

<?= $this->include('admin/layouts/footer') ?>
