<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL PROMOTION THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-light: #cffafe;
    --primary-bg: #ecfeff;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
    --radius: 16px;
}

body { background: #f8fafc; font-family: 'Inter', sans-serif; color: var(--text-main); }

/* Page Header */
.page-header { margin-bottom: 28px; }
.page-title { 
    font-size: 26px; 
    font-weight: 800; 
    color: #0f172a; 
    letter-spacing: -0.5px; 
    margin-bottom: 8px; 
}
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Card Main */
.card {
    background: #ffffff;
    border-radius: var(--radius);
    padding: 32px;
    border: 1px solid var(--border);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    max-width: 900px;
    margin: 0 auto;
}

/* Warning & Info */
.alert-warning {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    color: #92400e;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kelas-info {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
    color: white;
    padding: 24px;
    border-radius: 14px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(8, 145, 178, 0.2);
}

.kelas-info h3 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 8px; }
.kelas-info .kelas-name { font-size: 22px; font-weight: 800; }

/* Form Elements */
.form-group { margin-bottom: 24px; }
.form-group label { display: block; margin-bottom: 8px; color: #334155; font-weight: 700; font-size: 14px; }
.form-group label.required::after { content: " *"; color: #ef4444; }

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    background: #f8fafc;
    transition: all 0.2s;
}
.form-control:focus { outline: none; border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1); }

/* Student List */
.siswa-list {
    border: 1.5px solid var(--border);
    border-radius: 12px;
    max-height: 450px;
    overflow-y: auto;
    background: white;
}

.select-all-box {
    background: #f8fafc;
    padding: 14px 20px;
    border-bottom: 2px solid var(--border);
    display: flex;
    align-items: center;
    gap: 15px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.select-all-box label { font-weight: 800; color: var(--primary); cursor: pointer; margin: 0; font-size: 14px; }

.siswa-item {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.2s;
}

.siswa-item:hover { background: var(--primary-bg); }
.siswa-item:last-child { border-bottom: none; }

/* Checkbox Customization */
input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: var(--primary);
    cursor: pointer;
}

.siswa-info { flex: 1; cursor: pointer; }
.siswa-name { font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 2px; }
.siswa-detail { font-size: 12px; color: var(--secondary); font-weight: 500; }

/* Buttons */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid var(--border);
}

.btn {
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-primary { background: var(--primary); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.4); }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }

.empty-state { text-align: center; padding: 60px 20px; color: var(--secondary); }
</style>

<div class="page-header">
    <h1 class="page-title">Proses Kenaikan Kelas</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/kenaikan-kelas') ?>">Kenaikan Kelas</a> &nbsp;•&nbsp; 
        <span>Form Pemindahan</span>
    </div>
</div>

<div class="card">
    <div class="alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Perhatian:</strong> Pastikan kelas tujuan sudah tersedia. Proses ini akan memindahkan data akademik siswa ke tahun ajaran berikutnya.
    </div>
    
    <div class="kelas-info">
        <h3><i class="fas fa-sign-out-alt"></i> Sumber Data (Kelas Asal)</h3>
        <div class="kelas-name"><?= esc($kelas['nama_kelas']) ?></div>
        <p style="margin-top: 5px; opacity: 0.9; font-weight: 500;">
            <i class="fas fa-calendar-alt"></i> Periode: <?= esc($kelas['nama_tahun_ajaran']) ?> &nbsp;•&nbsp; 
            <i class="fas fa-users"></i> Populasi: <?= count($siswa) ?> Siswa
        </p>
    </div>
    
    <?php if (empty($siswa)): ?>
        <div class="empty-state">
            <div style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;">👥</div>
            <h3>Kelas Kosong</h3>
            <p>Tidak ditemukan data siswa aktif di dalam kelas ini.</p>
        </div>
    <?php else: ?>
        <form action="<?= base_url('admin/kenaikan-kelas/proses') ?>" method="POST" id="formKenaikanKelas">
            <input type="hidden" name="id_kelas_asal" value="<?= $kelas['id_kelas'] ?>">
            
            <div class="form-group">
                <label for="id_kelas_tujuan" class="required">Pilih Kelas Tujuan</label>
                <select class="form-control" id="id_kelas_tujuan" name="id_kelas_tujuan" required>
                    <option value="">-- Cari Kelas Tujuan (Tahun Ajaran Baru) --</option>
                    <?php foreach ($kelas_tujuan as $kt): ?>
                        <?php if ($kt['id_kelas'] != $kelas['id_kelas']): ?>
                        <option value="<?= $kt['id_kelas'] ?>">
                            <?= esc($kt['nama_kelas']) ?> — TA: <?= esc($kt['nama_tahun_ajaran']) ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="required">Daftar Siswa yang Dipromosikan</label>
                <div class="siswa-list">
                    <div class="select-all-box">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        <label for="selectAll">Pilih Seluruh Siswa</label>
                    </div>
                    
                    <?php foreach ($siswa as $s): ?>
                    <div class="siswa-item">
                        <input 
                            type="checkbox" 
                            name="siswa_ids[]" 
                            value="<?= $s['id_siswa'] ?>"
                            class="siswa-checkbox"
                            id="siswa_<?= $s['id_siswa'] ?>"
                        >
                        <label for="siswa_<?= $s['id_siswa'] ?>" class="siswa-info">
                            <div class="siswa-name"><?= esc($s['nama_lengkap']) ?></div>
                            <div class="siswa-detail">
                                NIS: <?= esc($s['nis']) ?> &nbsp;•&nbsp; 
                                <span style="text-transform: uppercase;"><?= esc($s['jenis_kelamin']) ?></span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" onclick="return confirmKenaikan()">
                    <i class="fas fa-level-up-alt"></i> Jalankan Kenaikan Kelas
                </button>
                <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

document.querySelectorAll('.siswa-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
        const selectAll = document.getElementById('selectAll');
        
        selectAll.checked = checkedCount === checkboxes.length;
    });
});

function confirmKenaikan() {
    const kelasTujuan = document.getElementById('id_kelas_tujuan');
    const selectedSiswa = document.querySelectorAll('.siswa-checkbox:checked');
    
    if (!kelasTujuan.value) {
        alert('Mohon pilih kelas tujuan terlebih dahulu.');
        return false;
    }
    
    if (selectedSiswa.length === 0) {
        alert('Mohon pilih minimal satu siswa yang akan dinaikkan kelas.');
        return false;
    }
    
    const kelasTujuanText = kelasTujuan.options[kelasTujuan.selectedIndex].text;
    
    return confirm(`KONFIRMASI: Anda akan memindahkan ${selectedSiswa.length} siswa ke ${kelasTujuanText}. Lanjutkan?`);
}
</script>

<?= $this->include('admin/layouts/footer') ?>