<?= $this->include('admin/layouts/header') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<style>
/* ==================== BLUISH TEAL THEME ==================== */
:root {
    --primary: #0891b2;       /* Cyan 600 */
    --primary-hover: #0e7490; /* Cyan 700 */
    --primary-light: #cffafe; /* Cyan 100 */
    --primary-bg: #ecfeff;    /* Cyan 50 */
    --secondary: #64748b;
    --text-main: #0f172a;
    --border: #e2e8f0;
    --radius: 12px;
}

body {
    background: #f8fafc;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    color: var(--text-main);
}

/* ==================== PAGE HEADER ==================== */
.page-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px;
}
.page-title {
    font-size: 24px; font-weight: 800; color: var(--text-main);
    letter-spacing: -0.5px;
}

/* ==================== BUTTONS ==================== */
.btn {
    padding: 10px 20px; border-radius: 10px; text-decoration: none;
    font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
    transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
}
.btn-primary:hover {
    transform: translateY(-2px); box-shadow: 0 6px 15px rgba(8, 145, 178, 0.4);
}
.btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

/* Action Buttons in Table */
.btn-action {
    width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; transition: 0.2s; color: white; margin-right: 4px; border: none;
}
.btn-view { background: #3b82f6; box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3); }
.btn-edit { background: #f59e0b; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3); }
.btn-delete { background: #ef4444; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3); cursor: pointer; }
.btn-action:hover { transform: translateY(-2px); filter: brightness(110%); }

/* ==================== STATS ROW ==================== */
.stats-row {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px; margin-bottom: 24px;
}
.stat-box {
    background: white; border-radius: 16px; padding: 24px;
    border: 1px solid var(--border); box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    position: relative; overflow: hidden; display: flex; flex-direction: column;
}
/* Aksen Warna Stat Box */
.stat-box.blue { border-bottom: 4px solid var(--primary); }
.stat-box.green { border-bottom: 4px solid #10b981; }
.stat-box.indigo { border-bottom: 4px solid #6366f1; }

.stat-icon {
    position: absolute; top: 20px; right: 20px; font-size: 40px; opacity: 0.1;
    color: var(--text-main);
}
.stat-value { font-size: 32px; font-weight: 800; color: var(--text-main); line-height: 1; margin-bottom: 8px; }
.stat-label { font-size: 13px; font-weight: 600; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.5px; }

/* ==================== FILTER CARD ==================== */
.filter-card {
    background: white; border-radius: 16px; padding: 20px;
    border: 1px solid var(--border); margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.filter-grid {
    display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;
}
.filter-group label {
    display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; 
    color: var(--secondary); text-transform: uppercase;
}
.filter-input {
    width: 100%; padding: 10px 14px; border: 1px solid var(--border);
    border-radius: 10px; font-size: 14px; transition: 0.2s; background: #f8fafc;
}
.filter-input:focus {
    outline: none; border-color: var(--primary); background: white;
    box-shadow: 0 0 0 3px var(--primary-bg);
}

/* ==================== CARD TABLE ==================== */
.card-table {
    background: white; border-radius: 16px; border: 1px solid var(--border);
    overflow: hidden; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

/* ==================== DATATABLES CUSTOMIZATION (FIXED) ==================== */
/* Sembunyikan search & length bawaan */
.dataTables_wrapper .dataTables_filter, 
.dataTables_wrapper .dataTables_length { display: none; }

table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-top: 0 !important; }

/* Header Table */
table.dataTable thead th {
    background: #f1f5f9; color: var(--secondary); font-size: 12px; font-weight: 700;
    text-transform: uppercase; padding: 16px !important; border-bottom: 1px solid var(--border) !important;
}

/* Body Table */
table.dataTable tbody td {
    padding: 16px !important; border-bottom: 1px solid #f8fafc !important;
    font-size: 14px; color: var(--text-main); vertical-align: middle;
}
table.dataTable tbody tr:hover { background-color: var(--primary-bg) !important; }

/* --- PAGINATION STYLING --- */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 6px;
    padding-top: 10px;
}

/* Style Tombol Umum (Angka, Prev, Next) */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    padding: 8px 14px !important;
    border: 1px solid var(--border) !important;
    background: white !important;
    color: var(--secondary) !important;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer !important; /* WAJIB ADA AGAR BISA DIKLIK */
    user-select: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Style Saat Hover (Kecuali Disabled) */
.dataTables_wrapper .dataTables_paginate .paginate_button:not(.disabled):hover {
    background: var(--primary-bg) !important;
    color: var(--primary) !important;
    border-color: var(--primary-light) !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(8, 145, 178, 0.1);
}

/* Style Tombol Aktif (Halaman Sekarang) */
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
    color: white !important;
    border-color: var(--primary) !important;
    box-shadow: 0 4px 6px rgba(8, 145, 178, 0.25);
    cursor: default !important;
}

/* Style Tombol Disabled (Prev di hal 1, Next di hal akhir) */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
    cursor: not-allowed !important;
    opacity: 0.5;
    background: #f1f5f9 !important;
    color: #cbd5e1 !important;
    border-color: #e2e8f0 !important;
    transform: none !important;
    box-shadow: none !important;
}

.dataTables_wrapper .dataTables_info {
    margin-top: 24px; font-size: 13px; color: var(--secondary); font-weight: 500;
}

/* ==================== BADGES & UTILS ==================== */
.badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-indigo { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
.badge-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-gray { background: #f1f5f9; color: #64748b; }

.va-code {
    font-family: 'SF Mono', 'Monaco', monospace; background: #f1f5f9;
    padding: 4px 8px; border-radius: 6px; color: var(--primary); font-size: 12px; border: 1px solid var(--border);
}

/* Responsive */
@media (max-width: 768px) {
    .filter-grid { grid-template-columns: 1fr; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 16px; }
}
</style>

<div class="page-header">
    <h1 class="page-title">Data Siswa</h1>
    <a href="<?= base_url('admin/siswa/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Siswa
    </a>
</div>

<?php
// Hitung manual untuk statistik (karena pagination pake JS)
$totalSiswa = count($siswa);
$siswaAktif = count(array_filter($siswa, fn($s) => $s['status_siswa'] === 'aktif'));
$siswaLulus = count(array_filter($siswa, fn($s) => $s['status_siswa'] === 'lulus'));
?>

<div class="stats-row">
    <div class="stat-box blue">
        <i class="fas fa-users stat-icon"></i>
        <div class="stat-value"><?= number_format($totalSiswa) ?></div>
        <div class="stat-label">Total Siswa</div>
    </div>
    <div class="stat-box green">
        <i class="fas fa-user-check stat-icon" style="color: #10b981; opacity: 0.1;"></i>
        <div class="stat-value" style="color: #059669;"><?= number_format($siswaAktif) ?></div>
        <div class="stat-label">Siswa Aktif</div>
    </div>
    <div class="stat-box indigo">
        <i class="fas fa-user-graduate stat-icon" style="color: #6366f1; opacity: 0.1;"></i>
        <div class="stat-value" style="color: #4f46e5;"><?= number_format($siswaLulus) ?></div>
        <div class="stat-label">Siswa Lulus</div>
    </div>
</div>

<div class="filter-card">
    <div class="filter-grid">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Pencarian Cepat</label>
            <input type="text" id="customSearch" class="filter-input" placeholder="Cari Nama, NIS, atau VA...">
        </div>
        
        <div class="filter-group">
            <label><i class="fas fa-school"></i> Filter Kelas</label>
            <select id="filterKelas" class="filter-input">
                <option value="">Semua Kelas</option>
                <?php if (isset($kelas_list)): ?>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?= esc($k['nama_kelas']) ?>"><?= esc($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label><i class="fas fa-flag"></i> Filter Status</label>
            <select id="filterStatus" class="filter-input">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
                <option value="Lulus">Lulus</option>
            </select>
        </div>
    </div>
</div>

<div class="card-table">
    <table id="tableSiswa" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="8%">NIS</th>
                <th>Tanggal Lahir</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>VA (Virtual Account)</th>
                <th class="text-center">Status</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($siswa as $index => $s): ?>
            <tr>
                <td style="text-align: center; color: #64748b; font-weight: 600;"><?= $index + 1 ?></td>
                <td><strong style="color: var(--primary); font-family: monospace; font-size: 14px;"><?= esc($s['nis']) ?></strong></td>
                <td><?= date('d/m/Y', strtotime($s['tanggal_lahir'])) ?></td>
                <td>
                    <div style="font-weight: 700; color: var(--text-main);"><?= esc($s['nama_lengkap']) ?></div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                        <i class="fas fa-<?= $s['jenis_kelamin'] === 'L' ? 'mars' : 'venus' ?>"></i>
                        <?= $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($s['nama_kelas'])): ?>
                        <span class="badge badge-gray"><i class="fas fa-school" style="margin-right:4px"></i> <?= esc($s['nama_kelas']) ?></span>
                    <?php else: ?>
                        <span style="color: #94a3b8; font-size: 12px; font-style: italic;">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="va-code"><?= esc($s['virtual_account']) ?></span>
                </td>
                <td class="text-center">
                    <?php if ($s['status_siswa'] === 'aktif'): ?>
                        <span class="badge badge-success">Aktif</span>
                    <?php elseif ($s['status_siswa'] === 'lulus'): ?>
                        <span class="badge badge-indigo">Lulus</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Nonaktif</span>
                    <?php endif; ?>
                    <span style="display:none;"><?= ucfirst($s['status_siswa']) ?></span>
                </td>
                <td class="text-center">
                    <a href="<?= base_url('admin/siswa/detail/' . $s['id_siswa']) ?>" class="btn-action btn-view" title="Lihat">
                        <i class="fas fa-eye" style="font-size: 12px;"></i>
                    </a>
                    <a href="<?= base_url('admin/siswa/edit/' . $s['id_siswa']) ?>" class="btn-action btn-edit" title="Edit">
                        <i class="fas fa-pencil" style="font-size: 12px;"></i>
                    </a>
                    <form action="<?= base_url('admin/siswa/delete/' . $s['id_siswa']) ?>" method="POST" style="display: inline;">
                        <button type="submit" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin hapus siswa ini?')">
                            <i class="fas fa-trash" style="font-size: 12px;"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        var table = $('#tableSiswa').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "pagingType": "simple_numbers",
            "pageLength": 10,
            "lengthChange": false, 
            "dom": 'rtip', 
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": 7 } 
            ]
        });

        // 1. Custom Search Box (Pencarian Global)
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // 2. Custom Filter Kelas (KOLOM DIUBAH KE INDEX 4 KARENA ADA KOLOM BARU)
        $('#filterKelas').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(4).search(val ? val : '', true, false).draw();
        });

        // 3. Custom Filter Status (KOLOM DIUBAH KE INDEX 6 KARENA ADA KOLOM BARU)
        $('#filterStatus').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(6).search(val ? val : '', true, false).draw();
        });
    });
</script>

<?= $this->include('admin/layouts/footer') ?>