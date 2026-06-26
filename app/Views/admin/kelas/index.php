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

/* Page Header */
.page-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px;
}
.page-title {
    font-size: 24px; font-weight: 800; color: var(--text-main);
    letter-spacing: -0.5px;
}

/* Buttons Standard */
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

/* Action Buttons (Icon Only) */
.btn-action {
    width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; transition: 0.2s; color: white; margin-right: 4px; border: none; cursor: pointer;
}
.btn-edit { background: #f59e0b; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3); } /* Kuning */
.btn-delete { background: #ef4444; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3); } /* Merah */
.btn-action:hover { transform: translateY(-2px); filter: brightness(110%); }

/* Card Table */
.card-table {
    background: white; border-radius: 16px; border: 1px solid var(--border);
    overflow: hidden; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

/* Search Bar */
.search-bar {
    margin-bottom: 20px; display: flex; justify-content: flex-end;
}
.search-input {
    width: 300px; padding: 10px 14px; border: 1px solid var(--border);
    border-radius: 10px; font-size: 14px; transition: 0.2s; background: #f8fafc;
}
.search-input:focus {
    outline: none; border-color: var(--primary); background: white;
    box-shadow: 0 0 0 3px var(--primary-bg);
}

/* DataTables Customization */
.dataTables_wrapper .dataTables_filter, 
.dataTables_wrapper .dataTables_length { display: none; }

table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-top: 0 !important; }

/* Header */
table.dataTable thead th {
    background: #f1f5f9; color: var(--secondary); font-size: 12px; font-weight: 700;
    text-transform: uppercase; padding: 16px !important; border-bottom: 1px solid var(--border) !important;
}

/* Body */
table.dataTable tbody td {
    padding: 16px !important; border-bottom: 1px solid #f8fafc !important;
    font-size: 14px; color: var(--text-main); vertical-align: middle;
}
table.dataTable tbody tr:hover { background-color: var(--primary-bg) !important; }

/* Pagination Styling */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 20px; display: flex; justify-content: flex-end; gap: 6px; padding-top: 10px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important; padding: 8px 14px !important; border: 1px solid var(--border) !important;
    background: white !important; color: var(--secondary) !important; font-size: 13px; font-weight: 600;
    cursor: pointer !important; user-select: none; display: inline-flex; align-items: center; justify-content: center;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:not(.disabled):hover {
    background: var(--primary-bg) !important; color: var(--primary) !important; border-color: var(--primary-light) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #06b6d4, #0891b2) !important; color: white !important; border-color: var(--primary) !important; cursor: default !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    cursor: not-allowed !important; opacity: 0.5; background: #f1f5f9 !important;
}
.dataTables_wrapper .dataTables_info { margin-top: 24px; font-size: 13px; color: var(--secondary); font-weight: 500; }

/* Badges */
.badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

/* Responsive */
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; gap: 16px; }
    .search-input { width: 100%; }
}
</style>

<div class="page-header">
    <h1 class="page-title">Kelas</h1>
    <a href="<?= base_url('admin/kelas/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kelas
    </a>
</div>

<div class="card-table">
    <div class="search-bar">
        <input type="text" id="customSearch" class="search-input" placeholder="Cari Kelas, Tingkat, atau Tahun Ajaran...">
    </div>

    <table id="tableKelas" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Kelas</th>
                <th>Tingkat</th>
                <th>Tahun Ajaran</th>
                <th>Jumlah Siswa</th>
                <th class="text-center">Status TA</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kelas as $index => $k): ?>
            <tr>
                <td class="text-center" style="color:#64748b; font-weight:600;"><?= $index + 1 ?></td>
                <td>
                    <strong style="color:var(--primary); font-size:15px;"><?= esc($k['nama_kelas']) ?></strong>
                </td>
                <td><?= esc($k['tingkat']) ?></td>
                <td><?= esc($k['nama_tahun_ajaran']) ?></td>
                <td>
                    <span class="badge badge-info">
                        <i class="fas fa-users" style="font-size:10px;"></i> <?= $k['jumlah_siswa'] ?> Siswa
                    </span>
                </td>
                <td class="text-center">
                    <?php if ($k['status_tahun_ajaran'] === 'aktif'): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-danger"><i class="fas fa-lock"></i> Closed</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <a href="<?= base_url('admin/kelas/edit/' . $k['id_kelas']) ?>" class="btn-action btn-edit" title="Edit">
                        <i class="fas fa-pencil" style="font-size:12px;"></i>
                    </a>
                    
                    <form action="<?= base_url('admin/kelas/delete/' . $k['id_kelas']) ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                            <i class="fas fa-trash" style="font-size:12px;"></i>
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
        var table = $('#tableKelas').DataTable({
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
                { "orderable": false, "targets": 6 } // Matikan sort di kolom Aksi (index 6)
            ]
        });

        // Custom Search Box
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

<?= $this->include('admin/layouts/footer') ?>