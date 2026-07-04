<?= $this->include('admin/layouts/header') ?>

<style>
/* ============================================================
   LAPORAN PER KELAS — Premium Teal Theme
   ============================================================ */
:root {
    --primary:       #0d9488;
    --primary-dark:  #0f766e;
    --primary-deep:  #115e59;
    --primary-light: #ccfbf1;
    --primary-bg:    #f0fdfa;
    --amber:         #d97706;
    --green:         #16a34a;
    --green-dark:    #15803d;
    --red:           #dc2626;
    --red-dark:      #b91c1c;
    --slate-900:     #0f172a;
    --slate-800:     #1e293b;
    --slate-600:     #475569;
    --slate-400:     #94a3b8;
    --slate-200:     #e2e8f0;
    --slate-100:     #f1f5f9;
    --slate-50:      #f8fafc;
    --white:         #ffffff;
    --radius-xl:     20px;
    --radius-lg:     14px;
    --radius-md:     10px;
    --shadow-sm:     0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:     0 4px 16px rgba(13,148,136,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg:     0 10px 40px rgba(13,148,136,.12), 0 4px 12px rgba(0,0,0,.06);
}

* { box-sizing: border-box; }
body { background: var(--slate-50); font-family: 'Roboto', sans-serif; color: var(--slate-800); }

/* ── Page Header ─────────────────────────────────────────── */
.ph-wrap {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 32px;
    flex-wrap: wrap;
}

.ph-left {}

.ph-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--primary);
    background: var(--primary-bg);
    border: 1px solid var(--primary-light);
    border-radius: 30px;
    padding: 5px 14px;
    margin-bottom: 12px;
}

.ph-title {
    font-size: 28px;
    font-weight: 900;
    color: var(--slate-900);
    letter-spacing: -0.8px;
    line-height: 1.15;
    margin: 0 0 8px;
}

.breadcrumb {
    font-size: 12.5px;
    color: var(--slate-400);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}
.breadcrumb a { color: var(--primary); text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb-sep { color: var(--slate-300); }

/* ── Filter Card ─────────────────────────────────────────── */
.filter-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--slate-200);
    box-shadow: var(--shadow-md);
    margin-bottom: 28px;
    overflow: hidden;
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 28px;
    background: linear-gradient(135deg, var(--slate-900) 0%, var(--primary-deep) 100%);
    border-bottom: 1px solid rgba(255,255,255,.08);
}

.filter-header-icon {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,.12);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 14px;
    flex-shrink: 0;
}

.filter-header-text {}
.filter-header-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--white);
    letter-spacing: .3px;
}
.filter-header-sub {
    font-size: 11px;
    color: rgba(255,255,255,.5);
    font-weight: 500;
    margin-top: 1px;
}

.filter-body { padding: 28px; }

.filter-section-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--slate-400);
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 1px dashed var(--slate-200);
}

.form-row-2 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    color: var(--slate-600);
    font-weight: 700;
    font-size: 12.5px;
}

.form-group label .req { color: var(--red); margin-left: 2px; }
.form-group label .opt {
    font-size: 10.5px;
    font-weight: 500;
    color: var(--slate-400);
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 11px 15px;
    border: 1.5px solid var(--slate-200);
    border-radius: var(--radius-md);
    font-size: 13.5px;
    background: var(--slate-50);
    color: var(--slate-800);
    transition: border-color .2s, box-shadow .2s, background .2s;
    font-family: inherit;
}
.form-control:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(8,145,178,.1);
}

/* Divider antara dua section filter */
.filter-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--slate-200), transparent);
    margin: 22px 0;
}

/* ── Action Row ──────────────────────────────────────────── */
.action-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    border-radius: var(--radius-md);
    font-size: 13.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all .25s;
    text-decoration: none;
    font-family: inherit;
    white-space: nowrap;
}
.btn i { font-size: 13px; }

.btn-primary {
    background: var(--primary);
    color: var(--white);
    box-shadow: 0 4px 14px rgba(8,145,178,.3);
}
.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(8,145,178,.35);
}

.btn-success {
    background: var(--green);
    color: var(--white);
    box-shadow: 0 4px 14px rgba(16,185,129,.25);
}
.btn-success:hover {
    background: var(--green-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16,185,129,.3);
}

.btn-ghost {
    background: var(--white);
    color: var(--slate-600);
    border: 1.5px solid var(--slate-200);
}
.btn-ghost:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-bg);
}

/* ── Info Banner ─────────────────────────────────────────── */
.info-banner {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 22px;
    background: linear-gradient(135deg, var(--primary-bg), #e0f2fe);
    border: 1px solid var(--primary-light);
    border-left: 4px solid var(--primary);
    border-radius: var(--radius-lg);
    margin-bottom: 24px;
}

.info-banner-icon {
    width: 40px;
    height: 40px;
    background: var(--white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.info-banner-body {}
.info-banner-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--slate-900);
    margin-bottom: 3px;
}
.info-banner-meta {
    font-size: 12px;
    color: var(--slate-600);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.info-banner-meta .chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--white);
    border: 1px solid var(--primary-light);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--primary-dark);
}

/* ── Stats Grid ──────────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}

.stat-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--slate-200);
    padding: 22px 24px;
    position: relative;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

/* accent strip kiri */
.stat-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    border-radius: 4px 0 0 4px;
}
.stat-card.sc-blue::before  { background: var(--primary); }
.stat-card.sc-green::before { background: var(--green); }
.stat-card.sc-red::before   { background: var(--red); }
.stat-card.sc-amber::before { background: var(--amber); }

/* decorative circle */
.stat-card::after {
    content: '';
    position: absolute;
    right: -20px; top: -20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    opacity: .06;
}
.stat-card.sc-blue::after  { background: var(--primary); }
.stat-card.sc-green::after { background: var(--green); }
.stat-card.sc-red::after   { background: var(--red); }
.stat-card.sc-amber::after { background: var(--amber); }

.stat-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    margin-bottom: 14px;
}
.sc-blue  .stat-icon { background: #e0f9ff; color: var(--primary); }
.sc-green .stat-icon { background: #d1fae5; color: var(--green-dark); }
.sc-red   .stat-icon { background: #fee2e2; color: var(--red-dark); }
.sc-amber .stat-icon { background: #fef3c7; color: #b45309; }

.stat-label {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--slate-400);
    margin-bottom: 6px;
}
.stat-value {
    font-size: 21px;
    font-weight: 900;
    color: var(--slate-900);
    line-height: 1.1;
    letter-spacing: -.5px;
}
.stat-sub {
    font-size: 11px;
    color: var(--slate-400);
    font-weight: 500;
    margin-top: 5px;
}

/* ── Table Card ──────────────────────────────────────────── */
.table-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--slate-200);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 28px;
}

.table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--slate-100);
    gap: 12px;
    flex-wrap: wrap;
}

.table-card-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--slate-900);
    display: flex;
    align-items: center;
    gap: 10px;
}
.table-card-title i {
    color: var(--primary);
    font-size: 15px;
}
.table-count-badge {
    background: var(--primary-bg);
    color: var(--primary-dark);
    border: 1px solid var(--primary-light);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Scrollbar */
.table-responsive::-webkit-scrollbar { height: 6px; }
.table-responsive::-webkit-scrollbar-track { background: var(--slate-100); }
.table-responsive::-webkit-scrollbar-thumb { background: var(--slate-800); border-radius: 6px; }
.table-responsive::-webkit-scrollbar-thumb:hover { background: var(--slate-900); }

table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap;
    font-size: 13px;
}

/* Sticky kolom kiri */
table thead th:nth-child(1),
table thead th:nth-child(2),
table thead th:nth-child(3),
table tbody td:nth-child(1),
table tbody td:nth-child(2),
table tbody td:nth-child(3),
table tfoot td:nth-child(1) {
    position: sticky;
    z-index: 2;
    background: inherit;
}
table thead th:nth-child(1), table tbody td:nth-child(1), table tfoot td:nth-child(1) { left: 0; }
table thead th:nth-child(2), table tbody td:nth-child(2) { left: 52px; }
table thead th:nth-child(3), table tbody td:nth-child(3) { left: 122px; }

/* Override bg sticky di thead */
table thead th:nth-child(1),
table thead th:nth-child(2),
table thead th:nth-child(3) { background: var(--primary); }

/* Thead */
table thead tr:first-child th {
    background: var(--primary);
    color: var(--white);
    padding: 13px 14px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    border: none;
    border-right: 1px solid rgba(255,255,255,.1);
    border-bottom: 1px solid rgba(255,255,255,.1);
}

table thead tr:last-child th {
    background: var(--primary-dark);
    color: rgba(255,255,255,.85);
    padding: 9px 14px;
    font-size: 10.5px;
    font-weight: 700;
    border: none;
    border-right: 1px solid rgba(255,255,255,.08);
}

/* Grand total header */
table thead th.th-grand {
    background: var(--slate-900) !important;
}
table thead th.th-grand-sub {
    background: var(--slate-800) !important;
}

/* Tbody */
table tbody td {
    padding: 11px 14px;
    border-bottom: 1px solid var(--slate-100);
    border-right: 1px solid var(--slate-100);
    color: var(--slate-800);
    vertical-align: middle;
}

table tbody tr:hover td {
    background: #f0fbff !important;
}

/* Zebra */
table tbody tr:nth-child(even) td { background: var(--slate-50); }
table tbody tr:nth-child(odd) td  { background: var(--white); }

/* Sticky override on hover */
table tbody tr:hover td:nth-child(1),
table tbody tr:hover td:nth-child(2),
table tbody tr:hover td:nth-child(3) { background: #e0f8ff !important; }

/* Tfoot */
table tfoot td {
    padding: 13px 14px;
    font-weight: 800;
    font-size: 13px;
    background: var(--slate-900) !important;
    color: var(--white);
    border-right: 1px solid rgba(255,255,255,.08);
}

/* Cells styling */
.cell-no {
    text-align: center;
    font-weight: 700;
    color: var(--slate-400);
    font-size: 12px;
    width: 52px;
}
.cell-nis {
    font-family: 'Roboto Mono', monospace;
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-600);
    width: 70px;
}
.cell-name .name-text {
    font-weight: 700;
    color: var(--slate-900);
    font-size: 13px;
    min-width: 200px;
}

.cell-tagihan  { text-align: right; background: var(--slate-50) !important; color: var(--slate-700); }
.cell-dibayar  { text-align: right; background: #f0fdf4 !important; color: #166534; font-weight: 600; }
.cell-sisa     { text-align: right; background: #fff5f5 !important; color: #991b1b; font-weight: 600; }

.cell-tot-tag  { text-align: right; background: #f1f5f9 !important; font-weight: 800; color: var(--slate-800); }
.cell-tot-pay  { text-align: right; background: #ecfdf5 !important; font-weight: 800; color: var(--green-dark); }
.cell-tot-tung { text-align: right; background: #fff1f2 !important; font-weight: 800; color: var(--red-dark); }

/* Override sticky background di cell zebra/hover */
table tbody tr:nth-child(even) td:nth-child(2),
table tbody tr:nth-child(even) td:nth-child(3) { background: var(--slate-50); }
table tbody tr:nth-child(odd) td:nth-child(2),
table tbody tr:nth-child(odd) td:nth-child(3)  { background: var(--white); }

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.badge-lunas    { background: #d1fae5; color: #065f46; }
.badge-cicil    { background: #fef3c7; color: #92400e; }
.badge-nunggak  { background: #fee2e2; color: #991b1b; }

/* ── Empty State ─────────────────────────────────────────── */
.empty-wrap {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--slate-200);
    padding: 70px 40px;
    text-align: center;
    box-shadow: var(--shadow-sm);
}
.empty-icon-ring {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: var(--primary-bg);
    border: 2px dashed var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
    font-size: 32px;
    color: var(--primary);
    opacity: .7;
}
.empty-title { font-size: 20px; font-weight: 900; color: var(--slate-900); margin-bottom: 10px; }
.empty-sub   { font-size: 14px; color: var(--slate-400); font-weight: 500; max-width: 400px; margin: 0 auto; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 640px) {
    .ph-title { font-size: 22px; }
    .filter-body { padding: 20px; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .action-row { flex-direction: column; align-items: stretch; }
    .btn { justify-content: center; }
}
</style>

<!-- ══════════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════════ -->
<div class="ph-wrap">
    <div class="ph-left">
        <div class="ph-eyebrow">
            <i class="fas fa-chart-bar"></i>
            Laporan Keuangan
        </div>
        <h1 class="ph-title">Analisa Per Kelas</h1>
        <div class="breadcrumb">
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="<?= base_url('admin/laporan') ?>">Laporan</a>
            <span class="breadcrumb-sep">›</span>
            <span>Per Kelas</span>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     FILTER CARD
══════════════════════════════════════════════════════════ -->
<div class="filter-card">
    <div class="filter-header">
        <div class="filter-header-icon">
            <i class="fas fa-sliders-h"></i>
        </div>
        <div class="filter-header-text">
            <div class="filter-header-title">Konfigurasi Laporan</div>
            <div class="filter-header-sub">Pilih kelas, tahun ajaran, dan cut-off tanggal</div>
        </div>
    </div>

    <div class="filter-body">
        <form action="<?= base_url('admin/laporan/per-kelas') ?>" method="GET">

            <div class="filter-section-label">
                <i class="fas fa-school" style="margin-right:5px;"></i> Identitas Kelas
            </div>
            <div class="form-row-2" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="tahun_ajaran_id">Tahun Ajaran <span class="req">*</span></label>
                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control" required>
                        <option value="">— Pilih Tahun Ajaran —</option>
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_tahun_ajaran'] ?>"
                                <?= (isset($tahun_ajaran_id) && $tahun_ajaran_id == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                                <?= esc($ta['nama_tahun_ajaran']) ?>
                                <?= $ta['status'] === 'aktif' ? ' (Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kelas_id">Kelas <span class="req">*</span></label>
                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                        <option value="">— Pilih Kelas —</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>"
                                data-tahun="<?= $k['id_tahun_ajaran'] ?>"
                                <?= (isset($kelas_id) && $kelas_id == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="filter-divider"></div>

            <div class="filter-section-label">
                <i class="fas fa-calendar-check" style="margin-right:5px;"></i> Cut-off Tanggal Laporan
                <span style="font-weight:500; text-transform:none; letter-spacing:0; margin-left:6px; color: var(--slate-400);">— hitung pembayaran s.d. tanggal ini, kosongkan untuk semua waktu</span>
            </div>
            <div class="form-row-2" style="grid-template-columns: minmax(240px, 400px);">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tanggal_akhir">Per Tanggal <span class="opt">(opsional)</span></label>
                    <div style="position: relative;">
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                               value="<?= esc($tanggal_akhir ?? '') ?>"
                               style="padding-left: 42px;">
                        <i class="fas fa-calendar-day" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--primary); font-size:14px; pointer-events:none;"></i>
                    </div>
                    <div style="margin-top: 6px; font-size: 11.5px; color: var(--slate-400); font-weight: 500;">
                        <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                        Contoh: isi <strong>31 Desember 2024</strong> → laporan tampilkan semua bayar hingga akhir tahun
                    </div>
                </div>
            </div>

            <div class="action-row">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Generate Laporan
                </button>

                <?php if (isset($kelas_id) && isset($tahun_ajaran_id)): ?>
                    <?php
                        $exportUrl = base_url('admin/laporan/per-kelas/export')
                            . '?kelas_id=' . $kelas_id
                            . '&tahun_ajaran_id=' . $tahun_ajaran_id
                            . ((!empty($tanggal_akhir)) ? '&tanggal_akhir=' . $tanggal_akhir : '');
                    ?>
                    <a href="<?= $exportUrl ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </a>
                    <a href="<?= base_url('admin/laporan/per-kelas') ?>" class="btn btn-ghost">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>

<?php if (isset($kelas_id) && isset($tahun_ajaran_id) && isset($siswa)): ?>

    <?php
        /* helper: cari nama dari array */
        $active_kelas = '';
        foreach ($kelas as $k) { if ($k['id_kelas'] == $kelas_id) { $active_kelas = $k['nama_kelas']; break; } }
        $active_ta = '';
        foreach ($tahun_ajaran as $ta) { if ($ta['id_tahun_ajaran'] == $tahun_ajaran_id) { $active_ta = $ta['nama_tahun_ajaran']; break; } }
    ?>

    <!-- INFO BANNER -->
    <div class="info-banner">
        <div class="info-banner-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-banner-body">
            <div class="info-banner-title">Menampilkan Data Laporan</div>
            <div class="info-banner-meta">
                <span class="chip"><i class="fas fa-door-open"></i> <?= esc($active_kelas) ?></span>
                <span class="chip"><i class="fas fa-calendar"></i> <?= esc($active_ta) ?></span>
                <?php if (!empty($tanggal_akhir)): ?>
                    <span class="chip" style="color: #b45309; border-color: #fde68a; background: #fefce8;">
                        <i class="fas fa-calendar-check"></i>
                        Per <?= date('d M Y', strtotime($tanggal_akhir)) ?>
                    </span>
                <?php else: ?>
                    <span class="chip" style="color: var(--slate-400); border-color: var(--slate-200);">
                        <i class="fas fa-infinity"></i> Semua Waktu
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card sc-blue">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Populasi Siswa</div>
            <div class="stat-value"><?= count($siswa) ?></div>
            <div class="stat-sub">siswa aktif</div>
        </div>
        <div class="stat-card sc-amber">
            <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-label">Omzet Tagihan</div>
            <div class="stat-value" style="font-size:17px;">Rp <?= number_format($total_tagihan ?? 0, 0, ',', '.') ?></div>
            <div class="stat-sub">total kewajiban</div>
        </div>
        <div class="stat-card sc-green">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label">Realisasi Bayar</div>
            <div class="stat-value" style="font-size:17px; color: var(--green-dark);">Rp <?= number_format($total_dibayar ?? 0, 0, ',', '.') ?></div>
            <div class="stat-sub">
                <?php
                    $pct = ($total_tagihan > 0) ? round(($total_dibayar / $total_tagihan) * 100) : 0;
                    echo $pct . '% terpenuhi';
                ?>
            </div>
        </div>
        <div class="stat-card sc-red">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-label">Outstanding</div>
            <div class="stat-value" style="font-size:17px; color: var(--red-dark);">Rp <?= number_format($total_tunggakan ?? 0, 0, ',', '.') ?></div>
            <div class="stat-sub">tunggakan tersisa</div>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <i class="fas fa-table"></i>
                Rekap Detail Pembayaran
                <span class="table-count-badge"><?= count($siswa) ?> siswa</span>
            </div>
            <div style="font-size: 12px; color: var(--slate-400); font-weight: 500;">
                <i class="fas fa-arrows-alt-h" style="margin-right:4px;"></i> Geser untuk melihat semua kolom
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; text-align: center;">NO</th>
                        <th rowspan="2" style="vertical-align: middle;">NIS</th>
                        <th rowspan="2" style="vertical-align: middle; min-width: 200px;">NAMA SISWA</th>

                        <?php if (!empty($jenis_tagihan)): ?>
                            <?php foreach ($jenis_tagihan as $jt): ?>
                                <th colspan="3" style="text-align: center;">
                                    <?= esc($jt['nama_tagihan']) ?>
                                </th>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <th colspan="3" class="th-grand" style="text-align: center;">
                            GRAND TOTAL
                        </th>
                        <th rowspan="2" style="vertical-align: middle; text-align: center; min-width: 100px;">STATUS</th>
                    </tr>
                    <tr>
                        <?php if (!empty($jenis_tagihan)): ?>
                            <?php foreach ($jenis_tagihan as $jt): ?>
                                <th style="text-align: right;">Tagihan</th>
                                <th style="text-align: right;">Dibayar</th>
                                <th style="text-align: right;">Sisa</th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <th class="th-grand-sub" style="text-align: right;">Tagihan</th>
                        <th class="th-grand-sub" style="text-align: right;">Bayar</th>
                        <th class="th-grand-sub" style="text-align: right;">Tunggakan</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($siswa)): ?>
                        <tr>
                            <td colspan="100" style="text-align: center; padding: 50px; color: var(--slate-400);">
                                <i class="fas fa-users-slash" style="font-size: 36px; display: block; margin-bottom: 12px; opacity: .3;"></i>
                                Tidak ada siswa di kelas ini
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($siswa as $index => $s): ?>
                            <tr>
                                <td class="cell-no"><?= $index + 1 ?></td>
                                <td class="cell-nis"><?= esc($s['nis']) ?></td>
                                <td class="cell-name">
                                    <div class="name-text"><?= esc($s['nama_lengkap']) ?></div>
                                </td>

                                <?php if (!empty($jenis_tagihan)): ?>
                                    <?php foreach ($jenis_tagihan as $jt): ?>
                                        <?php $d = $s['tagihan_detail'][$jt['id_jenis_tagihan']] ?? null; ?>
                                        <td class="cell-tagihan">
                                            <?= $d ? number_format($d['nominal_akhir'],   0, ',', '.') : '—' ?>
                                        </td>
                                        <td class="cell-dibayar">
                                            <?= $d ? number_format($d['nominal_dibayar'], 0, ',', '.') : '—' ?>
                                        </td>
                                        <td class="cell-sisa">
                                            <?= $d ? number_format($d['sisa_tagihan'],    0, ',', '.') : '—' ?>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <td class="cell-tot-tag"><?= number_format($s['total_tagihan'],   0, ',', '.') ?></td>
                                <td class="cell-tot-pay"><?= number_format($s['total_dibayar'],   0, ',', '.') ?></td>
                                <td class="cell-tot-tung"><?= number_format($s['total_tunggakan'],0, ',', '.') ?></td>

                                <td style="text-align: center;">
                                    <?php if ($s['total_tunggakan'] <= 0): ?>
                                        <span class="badge badge-lunas"><i class="fas fa-check"></i> Lunas</span>
                                    <?php elseif ($s['total_dibayar'] > 0): ?>
                                        <span class="badge badge-cicil"><i class="fas fa-minus"></i> Mencicil</span>
                                    <?php else: ?>
                                        <span class="badge badge-nunggak"><i class="fas fa-times"></i> Nunggak</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <?php if (!empty($siswa)): ?>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; letter-spacing: .5px; font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,.6); padding-right: 20px;">
                            Rekapitulasi Akhir
                        </td>

                        <?php if (!empty($jenis_tagihan)): ?>
                            <?php
                                $totPerJenis = [];
                                foreach ($jenis_tagihan as $jt) {
                                    $totPerJenis[$jt['id_jenis_tagihan']] = ['tagihan'=>0,'dibayar'=>0,'tunggakan'=>0];
                                }
                                foreach ($siswa as $s) {
                                    foreach ($jenis_tagihan as $jt) {
                                        $d = $s['tagihan_detail'][$jt['id_jenis_tagihan']] ?? null;
                                        if ($d) {
                                            $totPerJenis[$jt['id_jenis_tagihan']]['tagihan']   += $d['nominal_akhir'];
                                            $totPerJenis[$jt['id_jenis_tagihan']]['dibayar']   += $d['nominal_dibayar'];
                                            $totPerJenis[$jt['id_jenis_tagihan']]['tunggakan'] += $d['sisa_tagihan'];
                                        }
                                    }
                                }
                            ?>
                            <?php foreach ($jenis_tagihan as $jt): ?>
                                <td style="text-align: right;"><?= number_format($totPerJenis[$jt['id_jenis_tagihan']]['tagihan'],   0, ',', '.') ?></td>
                                <td style="text-align: right; color: #6ee7b7;"><?= number_format($totPerJenis[$jt['id_jenis_tagihan']]['dibayar'],   0, ',', '.') ?></td>
                                <td style="text-align: right; color: #fca5a5;"><?= number_format($totPerJenis[$jt['id_jenis_tagihan']]['tunggakan'], 0, ',', '.') ?></td>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <td style="text-align: right;"><?= number_format($total_tagihan   ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right; color: #6ee7b7;"><?= number_format($total_dibayar   ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right; color: #fca5a5;"><?= number_format($total_tunggakan ?? 0, 0, ',', '.') ?></td>

                        <td style="text-align: center; color: rgba(255,255,255,.4); font-size: 12px;">—</td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

<?php else: ?>

    <!-- EMPTY STATE -->
    <div class="empty-wrap">
        <div class="empty-icon-ring">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="empty-title">Laporan Siap Di-generate</div>
        <div class="empty-sub">
            Pilih tahun ajaran dan kelas di atas, lalu opsional isi tanggal cut-off untuk melihat realisasi pembayaran hingga tanggal tertentu.
        </div>
    </div>

<?php endif; ?>

<script>
/* Filter kelas berdasarkan tahun ajaran */
document.getElementById('tahun_ajaran_id')?.addEventListener('change', function () {
    const val     = this.value;
    const sel     = document.getElementById('kelas_id');
    const opts    = sel.querySelectorAll('option');

    opts.forEach(opt => {
        if (!opt.value) { opt.style.display = ''; return; }
        opt.style.display = (!val || opt.dataset.tahun === val) ? '' : 'none';
    });
    sel.value = '';
});

/* Jalankan saat load agar sync dengan nilai terpilih */
window.addEventListener('load', () => {
    const ta = document.getElementById('tahun_ajaran_id');
    if (ta?.value) ta.dispatchEvent(new Event('change'));
});
</script>

<?= $this->include('admin/layouts/footer') ?>