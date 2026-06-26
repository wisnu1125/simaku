<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== AUDIT LOG MODERN STYLE ==================== */
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

.page-header { margin-bottom: 24px; }
.page-title { font-size: 24px; font-weight: 700; color: #0f172a; }

/* Filter Card */
.filter-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border); margin-bottom: 24px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: flex-end; }
.filter-group label { display: block; margin-bottom: 6px; font-size: 11px; font-weight: 700; color: var(--secondary); text-transform: uppercase; }
.filter-input { 
    width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; 
    font-size: 13px; background: #f8fafc; transition: all 0.2s;
}
.filter-input:focus { outline: none; border-color: var(--primary); background: white; }

/* Table Card */
.card-table { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }

table { width: 100%; border-collapse: collapse; }
table thead th { 
    background: #f1f5f9; color: var(--secondary); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; padding: 12px !important; text-align: left;
}
table tbody td { padding: 14px 12px !important; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
table tbody tr:hover { background-color: #f8fafc; }

/* Text Estetik */
.text-main-bold { display: block; font-weight: 700; color: #0f172a; font-size: 13px; margin-bottom: 2px; }
.text-sub-label { display: block; font-size: 11px; font-weight: 500; color: #64748b; }

/* Badge Actions */
.badge { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; letter-spacing: 0.05em; }
.badge-create { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-update { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-delete { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

/* Pagination Styling */
.pagination-container { margin-top: 24px; display: flex; justify-content: center; }
.pagination { display: flex; list-style: none; gap: 5px; }
.pagination li a, .pagination li span {
    padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px;
    color: var(--secondary); text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s;
}
.pagination li.active span { background: var(--primary); color: white; border-color: var(--primary); }
.pagination li a:hover { background: var(--primary-bg); color: var(--primary); }

.ip-box { font-family: 'SF Mono', monospace; font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569; }

.btn-detail {
    padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;
    text-decoration: none; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
    transition: all 0.2s;
}
.btn-detail:hover { background: var(--primary); color: white; border-color: var(--primary); }
</style>

<div class="page-header">
    <h1 class="page-title">Audit Log System</h1>
</div>

<div class="filter-card">
    <form action="<?= base_url('admin/audit-log') ?>" method="GET">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Mulai</label>
                <input type="date" name="tanggal_mulai" class="filter-input" value="<?= $tanggal_mulai ?>">
            </div>
            <div class="filter-group">
                <label>Selesai</label>
                <input type="date" name="tanggal_selesai" class="filter-input" value="<?= $tanggal_selesai ?>">
            </div>
            <div class="filter-group">
                <label>Modul</label>
                <select name="modul" class="filter-input">
                    <option value="">Semua Modul</option>
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= esc($m['modul']) ?>" <?= ($modul == $m['modul']) ? 'selected' : '' ?>>
                            <?= esc(ucfirst($m['modul'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Aksi</label>
                <select name="aksi" class="filter-input">
                    <option value="">Semua Aksi</option>
                    <?php foreach ($actions as $a): ?>
                        <option value="<?= esc($a['aksi']) ?>" <?= ($aksi == $a['aksi']) ? 'selected' : '' ?>>
                            <?= esc(ucfirst($a['aksi'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Cari</label>
                <input type="text" name="keyword" class="filter-input" placeholder="User..." value="<?= esc($keyword ?? '') ?>">
            </div>
            <div class="filter-group" style="display: flex; gap: 8px;">
                <button type="submit" class="btn-detail" style="background: var(--primary); color: white; border: none; padding: 10px; flex: 1; font-size: 13px;">
                    Filter
                </button>
                <a href="<?= base_url('admin/audit-log') ?>" class="btn-detail" style="padding: 10px; flex: 1; text-align: center; font-size: 13px;">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-table">
    <?php if (empty($logs)): ?>
        <div class="empty-state">
            <div style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;">📋</div>
            <h3 style="font-size: 16px; color: #475569;">Tidak Ada Rekam Aktivitas</h3>
            <p style="font-size: 13px;">Silakan sesuaikan filter pencarian Anda</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                        <th>Keterangan</th>
                        <th>IP & Device</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <span class="text-main-bold"><?= date('d M Y', strtotime($log['created_at'])) ?></span>
                            <span class="text-sub-label"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                        </td>
                        <td>
                            <span class="text-main-bold"><?= esc($log['nama_lengkap']) ?></span>
                            <span class="text-sub-label">@<?= esc($log['username']) ?></span>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= strtoupper(esc($log['modul'])) ?></span>
                        </td>
                        <td>
                            <?php 
                                $aksiClass = 'badge-info';
                                if($log['aksi'] == 'create') $aksiClass = 'badge-create';
                                if($log['aksi'] == 'update') $aksiClass = 'badge-update';
                                if($log['aksi'] == 'delete') $aksiClass = 'badge-delete';
                            ?>
                            <span class="badge <?= $aksiClass ?>"><?= strtoupper($log['aksi']) ?></span>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: #475569;">
                                <?= esc(strlen($log['keterangan']) > 45 ? substr($log['keterangan'], 0, 45) . '...' : $log['keterangan']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="ip-box"><?= esc($log['ip_address']) ?></span>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/audit-log/detail/' . $log['id_log']) ?>" class="btn-detail">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->include('admin/layouts/footer') ?>