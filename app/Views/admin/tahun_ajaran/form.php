<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL FORM THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-light: #cffafe;
    --primary-bg: #ecfeff;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
    --radius: 14px;
}

body { 
    background: #f8fafc; 
    font-family: 'Inter', sans-serif; 
    color: var(--text-main);
}

/* Page Header */
.page-header { margin-bottom: 28px; }
.page-title { 
    font-size: 26px; 
    font-weight: 800; 
    color: #0f172a; 
    letter-spacing: -0.5px;
    margin-bottom: 8px;
}

/* Breadcrumb */
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; transition: color 0.2s; }
.breadcrumb a:hover { color: var(--primary-hover); }

/* Form Card */
.card-form { 
    background: white; 
    border-radius: var(--radius); 
    padding: 35px; 
    border: 1px solid var(--border); 
    max-width: 650px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Groups */
.form-group { margin-bottom: 24px; }
.form-group label { 
    display: block; 
    margin-bottom: 10px; 
    font-size: 13px; 
    font-weight: 700; 
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-group label.required::after { content: " *"; color: #ef4444; }

.form-control { 
    width: 100%; 
    padding: 12px 16px; 
    border: 1.5px solid var(--border); 
    border-radius: 10px; 
    font-size: 14px; 
    background: #f8fafc;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    color: var(--text-main);
}

.form-control:focus { 
    outline: none; 
    border-color: var(--primary); 
    background: white;
    box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
}

.form-control.is-invalid { border-color: #ef4444; background: #fffafb; }
.invalid-feedback { color: #ef4444; font-size: 12px; font-weight: 500; margin-top: 6px; display: flex; align-items: center; gap: 4px; }

/* Checkbox Modern */
.form-check { 
    display: flex; 
    align-items: flex-start; 
    gap: 12px; 
    margin-top: 20px;
    padding: 15px;
    background: var(--primary-bg);
    border-radius: 10px;
    border: 1px solid var(--primary-light);
}
.form-check input[type="checkbox"] { 
    width: 20px; height: 20px; cursor: pointer; 
    accent-color: var(--primary);
    margin-top: 2px;
}
.form-check label { font-size: 14px; font-weight: 600; color: #0e7490; cursor: pointer; margin: 0; }

/* Info Boxes */
.status-info {
    display: flex; align-items: center; gap: 12px;
    padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500;
}
.status-info.aktif { background: #ecfeff; border: 1px solid #0891b2; color: #0e7490; }
.status-info.selesai { background: #fffbeb; border: 1px solid #f59e0b; color: #92400e; }

/* Buttons */
.form-actions { display: flex; gap: 15px; margin-top: 35px; border-top: 1px solid var(--border); padding-top: 25px; }
.btn { 
    padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; 
    display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.4); }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }

.form-hint { font-size: 12px; color: var(--secondary); margin-top: 8px; font-style: italic; }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($tahun_ajaran) ? 'Edit' : 'Tambah' ?> Tahun Ajaran</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/tahun-ajaran') ?>">Tahun Ajaran</a> &nbsp;•&nbsp; 
        <span><?= isset($tahun_ajaran) ? 'Perbarui Data' : 'Data Baru' ?></span>
    </div>
</div>

<div class="card-form">
    <?php if (isset($tahun_ajaran) && $tahun_ajaran['status'] === 'aktif'): ?>
    <div class="status-info aktif">
        <i class="fas fa-certificate fa-lg"></i> 
        <span>Sistem mendeteksi tahun ajaran ini sedang dalam status <strong>AKTIF</strong>.</span>
    </div>
    <?php elseif (isset($tahun_ajaran) && $tahun_ajaran['status'] === 'selesai'): ?>
    <div class="status-info selesai">
        <i class="fas fa-history fa-lg"></i> 
        <span>Tahun ajaran ini tercatat sudah <strong>SELESAI</strong>.</span>
    </div>
    <?php endif; ?>
    
    <form action="<?= isset($tahun_ajaran) ? base_url('admin/tahun-ajaran/update/' . $tahun_ajaran['id_tahun_ajaran']) : base_url('admin/tahun-ajaran/store') ?>" method="POST">
        
        <div class="form-group">
            <label for="nama_tahun_ajaran" class="required">Identitas Tahun Ajaran</label>
            <input 
                type="text" 
                class="form-control <?= isset($errors['nama_tahun_ajaran']) ? 'is-invalid' : '' ?>" 
                id="nama_tahun_ajaran" 
                name="nama_tahun_ajaran" 
                placeholder="Misal: 2024/2025" 
                value="<?= old('nama_tahun_ajaran', $tahun_ajaran['nama_tahun_ajaran'] ?? '') ?>"
                required
            >
            <?php if (isset($errors['nama_tahun_ajaran'])): ?>
                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['nama_tahun_ajaran'] ?></div>
            <?php endif; ?>
            <div class="form-hint">Gunakan format YYYY/YYYY (contoh: 2025/2026)</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="tanggal_mulai" class="required">Tanggal Mulai</label>
                <input 
                    type="date" 
                    class="form-control <?= isset($errors['tanggal_mulai']) ? 'is-invalid' : '' ?>" 
                    id="tanggal_mulai" 
                    name="tanggal_mulai" 
                    value="<?= old('tanggal_mulai', $tahun_ajaran['tanggal_mulai'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['tanggal_mulai'])): ?>
                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['tanggal_mulai'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="tanggal_selesai" class="required">Tanggal Selesai</label>
                <input 
                    type="date" 
                    class="form-control <?= isset($errors['tanggal_selesai']) ? 'is-invalid' : '' ?>" 
                    id="tanggal_selesai" 
                    name="tanggal_selesai" 
                    value="<?= old('tanggal_selesai', $tahun_ajaran['tanggal_selesai'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['tanggal_selesai'])): ?>
                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['tanggal_selesai'] ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!isset($tahun_ajaran)): ?>
        <div class="form-group">
            <div class="form-check">
                <input 
                    type="checkbox" 
                    id="set_aktif" 
                    name="set_aktif" 
                    value="1"
                    checked
                >
                <label for="set_aktif">
                    Tetapkan sebagai Tahun Ajaran Aktif sekarang
                </label>
            </div>
            <div class="form-hint" style="color: #0891b2; margin-left: 32px;">Otomatis menonaktifkan tahun ajaran sebelumnya yang sedang aktif.</div>
        </div>
        <?php else: ?>
            <?php if ($tahun_ajaran['status'] !== 'aktif'): ?>
            <div class="form-group">
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        id="set_aktif" 
                        name="set_aktif" 
                        value="1"
                    >
                    <label for="set_aktif">
                        Aktifkan kembali tahun ajaran ini
                    </label>
                </div>
                <div class="form-hint" style="color: #0891b2; margin-left: 32px;">Akan memindahkan status tahun ajaran aktif saat ini ke status Selesai.</div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> <?= isset($tahun_ajaran) ? 'Simpan Perubahan' : 'Proses Data' ?>
            </button>
            <a href="<?= base_url('admin/tahun-ajaran') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>