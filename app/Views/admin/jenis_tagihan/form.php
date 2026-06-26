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

body { background: #f8fafc; font-family: 'Inter', sans-serif; }

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
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Form Card */
.card-form { 
    background: white; 
    border-radius: var(--radius); 
    padding: 35px; 
    border: 1px solid var(--border); 
    max-width: 750px;
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
    outline: none; border-color: var(--primary); background: white;
    box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
}

/* Radio Card Grid */
.radio-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 10px;
}

.radio-card {
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 20px 15px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    background: #fdfdfd;
    position: relative;
}

.radio-card:hover {
    border-color: var(--primary-light);
    background: var(--primary-bg);
    transform: translateY(-2px);
}

.radio-card input[type="radio"] { display: none; }

.radio-card.active {
    border-color: var(--primary);
    background: var(--primary-bg);
    box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.1);
}

.radio-icon { font-size: 28px; margin-bottom: 10px; display: block; }
.radio-label { font-size: 14px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px; }
.radio-desc { font-size: 11px; color: #64748b; font-weight: 500; line-height: 1.3; }

.radio-card.active .radio-label { color: var(--primary); }
.radio-card.active .radio-desc { color: var(--primary-hover); }

/* Actions */
.form-actions { display: flex; gap: 12px; margin-top: 35px; padding-top: 25px; border-top: 1px solid var(--border); }
.btn { 
    padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; 
    display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }

.form-hint { font-size: 12px; color: var(--secondary); margin-top: 8px; font-style: italic; }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($jenis_tagihan) ? 'Konfigurasi' : 'Definisi' ?> Jenis Tagihan</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/jenis-tagihan') ?>">Master Tagihan</a> &nbsp;•&nbsp; 
        <span><?= isset($jenis_tagihan) ? 'Sunting Data' : 'Baru' ?></span>
    </div>
</div>

<div class="card-form">
    <form action="<?= isset($jenis_tagihan) ? base_url('admin/jenis-tagihan/update/' . $jenis_tagihan['id_jenis_tagihan']) : base_url('admin/jenis-tagihan/store') ?>" method="POST">
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="nama_tagihan" class="required">Nama Tagihan</label>
                <input 
                    type="text" 
                    class="form-control <?= isset($errors['nama_tagihan']) ? 'is-invalid' : '' ?>" 
                    id="nama_tagihan" name="nama_tagihan" 
                    placeholder="Contoh: SPP Bulanan, Uang Pangkal" 
                    value="<?= old('nama_tagihan', $jenis_tagihan['nama_tagihan'] ?? '') ?>" required
                >
                <div class="form-hint">Nama deskriptif untuk kwitansi dan laporan.</div>
            </div>

            <div class="form-group">
                <label for="kode_tagihan" class="required">Kode Tagihan</label>
                <input 
                    type="text" 
                    class="form-control <?= isset($errors['kode_tagihan']) ? 'is-invalid' : '' ?>" 
                    id="kode_tagihan" name="kode_tagihan" 
                    placeholder="Contoh: SPP" 
                    value="<?= old('kode_tagihan', $jenis_tagihan['kode_tagihan'] ?? '') ?>"
                    style="text-transform: uppercase;" required
                >
                <div class="form-hint">Kode singkat (Kapital).</div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="required">Model / Tipe Penagihan</label>
            <div class="radio-grid">
                <label class="radio-card <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'bulanan') ? 'active' : '' ?>" onclick="selectTipe(this)">
                    <input type="radio" name="tipe_tagihan" value="bulanan" <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'bulanan') ? 'checked' : '' ?> required>
                    <div class="radio-content">
                        <span class="radio-icon">📅</span>
                        <span class="radio-label">Bulanan</span>
                        <span class="radio-desc">Ditagih berkala tiap bulan</span>
                    </div>
                </label>
                
                <label class="radio-card <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'tahunan') ? 'active' : '' ?>" onclick="selectTipe(this)">
                    <input type="radio" name="tipe_tagihan" value="tahunan" <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'tahunan') ? 'checked' : '' ?> required>
                    <div class="radio-content">
                        <span class="radio-icon">📆</span>
                        <span class="radio-label">Tahunan</span>
                        <span class="radio-desc">Sekali per tahun ajaran</span>
                    </div>
                </label>
                
                <label class="radio-card <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'sekali') ? 'active' : '' ?>" onclick="selectTipe(this)">
                    <input type="radio" name="tipe_tagihan" value="sekali" <?= (old('tipe_tagihan', $jenis_tagihan['tipe_tagihan'] ?? '') === 'sekali') ? 'checked' : '' ?> required>
                    <div class="radio-content">
                        <span class="radio-icon">⭐</span>
                        <span class="radio-label">Sekali Saja</span>
                        <span class="radio-desc">Ditagih hanya satu kali</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="grup_tagihan">Klasifikasi (Group)</label>
                <select class="form-control" id="grup_tagihan" name="grup_tagihan">
                    <option value="">- Tanpa Group -</option>
                    <option value="DAFTAR ULANG" <?= old('grup_tagihan', $jenis_tagihan['grup_tagihan'] ?? '') === 'DAFTAR ULANG' ? 'selected' : '' ?>>DAFTAR ULANG</option>
                    <option value="BULANAN" <?= old('grup_tagihan', $jenis_tagihan['grup_tagihan'] ?? '') === 'BULANAN' ? 'selected' : '' ?>>BULANAN</option>
                    <option value="LAINNYA" <?= old('grup_tagihan', $jenis_tagihan['grup_tagihan'] ?? '') === 'LAINNYA' ? 'selected' : '' ?>>LAINNYA</option>
                </select>
                <div class="form-hint">Memudahkan filter di laporan keuangan.</div>
            </div>

            <?php if (isset($jenis_tagihan)): ?>
            <div class="form-group">
                <label for="status" class="required">Status Aktif</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="aktif" <?= (old('status', $jenis_tagihan['status'] ?? '') === 'aktif') ? 'selected' : '' ?>>Aktif Digunakan</option>
                    <option value="nonaktif" <?= (old('status', $jenis_tagihan['status'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktifkan</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="keterangan">Keterangan Tambahan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Informasi pendukung tentang tagihan ini..."><?= old('keterangan', $jenis_tagihan['keterangan'] ?? '') ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-double"></i> <?= isset($jenis_tagihan) ? 'Perbarui Data' : 'Simpan Definisi' ?>
            </button>
            <a href="<?= base_url('admin/jenis-tagihan') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>

<script>
function selectTipe(card) {
    document.querySelectorAll('.radio-card').forEach(function(el) {
        el.classList.remove('active');
    });
    card.classList.add('active');
    card.querySelector('input[type="radio"]').checked = true;
}

document.getElementById('kode_tagihan').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});
</script>

<?= $this->include('admin/layouts/footer') ?>