<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== CLEAN PRO-STYLE THEME ==================== */
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

/* Info Box */
.info-box {
    background: #eff6ff;
    border: 1px solid #dbeafe;
    color: #1e40af;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Filter Card */
.filter-card { 
    background: white; 
    border-radius: 14px; 
    padding: 24px; 
    border: 1px solid var(--border); 
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 11px;
    font-weight: 700;
    color: var(--secondary);
    text-transform: uppercase;
}

.filter-input { 
    width: 100%; 
    padding: 12px; 
    border: 1.5px solid var(--border); 
    border-radius: 10px; 
    font-size: 14px; 
    background: #f8fafc;
    transition: all 0.2s;
}

.filter-input:focus { outline: none; border-color: var(--primary); background: white; }

/* Kelas Header Card */
.kelas-header {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
    padding: 24px;
    border-radius: 14px;
    margin-bottom: 24px;
    box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.2);
}

.kelas-title { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
.kelas-subtitle { font-size: 13px; opacity: 0.85; font-weight: 500; }

/* Student Table Styles */
.card-table { background: white; border-radius: 14px; border: 1px solid var(--border); padding: 24px; }
table { width: 100%; border-collapse: collapse; }
table thead th { 
    background: #f1f5f9; 
    color: var(--secondary); 
    font-size: 11px; 
    font-weight: 700; 
    text-transform: uppercase; 
    padding: 14px !important;
}
table tbody td { padding: 16px 14px !important; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

.text-main-bold { display: block; font-weight: 700; color: #0f172a; font-size: 14px; }
.text-sub-label { display: block; font-size: 11px; font-weight: 500; color: var(--secondary); }

/* Buttons */
.btn { 
    padding: 12px 24px; 
    border-radius: 10px; 
    font-size: 14px; 
    font-weight: 700; 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}

.btn-primary { background: var(--primary); color: white; border: none; }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
.btn-warning { background: #f59e0b; color: white; border: none; }
.btn-warning:hover { background: #d97706; transform: translateY(-1px); }

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px dashed var(--border);
}

.badge-active {
    background: #dcfce7;
    color: #15803d;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
}
</style>

<div class="page-header">
    <h1 class="page-title">Kenaikan & Kelulusan</h1>
</div>

<div class="info-box">
    <i class="fas fa-info-circle" style="font-size: 18px;"></i>
    <span>Pilih kelas asal untuk memproses pemindahan siswa ke tingkat selanjutnya atau menandai kelulusan.</span>
</div>

<div class="filter-card">
    <form action="<?= base_url('admin/kenaikan-kelas') ?>" method="GET">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Pilih Kelas Sumber</label>
                <select name="id_kelas" class="filter-input" required onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>

<?php if (isset($kelas_detail) && isset($siswa)): ?>
    <div class="kelas-header">
        <div class="kelas-title"><i class="fas fa-door-open"></i> <?= esc($kelas_detail['nama_kelas']) ?></div>
        <div class="kelas-subtitle">
            Tahun Ajaran Aktif: <?= esc($kelas_detail['nama_tahun_ajaran']) ?> &nbsp;•&nbsp; 
            Kapasitas Terisi: <?= count($siswa) ?> Siswa
        </div>
    </div>

    <div class="card-table">
        <?php if (empty($siswa)): ?>
            <div style="text-align: center; padding: 40px; color: var(--secondary);">
                <i class="fas fa-users-slash" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px;"></i>
                <h3 style="font-size: 16px; font-weight: 700;">Kelas Kosong</h3>
                <p style="font-size: 13px;">Tidak ada data siswa aktif di dalam kelas ini.</p>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a;">Daftar Siswa Kelas Asal</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Biodata Siswa</th>
                            <th>L/P</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $index => $s): ?>
                        <tr>
                            <td style="color: #cbd5e1; font-weight: 600;"><?= $index + 1 ?></td>
                            <td>
                                <span class="text-main-bold"><?= esc($s['nama_lengkap']) ?></span>
                                <span class="text-sub-label">NIS: <?= esc($s['nis']) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #475569;"><?= $s['jenis_kelamin'] ?></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-active">AKTIF</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="action-buttons">
                <a href="<?= base_url('admin/kenaikan-kelas/form?id_kelas=' . $id_kelas) ?>" class="btn btn-primary">
                    <i class="fas fa-level-up-alt"></i> Proses Kenaikan Kelas
                </a>
                <a href="<?= base_url('admin/kenaikan-kelas/kelulusan?id_kelas=' . $id_kelas) ?>" class="btn btn-warning">
                    <i class="fas fa-graduation-cap"></i> Proses Kelulusan Siswa
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->include('admin/layouts/footer') ?>