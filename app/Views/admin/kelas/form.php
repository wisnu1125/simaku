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
.breadcrumb a:hover { color: var(--primary-hover); text-decoration: underline; }

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

/* Buttons */
.form-actions { 
    display: flex; 
    gap: 15px; 
    margin-top: 35px; 
    border-top: 1px solid var(--border); 
    padding-top: 25px; 
}
.btn { 
    padding: 12px 28px; 
    border-radius: 10px; 
    font-size: 14px; 
    font-weight: 700; 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    transition: all 0.2s; 
    border: none; 
    cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.4); }

.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }

.form-hint { font-size: 12px; color: var(--secondary); margin-top: 8px; font-style: italic; }

/* Status Labels in Select */
.status-pill { font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 10px; }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($kelas) ? 'Perbarui' : 'Daftarkan' ?> Unit Kelas</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/kelas') ?>">Manajemen Kelas</a> &nbsp;•&nbsp; 
        <span><?= isset($kelas) ? 'Sunting Data' : 'Tambah Baru' ?></span>
    </div>
</div>

<div class="card-form">
    <form action="<?= isset($kelas) ? base_url('admin/kelas/update/' . $kelas['id_kelas']) : base_url('admin/kelas/store') ?>" method="POST">
        
        <div class="form-group">
            <label for="nama_kelas" class="required">Label / Nama Kelas</label>
            <input 
                type="text" 
                class="form-control <?= isset($errors['nama_kelas']) ? 'is-invalid' : '' ?>" 
                id="nama_kelas" 
                name="nama_kelas" 
                placeholder="Contoh: 7 Al-Farabi atau 9A" 
                value="<?= old('nama_kelas', $kelas['nama_kelas'] ?? '') ?>"
                required
            >
            <?php if (isset($errors['nama_kelas'])): ?>
                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['nama_kelas'] ?></div>
            <?php endif; ?>
            <div class="form-hint">Gunakan penamaan yang konsisten untuk mempermudah identifikasi.</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="tingkat" class="required">Tingkat Pendidikan</label>
                <select 
                    class="form-control <?= isset($errors['tingkat']) ? 'is-invalid' : '' ?>" 
                    id="tingkat" 
                    name="tingkat"
                    required
                >
                    <option value="">-- Pilih Tingkat --</option>
                    <?php for ($i = 7; $i <= 9; $i++): ?>
                        <option value="<?= $i ?>" <?= (old('tingkat', $kelas['tingkat'] ?? '') == $i) ? 'selected' : '' ?>>
                            Kelas <?= $i ?> (SMP)
                        </option>
                    <?php endfor; ?>
                </select>
                <?php if (isset($errors['tingkat'])): ?>
                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['tingkat'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="id_tahun_ajaran" class="required">Tahun Ajaran Aktif</label>
                <select 
                    class="form-control <?= isset($errors['id_tahun_ajaran']) ? 'is-invalid' : '' ?>" 
                    id="id_tahun_ajaran" 
                    name="id_tahun_ajaran"
                    required
                >
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta['id_tahun_ajaran'] ?>" 
                            <?= (old('id_tahun_ajaran', $kelas['id_tahun_ajaran'] ?? '') == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?> 
                            <?= $ta['status'] === 'aktif' ? '• AKTIF' : '• CLOSED' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['id_tahun_ajaran'])): ?>
                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?= $errors['id_tahun_ajaran'] ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= isset($kelas) ? 'Simpan Perubahan' : 'Daftarkan Kelas' ?>
            </button>
            <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>