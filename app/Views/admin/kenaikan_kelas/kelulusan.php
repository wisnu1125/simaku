<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL GRADUATION THEME ==================== */
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
    padding: 24px;
    border: 1px solid var(--border);
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Info Box Modern */
.info-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-box-title {
    font-weight: 800;
    color: #92400e;
    font-size: 14px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box-text {
    color: #78350f;
    font-size: 13px;
    line-height: 1.6;
}

/* Kelas Info Header */
.kelas-info {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
    padding: 24px;
    border-radius: 14px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.2);
}

.kelas-info-left h3 { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
.kelas-info-left .tahun { font-size: 14px; opacity: 0.9; font-weight: 500; }
.kelas-info-right { text-align: right; }
.kelas-info-right .label { font-size: 11px; text-transform: uppercase; font-weight: 700; opacity: 0.8; }
.kelas-info-right .value { font-size: 28px; font-weight: 900; }

/* Toolbar */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.toolbar-left { display: flex; gap: 12px; align-items: center; }

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: #f8fafc;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.checkbox-wrapper label { font-weight: 700; color: #475569; font-size: 14px; margin: 0; cursor: pointer; }

.selected-count {
    padding: 10px 16px;
    background: var(--primary-bg);
    border: 1px solid var(--primary-light);
    border-radius: 10px;
    color: var(--primary);
    font-weight: 700;
    font-size: 14px;
    display: none;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

.selected-count.show { display: block; }

/* Buttons */
.btn {
    padding: 12px 24px;
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
.btn-primary:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-2px); }
.btn-primary:disabled { background: #cbd5e1; cursor: not-allowed; }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: #f8fafc; color: #0f172a; }

/* Table Styling */
.table-responsive { border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
table thead tr { background: #f8fafc; border-bottom: 2px solid var(--border); }
table th { padding: 14px; text-align: left; font-size: 11px; font-weight: 800; color: var(--secondary); text-transform: uppercase; }
table td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
table tbody tr:hover { background: #f8fafc; }
table tbody tr.selected { background: var(--primary-bg); }

.checkbox-cell { width: 50px; text-align: center; }
input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; }

.badge { padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
.badge-success { background: #dcfce7; color: #15803d; }

.empty-state { text-align: center; padding: 60px 20px; color: var(--secondary); }
</style>

<div class="page-header">
    <h1 class="page-title">Kelulusan Siswa</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/kenaikan-kelas') ?>">Kenaikan Kelas</a> &nbsp;•&nbsp; 
        <span>Siklus Kelulusan</span>
    </div>
</div>

<div class="info-box">
    <div class="info-box-title">
        <i class="fas fa-exclamation-triangle"></i>
        Proses Permanen
    </div>
    <div class="info-box-text">
        <strong>Peringatan!</strong> Status kelulusan akan mengubah record siswa menjadi <strong>"Lulus"</strong> dan menghentikan status aktif di kelas ini. Pastikan seluruh tagihan keuangan telah diselesaikan oleh siswa sebelum melakukan konfirmasi kelulusan.
    </div>
</div>

<div class="kelas-info">
    <div class="kelas-info-left">
        <h3><i class="fas fa-school"></i> <?= esc($kelas['nama_kelas']) ?></h3>
        <div class="tahun">Tahun Ajaran: <?= esc($kelas['nama_tahun_ajaran']) ?></div>
    </div>
    <div class="kelas-info-right">
        <div class="label">Total Siswa Terdaftar</div>
        <div class="value"><?= count($siswa) ?></div>
    </div>
</div>

<?php if (empty($siswa)): ?>
    <div class="card">
        <div class="empty-state">
            <div style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;">📂</div>
            <h3>Kelas Sudah Kosong</h3>
            <p>Tidak ada siswa aktif yang ditemukan untuk diproses kelulusannya.</p>
            <br>
            <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas
            </a>
        </div>
    </div>
<?php else: ?>
    
    <div class="card">
        <form action="<?= base_url('admin/kenaikan-kelas/proses-kelulusan') ?>" method="POST" id="formKelulusan">
            <input type="hidden" name="id_kelas" value="<?= $kelas['id_kelas'] ?>">
            
            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="checkAll">
                        <label for="checkAll">Pilih Semua</label>
                    </div>
                    
                    <div class="selected-count" id="selectedCount">
                        <i class="fas fa-graduation-cap"></i> &nbsp;
                        <span id="countText">0 siswa dipilih</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnLuluskan" disabled>
                        <i class="fas fa-award"></i> Konfirmasi Kelulusan
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="checkAllTable" style="display: none;">
                            </th>
                            <th>NO</th>
                            <th>NIS SISWA</th>
                            <th>NAMA LENGKAP</th>
                            <th>L/P</th>
                            <th>NAMA WALI</th>
                            <th>KONTAK WALI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $index => $s): ?>
                            <tr>
                                <td class="checkbox-cell">
                                    <input type="checkbox" 
                                           name="siswa_ids[]" 
                                           value="<?= $s['id_siswa'] ?>" 
                                           class="siswa-checkbox">
                                </td>
                                <td style="font-weight: 700; color: var(--secondary);"><?= $index + 1 ?></td>
                                <td style="font-family: monospace; font-weight: 700;"><?= esc($s['nis']) ?></td>
                                <td><strong><?= esc($s['nama_lengkap']) ?></strong></td>
                                <td style="text-transform: uppercase; font-weight: 600;"><?= $s['jenis_kelamin'] ?></td>
                                <td><?= esc($s['nama_wali']) ?: '-' ?></td>
                                <td><?= esc($s['telp_wali']) ?: '-' ?></td>
                                <td>
                                    <span class="badge badge-success">Aktif</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
// Check all functionality
const checkAll = document.getElementById('checkAll');
const checkboxes = document.querySelectorAll('.siswa-checkbox');
const selectedCount = document.getElementById('selectedCount');
const countText = document.getElementById('countText');
const btnLuluskan = document.getElementById('btnLuluskan');
const formKelulusan = document.getElementById('formKelulusan');

function updateSelectedCount() {
    const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
    
    if (checked > 0) {
        selectedCount.classList.add('show');
        countText.textContent = `${checked} siswa dipilih`;
        btnLuluskan.disabled = false;
    } else {
        selectedCount.classList.remove('show');
        btnLuluskan.disabled = true;
    }
}

checkAll.addEventListener('change', function() {
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        if (this.checked) {
            cb.closest('tr').classList.add('selected');
        } else {
            cb.closest('tr').classList.remove('selected');
        }
    });
    updateSelectedCount();
});

checkboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        if (this.checked) {
            this.closest('tr').classList.add('selected');
        } else {
            this.closest('tr').classList.remove('selected');
        }
        
        // Update check all status
        const allChecked = Array.from(checkboxes).every(c => c.checked);
        const someChecked = Array.from(checkboxes).some(c => c.checked);
        
        checkAll.checked = allChecked;
        checkAll.indeterminate = someChecked && !allChecked;
        
        updateSelectedCount();
    });
});

// Konfirmasi sebelum submit
formKelulusan.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
    
    if (checked === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 siswa untuk diluluskan');
        return;
    }
    
    const confirmed = confirm(`⚠️ PERHATIAN!\n\nAnda akan meluluskan ${checked} siswa dari kelas ${<?= json_encode($kelas['nama_kelas']) ?>}.\n\nProses ini akan:\n- Mengubah status siswa menjadi "Lulus"\n- Melepaskan siswa dari kelas\n- Bersifat PERMANEN\n\nApakah Anda yakin ingin melanjutkan?`);
    
    if (!confirmed) {
        e.preventDefault();
    } else {
        // Show loading state
        btnLuluskan.disabled = true;
        btnLuluskan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses kelulusan...';
    }
});
</script>

<?= $this->include('admin/layouts/footer') ?>