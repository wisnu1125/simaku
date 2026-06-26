<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== MODERN REPORT DASHBOARD ==================== */
:root {
    --primary: #0891b2;
    --primary-dark: #0e7490;
    --teal-soft: #f0fdfa;
    --text-main: #0f172a;
    --text-muted: #64748b;
}

body {
    background-color: #f8fafc;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.page-header {
    margin-bottom: 32px;
}

.page-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.5px;
}

/* Reports Grid */
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
}

/* Report Card Styling */
.report-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Efek Hover Halus */
.report-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    border-color: var(--primary);
}

/* Icon Container */
.report-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 24px;
    transition: transform 0.3s ease;
}

.report-card:hover .report-icon {
    transform: scale(1.1) rotate(-5deg);
}

/* Warna Gradient Icon */
.report-icon.primary { background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #0d9488; }
.report-icon.danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.report-icon.info { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }

.report-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 12px;
}

.report-desc {
    font-size: 14px;
    color: var(--text-muted);
    margin-bottom: 30px;
    line-height: 1.6;
    flex-grow: 1; /* Biar tombol sejajar di bawah */
}

/* Button Estetik */
.btn-report {
    width: 100%;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.2s;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.report-card:hover .btn-report {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.btn-report i {
    font-size: 12px;
    transition: transform 0.2s;
}

.btn-report:hover i {
    transform: translateX(4px);
}

/* Background Pattern (Dekorasi) */
.report-card::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    background: rgba(0,0,0,0.02);
    border-radius: 50%;
}

@media (max-width: 768px) {
    .reports-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <h1 class="page-title">Modul Laporan</h1>
</div>

<div class="reports-grid">
    <div class="report-card">
        <div class="report-icon primary">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h3 class="report-title">Laporan Pembayaran</h3>
        <p class="report-desc">
            Pantau arus kas masuk per periode. Dilengkapi filter tanggal dan export Excel untuk kebutuhan pembukuan.
        </p>
        <a href="<?= base_url('admin/laporan/pembayaran') ?>" class="btn-report">
            Lihat Laporan <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="report-card">
        <div class="report-icon danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="report-title">Laporan Tunggakan</h3>
        <p class="report-desc">
            Identifikasi siswa yang memiliki tagihan belum lunas. Tersedia data kontak wali untuk mempermudah penagihan.
        </p>
        <a href="<?= base_url('admin/laporan/tunggakan') ?>" class="btn-report">
            Lihat Laporan <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="report-card">
        <div class="report-icon info">
            <i class="fas fa-users"></i>
        </div>
        <h3 class="report-title">Laporan Per Kelas</h3>
        <p class="report-desc">
            Rekapitulasi progres pembayaran kolektif per kelas. Memudahkan monitoring persentase kelunasan siswa.
        </p>
        <a href="<?= base_url('admin/laporan/per-kelas') ?>" class="btn-report">
            Lihat Laporan <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>