<?= $this->include('admin/layouts/header') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<style>
/* ==================== CLEAN BLUISH TEAL THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-bg: #ecfeff;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
}

body { 
    background: #f8fafc; 
    font-family: 'Inter', sans-serif; 
    color: var(--text-main);
}

/* Page Header */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 24px; font-weight: 700; color: #0f172a; }

/* Buttons */
.btn-primary { 
    padding: 10px 20px; border-radius: 8px; text-decoration: none; 
    font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; 
    transition: background 0.2s ease;
    background: var(--primary); color: white; border: none; cursor: pointer;
}
.btn-primary:hover { background: var(--primary-hover); }

/* Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px; }
.stat-box { 
    background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border); 
    transition: border-color 0.2s ease;
}
.stat-box:hover { border-color: var(--primary); }
.stat-value { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
.stat-label { font-size: 11px; font-weight: 700; color: var(--secondary); text-transform: uppercase; }

/* Filter Card */
.filter-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border); margin-bottom: 24px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: flex-end; }
.filter-group label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: var(--secondary); text-transform: uppercase; }
.filter-input { 
    width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; 
    font-size: 14px; background: #f8fafc; transition: border-color 0.2s;
}
.filter-input:focus { outline: none; border-color: var(--primary); background: white; }

/* Table Styles */
.card-table { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.dataTables_wrapper .dataTables_filter { display: none; }

table.dataTable thead th { 
    background: #f1f5f9; color: var(--secondary); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; padding: 12px !important; border-bottom: 1px solid var(--border) !important;
}
table.dataTable tbody tr { transition: background 0.15s; }
table.dataTable tbody tr:hover { background-color: var(--primary-bg) !important; }

/* Badges & UI Elements */
.badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #15803d; }
.badge-danger { background: #fee2e2; color: #b91c1c; }
.badge-info { background: #e0f2fe; color: #0369a1; }

.nominal-price { font-weight: 700; color: var(--primary); font-family: 'SF Mono', monospace; }
.kwitansi-code { font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: var(--text-main); font-weight: 600; font-size: 12px; }

/* Search Box */
.search-input-client { 
    width: 300px; padding: 10px 14px; border: 1px solid var(--border); 
    border-radius: 8px; font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.search-input-client:focus { outline: none; border-color: var(--primary); background: white; width: 320px; }

@media (max-width: 768px) { .filter-grid { grid-template-columns: 1fr; } .search-input-client { width: 100%; } }

/* Styling teks di dalam tabel agar lebih profesional */
.text-main-bold {
    display: block;
    font-weight: 700;
    color: #0f172a;
    font-size: 14px;
    margin-bottom: 2px;
}

.text-sub-label {
    display: block;
    font-size: 11px;
    font-weight: 500;
    color: #64748b;
    letter-spacing: 0.02em;
}

.kwitansi-wrapper {
    display: inline-flex;
    flex-direction: column;
}

.nominal-bayar {
    font-family: 'SF Mono', 'Roboto Mono', monospace;
    font-weight: 800;
    color: var(--primary);
    font-size: 14px;
}

/* Garis halus pemisah di baris tabel */
table.dataTable tbody td {
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 14px 12px !important;
}
</style>

<div class="page-header">
    <h1 class="page-title">Riwayat Pembayaran</h1>
    <a href="<?= base_url('admin/pembayaran/create') ?>" class="btn-primary">
        <i class="fas fa-plus"></i> Input Pembayaran
    </a>
</div>

<?php
$totalValid = 0;
$totalDibatalkan = 0;
$countValid = 0;
foreach ($pembayaran as $p) {
    if ($p['status_pembayaran'] === 'valid') {
        $totalValid += $p['nominal_bayar'];
        $countValid++;
    } else {
        $totalDibatalkan += $p['nominal_bayar'];
    }
}
?>

<div class="stats-row">
    <div class="stat-box" style="border-left: 4px solid #10b981;">
        <div class="stat-label">Total Valid</div>
        <div class="stat-value" style="color: #10b981;">Rp <?= number_format($totalValid, 0, ',', '.') ?></div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #3b82f6;">
        <div class="stat-label">Transaksi Berhasil</div>
        <div class="stat-value" style="color: #3b82f6;"><?= $countValid ?> Transaksi</div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #ef4444;">
        <div class="stat-label">Total Dibatalkan</div>
        <div class="stat-value" style="color: #ef4444;">Rp <?= number_format($totalDibatalkan, 0, ',', '.') ?></div>
    </div>
</div>

<div class="filter-card">
    <form method="GET" action="<?= base_url('admin/pembayaran') ?>" id="filterForm">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Status</label>
                <select name="filter_status" class="filter-input" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="valid" <?= ($filter_status == 'valid') ? 'selected' : '' ?>>Valid</option>
                    <option value="dibatalkan" <?= ($filter_status == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Metode</label>
                <select name="filter_metode" class="filter-input" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="tunai" <?= ($filter_metode == 'tunai') ? 'selected' : '' ?>>Tunai</option>
                    <option value="transfer" <?= ($filter_metode == 'transfer') ? 'selected' : '' ?>>Transfer</option>
                </select>
            </div>
            <div class="filter-group" style="margin-left: auto;">
                <a href="<?= base_url('admin/pembayaran') ?>" style="color: var(--secondary); font-size: 13px; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-undo"></i> Reset Filter
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-table">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 700;">Data Transaksi</h3>
        <input type="text" id="customSearch" class="search-input-client" placeholder="Cari NIS, Nama, atau Kwitansi...">
    </div>

    <table id="tablePembayaran" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Kwitansi</th>
                <th>Siswa</th>
                <th>Tagihan</th>
                <th style="text-align: right;">Nominal</th>
                <th style="text-align: center;">Metode</th>
                <th style="text-align: center;">Status</th>
                <th width="8%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pembayaran as $index => $p): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <div style="font-weight:600; font-size: 13px;"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></div>
                    <div style="font-size: 11px; color: var(--secondary);"><?= date('H:i', strtotime($p['tanggal_bayar'])) ?> WIB</div>
                </td>
                <td><span class="kwitansi-code"><?= esc($p['nomor_kwitansi']) ?></span></td>
                <td>
                    <div style="font-weight:700;"><?= esc($p['nama_siswa']) ?></div>
                    <div style="font-size:11px; color:var(--secondary);">NIS: <?= esc($p['nis']) ?></div>
                </td>
                <td>
                    <div style="font-weight:600; font-size:13px;"><?= esc($p['nama_tagihan']) ?></div>
                    <div style="font-size:11px; color:var(--secondary);"><?= esc($p['nama_tahun_ajaran']) ?></div>
                </td>
                <td style="text-align: right;"><span class="nominal-price">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></span></td>
                <td class="text-center">
                    <?php if ($p['metode_pembayaran'] === 'tunai'): ?>
                        <span class="badge badge-success">TUNAI</span>
                    <?php else: ?>
                        <span class="badge badge-info">TRANSFER</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ($p['status_pembayaran'] === 'valid'): ?>
                        <span class="badge badge-success">VALID</span>
                    <?php else: ?>
                        <span class="badge badge-danger">BATAL</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <a href="<?= base_url('admin/pembayaran/detail/' . $p['id_pembayaran']) ?>" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">
                        Detail
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        var table = $('#tablePembayaran').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "dom": 'rtip', 
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": 8 }
            ]
        });

        // Live search logic
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

<?= $this->include('admin/layouts/footer') ?>