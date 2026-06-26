<?= $this->include('admin/layouts/header') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<style>
/* ==================== CLEAN BLUISH TEAL THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
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

/* Button - Animasi Ringan (Hanya transisi warna) */
.btn-generate { 
    padding: 10px 20px; border-radius: 8px; text-decoration: none; 
    font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; 
    transition: background 0.2s ease;
    background: var(--primary); color: white;
}
.btn-generate:hover { background: var(--primary-hover); }

/* Tombol Hapus Terpilih */
.btn-delete-selected {
    padding: 10px 20px; border-radius: 8px; border: none;
    font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
    transition: all 0.2s ease;
    background: #ef4444; color: white; cursor: pointer;
    opacity: 0.5; pointer-events: none;
}
.btn-delete-selected.active {
    opacity: 1; pointer-events: auto;
}
.btn-delete-selected.active:hover {
    background: #dc2626;
}

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
.filter-group label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: var(--secondary); }
.filter-input { 
    width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; 
    font-size: 14px; background: #f8fafc; transition: border-color 0.2s;
}
.filter-input:focus { outline: none; border-color: var(--primary); background: white; }

/* Table Styles */
.card-table { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 20px; }
.dataTables_wrapper .dataTables_filter { display: none; }

table.dataTable thead th { 
    background: #f1f5f9; color: var(--secondary); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; padding: 12px !important;
}
table.dataTable tbody tr { transition: background 0.15s; }
table.dataTable tbody tr:hover { background-color: #f0f9ff !important; }

/* Badges */
.badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-success { background: #dcfce7; color: #15803d; }
.badge-warning { background: #ffedd5; color: #c2410c; }
.badge-danger { background: #fee2e2; color: #b91c1c; }

/* Search Box */
.search-input-client { 
    width: 300px; padding: 10px 14px; border: 1px solid var(--border); 
    border-radius: 8px; font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.search-input-client:focus { outline: none; border-color: var(--primary); background: white; width: 320px; }

/* Icon Button */
.btn-action { width: 30px; height: 30px; border-radius: 6px; color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: opacity 0.2s; }
.btn-view { background: #3b82f6; }
.btn-delete { background: #ef4444; }
.btn-action:hover { opacity: 0.8; }

/* Checkbox Styling */
input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--primary);
}

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

/* Counter untuk checkbox terpilih */
.selection-counter {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--secondary);
    font-weight: 600;
}
</style>

<div class="page-header">
    <h1 class="page-title">Monitoring Tagihan</h1>
    <a href="<?= base_url('admin/tagihan/generate') ?>" class="btn-generate">
        <i class="fas fa-bolt"></i> Generate Tagihan
    </a>
</div>

<?php
$t_tagihan = 0; $t_bayar = 0; $t_sisa = 0;
foreach ($tagihan as $t) {
    $t_tagihan += $t['nominal_akhir'];
    $t_bayar += $t['nominal_dibayar'];
    $t_sisa += $t['sisa_tagihan'];
}
?>

<div class="stats-row">
    <div class="stat-box" style="border-left: 4px solid #3b82f6;">
        <div class="stat-label">Total Piutang</div>
        <div class="stat-value">Rp <?= number_format($t_tagihan, 0, ',', '.') ?></div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #10b981;">
        <div class="stat-label">Terbayar</div>
        <div class="stat-value" style="color: #10b981;">Rp <?= number_format($t_bayar, 0, ',', '.') ?></div>
    </div>
    <div class="stat-box" style="border-left: 4px solid #ef4444;">
        <div class="stat-label">Tunggakan</div>
        <div class="stat-value" style="color: #ef4444;">Rp <?= number_format($t_sisa, 0, ',', '.') ?></div>
    </div>
</div>

<div class="filter-card">
    <form method="GET" action="<?= base_url('admin/tagihan') ?>">
        <div class="filter-grid">
            <div class="filter-group">
                <label>TAHUN AJARAN</label>
                <select name="filter_tahun_ajaran" class="filter-input" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= ($filter_tahun_ajaran == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>KELAS</label>
                <select name="filter_kelas" class="filter-input" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= ($filter_kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <a href="<?= base_url('admin/tagihan') ?>" style="color: var(--secondary); font-size: 13px; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-table">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <h3 style="font-size: 16px; font-weight: 700;">Daftar Rincian</h3>
            <button type="button" id="btnDeleteSelected" class="btn-delete-selected">
                <i class="fas fa-trash"></i> 
                <span class="selection-counter">Hapus <span id="selectedCount">0</span> Terpilih</span>
            </button>
        </div>
        <input type="text" id="customSearch" class="search-input-client" placeholder="Cari nama atau NIS...">
    </div>

    <form id="formBulkDelete" method="POST" action="<?= base_url('admin/tagihan/bulk-delete') ?>">
        <?= csrf_field() ?>
        <table id="tableTagihan" class="display" style="width:100%">
            <thead>
                <tr>
                    <th width="3%"><input type="checkbox" id="selectAll"></th>
                    <th width="5%">No</th>
                    <th>Siswa</th>
                    <th>Tagihan</th>
                    <th style="text-align: right;">Nominal</th>
                    <th style="text-align: right;">Sisa</th>
                    <th style="text-align: center;">Status</th>
                    <th width="8%" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tagihan as $index => $t): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($t['status_tagihan'] === 'belum_bayar'): ?>
                            <input type="checkbox" class="checkbox-item" name="id_tagihan[]" value="<?= $t['id_tagihan'] ?>">
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td>
                        <div style="font-weight:700;"><?= esc($t['nama_siswa']) ?></div>
                        <div style="font-size:11px; color:var(--secondary);"><?= esc($t['nis']) ?> • <?= esc($t['nama_kelas']) ?></div>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:13px;"><?= esc($t['nama_tagihan']) ?></div>
                        <?php if($t['bulan_tagihan']): ?><span style="font-size:10px; color:var(--secondary);">Bulan <?= $t['bulan_tagihan'] ?></span><?php endif; ?>
                    </td>
                    <td style="text-align: right; font-weight: 700;">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: 700; color: #ef4444;">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php if ($t['status_tagihan'] === 'lunas'): ?>
                            <span class="badge badge-success">LUNAS</span>
                        <?php elseif ($t['status_tagihan'] === 'cicil'): ?>
                            <span class="badge badge-warning">CICIL</span>
                        <?php else: ?>
                            <span class="badge badge-danger">BELUM</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div style="display: flex; gap: 4px; justify-content: center;">
                            <a href="<?= base_url('admin/tagihan/detail/'.$t['id_siswa']) ?>" class="btn-action btn-view"><i class="fas fa-eye"></i></a>
                            <?php if ($t['status_tagihan'] === 'belum_bayar'): ?>
                            <button type="button" class="btn-action btn-delete btn-delete-single" data-id="<?= $t['id_tagihan'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>

<!-- Form untuk delete single (hidden) -->
<form id="formDeleteSingle" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
$(document).ready(function() {
    var table = $('#tableTagihan').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" },
        "pageLength": 10,
        "dom": 'rtip', 
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] }
        ]
    });

    // Custom search
    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Fungsi update counter dan button state
    function updateSelection() {
        var checkedCount = $('.checkbox-item:checked').length;
        $('#selectedCount').text(checkedCount);
        
        if (checkedCount > 0) {
            $('#btnDeleteSelected').addClass('active');
        } else {
            $('#btnDeleteSelected').removeClass('active');
        }
    }

    // Select All
    $('#selectAll').on('change', function() {
        var isChecked = $(this).prop('checked');
        
        // Only check visible checkboxes on current page
        table.$('.checkbox-item', {"page": "current"}).each(function() {
            $(this).prop('checked', isChecked);
        });
        
        updateSelection();
    });

    // Individual checkbox
    $(document).on('change', '.checkbox-item', function() {
        // Update select all state
        var totalCheckboxes = table.$('.checkbox-item', {"page": "current"}).length;
        var checkedCheckboxes = table.$('.checkbox-item:checked', {"page": "current"}).length;
        
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        
        updateSelection();
    });

    // Reset select all when changing page
    table.on('page.dt', function() {
        $('#selectAll').prop('checked', false);
    });

    // Bulk Delete
    $('#btnDeleteSelected').on('click', function() {
        if (!$(this).hasClass('active')) return;
        
        var checkedCount = $('.checkbox-item:checked').length;
        
        if (confirm('Apakah Anda yakin ingin menghapus ' + checkedCount + ' tagihan yang dipilih?')) {
            $('#formBulkDelete').submit();
        }
    });

    // Single Delete
    $(document).on('click', '.btn-delete-single', function() {
        var id = $(this).data('id');
        
        if (confirm('Apakah Anda yakin ingin menghapus tagihan ini?')) {
            var form = $('#formDeleteSingle');
            form.attr('action', '<?= base_url('admin/tagihan/delete/') ?>' + id);
            form.submit();
        }
    });
});
</script>

<?= $this->include('admin/layouts/footer') ?>