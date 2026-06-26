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
    transition: all 0.2s ease;
    background: var(--primary); color: white; border: none; cursor: pointer;
}
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }

/* Card Style */
.card-table { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }

/* Custom Search Box */
.search-input-client { 
    width: 300px; padding: 10px 14px; border: 1px solid var(--border); 
    border-radius: 8px; font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.search-input-client:focus { outline: none; border-color: var(--primary); background: white; width: 320px; }

/* Table Typography */
table.dataTable { border-collapse: collapse !important; border: none !important; }
table.dataTable thead th { 
    background: #f1f5f9; color: var(--secondary); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; padding: 12px !important; border-bottom: 1px solid var(--border) !important;
}
table.dataTable tbody td { padding: 14px 12px !important; border-bottom: 1px solid #f8fafc !important; vertical-align: middle; }
table.dataTable tbody tr:hover { background-color: var(--primary-bg) !important; }

.text-main-bold { display: block; font-weight: 700; color: #0f172a; font-size: 14px; margin-bottom: 2px; }
.text-sub-label { display: block; font-size: 11px; font-weight: 500; color: var(--secondary); }

/* Badges */
.badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #15803d; }
.badge-danger { background: #fee2e2; color: #b91c1c; }
.badge-info { background: #e0f2fe; color: #0369a1; }
.badge-purple { background: #f3e8ff; color: #7e22ce; }

/* Action Buttons */
.btn-action { width: 32px; height: 32px; border-radius: 6px; color: white; border: none; display: inline-flex; align-items: center; justify-content: center; transition: opacity 0.2s; text-decoration: none; }
.btn-warning { background: #f59e0b; }
.btn-danger-action { background: #ef4444; }
.btn-action:hover { opacity: 0.8; }

.dataTables_wrapper .dataTables_filter { display: none; }
</style>

<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <a href="<?= base_url('admin/users/create') ?>" class="btn-primary">
        <i class="fas fa-user-plus"></i> Tambah User
    </a>
</div>

<div class="card-table">
    <?php if (empty($users)): ?>
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;">👥</div>
            <h3 style="font-size: 16px; color: var(--secondary);">Belum Ada User</h3>
            <p style="font-size: 14px; color: #94a3b8;">Silakan tambah user untuk mengelola sistem</p>
        </div>
    <?php else: ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-main);">Daftar Pengguna</h3>
            <input type="text" id="customSearch" class="search-input-client" placeholder="Cari username, nama, atau email...">
        </div>

        <div class="table-responsive">
            <table id="tableUsers" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Username</th>
                        <th>Informasi Profil</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="10%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $u): ?>
                    <tr>
                        <td class="text-center" style="color: #cbd5e1; font-weight: 600; font-size: 12px;"><?= $index + 1 ?></td>
                        <td>
                            <span class="text-main-bold" style="font-family: 'SF Mono', monospace; color: var(--primary);"><?= esc($u['username']) ?></span>
                        </td>
                        <td>
                            <span class="text-main-bold"><?= esc($u['nama_lengkap']) ?></span>
                            <span class="text-sub-label"><i class="far fa-envelope"></i> <?= esc($u['email']) ?></span>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'super_admin'): ?>
                                <span class="badge badge-purple"><i class="fas fa-crown"></i> Super Admin</span>
                            <?php else: ?>
                                <span class="badge badge-info"><i class="fas fa-user-shield"></i> Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['status'] === 'aktif'): ?>
                                <span class="badge badge-success">● Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">● Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="<?= base_url('admin/users/edit/' . $u['id_user']) ?>" class="btn-action btn-warning" title="Edit">
                                    <i class="fas fa-edit" style="font-size: 12px;"></i>
                                </a>
                                <?php if ($u['role'] !== 'super_admin' && $u['id_user'] != session()->get('id_user')): ?>
                                <form action="<?= base_url('admin/users/delete/' . $u['id_user']) ?>" method="POST" style="display: inline;">
                                    <button type="submit" class="btn-action btn-danger-action" title="Hapus" onclick="return confirm('Hapus user ini?')">
                                        <i class="fas fa-trash" style="font-size: 12px;"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        var table = $('#tableUsers').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "dom": 'rtip', 
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": 5 }
            ]
        });

        // Live search
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

<?= $this->include('admin/layouts/footer') ?>