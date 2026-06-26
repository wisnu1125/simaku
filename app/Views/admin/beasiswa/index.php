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
.dataTables_wrapper .dataTables_filter { display: none; } /* Sembunyikan search default */

table.dataTable thead th { 
    background: #f1f5f9; color: var(--secondary); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; padding: 12px !important; border-bottom: 1px solid var(--border) !important;
}
table.dataTable tbody tr { transition: background 0.15s; }
table.dataTable tbody tr:hover { background-color: var(--primary-bg) !important; }

/* Badges */
.badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #15803d; }
.badge-danger { background: #fee2e2; color: #b91c1c; }

/* Custom Search Box */
.search-input-client { 
    width: 300px; padding: 10px 14px; border: 1px solid var(--border); 
    border-radius: 8px; font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.search-input-client:focus { outline: none; border-color: var(--primary); background: white; width: 320px; }

/* Action Buttons */
.btn-action { width: 32px; height: 32px; border-radius: 6px; color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: opacity 0.2s; text-decoration: none; }
.btn-warning { background: #f59e0b; }
.btn-danger { background: #ef4444; }
.btn-action:hover { opacity: 0.8; }

.beasiswa-value { font-weight: 700; color: var(--primary); font-family: 'SF Mono', monospace; }

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
    <h1 class="page-title">Data Beasiswa</h1>
    <a href="<?= base_url('admin/beasiswa/create') ?>" class="btn-primary">
        <i class="fas fa-plus"></i> Tambah Beasiswa
    </a>
</div>

<div class="filter-card">
    <form method="GET" action="<?= base_url('admin/beasiswa') ?>" id="filterForm">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Tahun Ajaran</label>
                <select name="filter_tahun_ajaran" class="filter-input" onchange="this.form.submit()">
                    <option value="">Semua Tahun Ajaran</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= ($filter_tahun_ajaran == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="margin-left: auto;">
                <a href="<?= base_url('admin/beasiswa') ?>" style="color: var(--secondary); font-size: 13px; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-undo"></i> Reset Filter
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-table">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 700;">Daftar Siswa Penerima</h3>
        <input type="text" id="customSearch" class="search-input-client" placeholder="Cari nama, NIS, atau jenis beasiswa...">
    </div>

    <table id="tableBeasiswa" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Siswa</th>
                <th>Nama Beasiswa</th>
                <th>Jenis Tagihan</th>
                <th style="text-align: right;">Nilai</th>
                <th style="text-align: center;">Status</th>
                <th width="10%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($beasiswa as $index => $b): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <div style="font-weight:700;"><?= esc($b['nama_siswa']) ?></div>
                    <div style="font-size:11px; color:var(--secondary);">NIS: <?= esc($b['nis']) ?></div>
                </td>
                <td>
                    <div style="font-weight:600; font-size:13px;"><?= esc($b['nama_beasiswa']) ?></div>
                    <?php if ($b['keterangan']): ?>
                        <div style="font-size:10px; color:var(--secondary); max-width: 200px;" class="text-truncate">
                            <?= esc($b['keterangan']) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><span style="font-size: 13px;"><?= esc($b['nama_tagihan']) ?></span></td>
                <td style="text-align: right;">
                    <div class="beasiswa-value">
                        <?= $b['tipe_beasiswa'] === 'nominal' ? 'Rp ' . number_format($b['nilai_beasiswa'], 0, ',', '.') : number_format($b['nilai_beasiswa'], 0, ',', '.') . '%' ?>
                    </div>
                </td>
                <td class="text-center">
                    <?php if ($b['status'] === 'aktif'): ?>
                        <span class="badge badge-success">AKTIF</span>
                    <?php else: ?>
                        <span class="badge badge-danger">NONAKTIF</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="<?= base_url('admin/beasiswa/edit/' . $b['id_beasiswa']) ?>" class="btn-action btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?= base_url('admin/beasiswa/delete/' . $b['id_beasiswa']) ?>" method="POST" style="display: inline;">
                            <button type="submit" class="btn-action btn-danger" onclick="return confirm('Hapus beasiswa ini? Tagihan akan dihitung ulang secara otomatis.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        var table = $('#tableBeasiswa').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "dom": 'rtip', 
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": 6 }
            ]
        });

        // Live search logic
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

<?= $this->include('admin/layouts/footer') ?>