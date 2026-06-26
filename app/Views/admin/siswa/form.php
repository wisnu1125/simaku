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
    max-width: 850px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Sections */
.form-section { margin-bottom: 35px; }
.form-section-title { 
    font-size: 15px; 
    font-weight: 700; 
    color: var(--primary); 
    margin-bottom: 20px; 
    padding-bottom: 10px; 
    border-bottom: 2px solid var(--primary-light);
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Grid System */
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
.form-row.single { grid-template-columns: 1fr; }

/* Form Controls */
.form-group { margin-bottom: 15px; }
.form-group label { 
    display: block; 
    margin-bottom: 8px; 
    font-size: 12px; 
    font-weight: 700; 
    color: #475569;
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
    transition: all 0.2s;
    color: var(--text-main);
}

.form-control:focus { 
    outline: none; 
    border-color: var(--primary); 
    background: white;
    box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
}

.form-control.is-invalid { border-color: #ef4444; background: #fffafb; }
.invalid-feedback { color: #ef4444; font-size: 12px; font-weight: 500; margin-top: 6px; }

/* Radio Buttons Modern */
.radio-group { display: flex; gap: 24px; margin-top: 10px; }
.radio-item { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.radio-item input[type="radio"] { 
    width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;
}
.radio-item label { font-size: 14px; font-weight: 500; color: var(--text-main); text-transform: none; letter-spacing: normal; }

/* Info & Alerts */
.alert-info { 
    background: var(--primary-bg); 
    border: 1px solid var(--primary-light); 
    color: #0e7490; 
    padding: 16px; 
    border-radius: 12px; 
    margin-bottom: 24px; 
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Actions & Buttons */
.form-actions { 
    display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); 
}
.btn { 
    padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; 
    display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }

.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: #f1f5f9; color: #1e293b; }

.btn-success { background: #10b981; color: white; }
.btn-success:hover { background: #059669; }

.form-hint { font-size: 12px; color: var(--secondary); margin-top: 6px; font-style: italic; }

@media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($siswa) ? 'Perbarui' : 'Tambah' ?> Siswa</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/siswa') ?>">Data Siswa</a> &nbsp;•&nbsp; 
        <span><?= isset($siswa) ? 'Sunting Profil' : 'Pendaftaran Baru' ?></span>
    </div>
</div>

<div class="card-form">
    <?php if (!isset($siswa)): ?>
    <div class="alert-info">
        <i class="fas fa-info-circle fa-lg"></i>
        <span>Virtual Account (VA) akan di-generate otomatis berbasis NIS jika dikosongkan.</span>
    </div>
    <?php endif; ?>
    
    <form action="<?= isset($siswa) ? base_url('admin/siswa/update/' . $siswa['id_siswa']) : base_url('admin/siswa/store') ?>" method="POST">
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-user-circle"></i> Identitas Pribadi Siswa
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nis" class="required">Nomor Induk Siswa (NIS)</label>
                    <input type="text" class="form-control <?= isset($errors['nis']) ? 'is-invalid' : '' ?>" 
                        id="nis" name="nis" placeholder="Contoh: 2024001" 
                        value="<?= old('nis', $siswa['nis'] ?? '') ?>" required>
                    <?php if (isset($errors['nis'])): ?>
                        <div class="invalid-feedback"><?= $errors['nis'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="nisn">NISN</label>
                    <input type="text" class="form-control <?= isset($errors['nisn']) ? 'is-invalid' : '' ?>" 
                        id="nisn" name="nisn" placeholder="Nomor Induk Siswa Nasional" 
                        value="<?= old('nisn', $siswa['nisn'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row single">
                <div class="form-group">
                    <label for="nama_lengkap" class="required">Nama Lengkap Siswa</label>
                    <input type="text" class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" 
                        id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap sesuai ijazah" 
                        value="<?= old('nama_lengkap', $siswa['nama_lengkap'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal_lahir" class="required">Tanggal Lahir</label>
                    <input type="date" class="form-control <?= isset($errors['tanggal_lahir']) ? 'is-invalid' : '' ?>" 
                        id="tanggal_lahir" name="tanggal_lahir" 
                        value="<?= old('tanggal_lahir', $siswa['tanggal_lahir'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="required">Jenis Kelamin</label>
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="jenis_kelamin" value="L" 
                                <?= (old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'L') ? 'checked' : '' ?> required>
                            <span>Laki-laki</span>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="jenis_kelamin" value="P" 
                                <?= (old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'P') ? 'checked' : '' ?> required>
                            <span>Perempuan</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-row single">
                <div class="form-group">
                    <label for="alamat">Alamat Tinggal</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                        placeholder="Alamat lengkap domisili saat ini"><?= old('alamat', $siswa['alamat'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-users"></i> Kontak Orang Tua / Wali
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nama_wali">Nama Lengkap Wali</label>
                    <input type="text" class="form-control" id="nama_wali" name="nama_wali" 
                        placeholder="Nama Ayah / Ibu / Wali" value="<?= old('nama_wali', $siswa['nama_wali'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="telp_wali">No. Telepon Aktif</label>
                    <input type="text" class="form-control" id="telp_wali" name="telp_wali" 
                        placeholder="Contoh: 081234567890" value="<?= old('telp_wali', $siswa['telp_wali'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-school"></i> Penempatan Kelas & Virtual Account
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="id_kelas">Unit Kelas</label>
                    <select class="form-control" id="id_kelas" name="id_kelas">
                        <option value="">-- Belum ada kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= (old('id_kelas', $siswa['id_kelas'] ?? '') == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (isset($siswa)): ?>
                <div class="form-group">
                    <label for="status_siswa" class="required">Status Akademik</label>
                    <select class="form-control" id="status_siswa" name="status_siswa" required>
                        <option value="aktif" <?= (old('status_siswa', $siswa['status_siswa'] ?? '') === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= (old('status_siswa', $siswa['status_siswa'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                        <option value="lulus" <?= (old('status_siswa', $siswa['status_siswa'] ?? '') === 'lulus') ? 'selected' : '' ?>>Lulus</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-row single">
                <div class="form-group">
                    <label for="virtual_account">Virtual Account Pembayaran</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" class="form-control <?= isset($errors['virtual_account']) ? 'is-invalid' : '' ?>" 
                            id="virtual_account" name="virtual_account" 
                            placeholder="Klik generate atau isi manual" 
                            value="<?= old('virtual_account', $siswa['virtual_account'] ?? '') ?>">
                        <?php if (!isset($siswa)): ?>
                        <button type="button" class="btn btn-success" onclick="generateVA()">
                            <i class="fas fa-magic"></i> Generate
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="form-hint">Format otomatis: 88 + 10 digit NIS</div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> <?= isset($siswa) ? 'Simpan Perubahan' : 'Daftarkan Siswa' ?>
            </button>
            <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function generateVA() {
    const nis = document.getElementById('nis').value;
    if (!nis) {
        alert('Tulis NIS terlebih dahulu sebelum meng-generate VA!');
        document.getElementById('nis').focus();
        return;
    }
    const paddedNIS = nis.padStart(10, '0');
    const va = '88' + paddedNIS;
    document.getElementById('virtual_account').value = va;
}
</script>

<?= $this->include('admin/layouts/footer') ?>