<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL DETAIL THEME ==================== */
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

body { background: #f8fafc; font-family: 'Inter', sans-serif; }

/* Header & Breadcrumb */
.page-header { margin-bottom: 28px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 8px; }
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Grid Layout */
.content-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
    align-items: start;
}

.card {
    background: #ffffff;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* Sidebar Profile Card */
.profile-card { padding: 32px 24px; text-align: center; }
.profile-avatar {
    width: 110px; height: 110px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, #06b6d4 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 42px; font-weight: 800; color: white;
    margin: 0 auto 20px;
    box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.3);
    border: 4px solid white;
}

.profile-name { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px; line-height: 1.2; }
.profile-nis { font-size: 14px; color: var(--secondary); font-weight: 600; margin-bottom: 20px; }

/* Status Badges */
.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 16px; border-radius: 30px;
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    margin-bottom: 24px;
}
.status-aktif { background: #dcfce7; color: #15803d; }
.status-nonaktif { background: #fee2e2; color: #b91c1c; }
.status-lulus { background: #fef3c7; color: #92400e; }

/* VA Box Modern */
.va-box {
    background: var(--primary-bg);
    border: 2px dashed var(--primary);
    border-radius: 12px;
    padding: 16px; margin-bottom: 24px;
}
.va-label { font-size: 10px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.va-number { font-size: 18px; font-weight: 800; color: #0e7490; font-family: 'JetBrains Mono', monospace; letter-spacing: 1px; }

/* Main Info Content */
.info-sheet { padding: 32px; }
.info-section { margin-bottom: 35px; }
.info-section-title {
    font-size: 14px; font-weight: 800; color: var(--primary);
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 16px; padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
    display: flex; align-items: center; gap: 10px;
}

.info-row {
    display: grid; grid-template-columns: 180px 1fr;
    padding: 14px 0; border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 13px; color: var(--secondary); font-weight: 600; }
.info-value { font-size: 14px; color: #334155; font-weight: 500; }
.info-value strong { color: #0f172a; font-weight: 700; }

/* Utility Classes */
.badge-unit {
    background: var(--primary-light); color: var(--primary-hover);
    padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 12px;
}

.btn {
    padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s; border: none; cursor: pointer; width: 100%;
}
.btn-edit { background: var(--primary); color: white; }
.btn-edit:hover { background: var(--primary-hover); transform: translateY(-2px); }
.btn-back { background: #f1f5f9; color: var(--secondary); margin-top: 10px; }
.btn-back:hover { background: #e2e8f0; color: #1e293b; }

@media (max-width: 850px) {
    .content-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <h1 class="page-title">Profil Detail Siswa</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/siswa') ?>">Manajemen Siswa</a> &nbsp;•&nbsp; 
        <span>Detail Siswa</span>
    </div>
</div>

<div class="content-grid">
    <div class="card profile-card">
        <div class="profile-avatar">
            <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
        </div>
        
        <div class="profile-name"><?= esc($siswa['nama_lengkap']) ?></div>
        <div class="profile-nis">NIS: <?= esc($siswa['nis']) ?></div>
        
        <div class="status-pill status-<?= $siswa['status_siswa'] ?>">
            <i class="fas fa-circle" style="font-size: 8px;"></i>
            <?= strtoupper($siswa['status_siswa']) ?>
        </div>
        
        <div class="va-box">
            <div class="va-label">Virtual Account</div>
            <div class="va-number"><?= esc($siswa['virtual_account']) ?></div>
        </div>
        
        <div class="action-area">
            <a href="<?= base_url('admin/siswa/edit/' . $siswa['id_siswa']) ?>" class="btn btn-edit">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
            <a href="<?= base_url('admin/siswa') ?>" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card info-sheet">
        <div class="info-section">
            <div class="info-section-title">
                <i class="fas fa-user"></i> Data Pribadi
            </div>
            
            <div class="info-row">
                <div class="info-label">Nomor Induk Siswa</div>
                <div class="info-value"><strong><?= esc($siswa['nis']) ?></strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">NISN</div>
                <div class="info-value"><?= $siswa['nisn'] ? esc($siswa['nisn']) : '<em style="color:#94a3b8;">Tidak diisi</em>' ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value"><?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tempat, Tanggal Lahir</div>
                <div class="info-value">
                    <?= date('d M Y', strtotime($siswa['tanggal_lahir'])) ?>
                    <span style="color: var(--secondary); font-size: 12px; margin-left: 8px;">
                        (Usia: <?= floor((time() - strtotime($siswa['tanggal_lahir'])) / (365.25 * 24 * 60 * 60)) ?> Tahun)
                    </span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Alamat Lengkap</div>
                <div class="info-value"><?= $siswa['alamat'] ? nl2br(esc($siswa['alamat'])) : '---' ?></div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-section-title">
                <i class="fas fa-users"></i> Orang Tua / Wali
            </div>
            <div class="info-row">
                <div class="info-label">Nama Wali</div>
                <div class="info-value"><?= $siswa['nama_wali'] ? esc($siswa['nama_wali']) : '---' ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Kontak Wali</div>
                <div class="info-value">
                    <?= $siswa['telp_wali'] ? esc($siswa['telp_wali']) : '---' ?>
                    <?php if($siswa['telp_wali']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $siswa['telp_wali']) ?>" target="_blank" style="margin-left:10px; color:#10b981;">
                            <i class="fab fa-whatsapp"></i> Hubungi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-section-title">
                <i class="fas fa-graduation-cap"></i> Penempatan Akademik
            </div>
            <div class="info-row">
                <div class="info-label">Kelas Saat Ini</div>
                <div class="info-value">
                    <?php if ($siswa['nama_kelas']): ?>
                        <span class="badge-unit">
                            Kelas <?= esc($siswa['nama_kelas']) ?> (Tingkat <?= esc($siswa['tingkat']) ?>)
                        </span>
                    <?php else: ?>
                        <span style="color: #ef4444; font-weight: 600;">Belum Masuk Kelas</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tahun Ajaran</div>
                <div class="info-value"><?= esc($siswa['nama_tahun_ajaran'] ?? '---') ?></div>
            </div>
        </div>

        <div class="info-section" style="margin-bottom:0; opacity: 0.7;">
            <div class="info-section-title" style="border-bottom-color: #f1f5f9; color: #64748b;">
                <i class="fas fa-cog"></i> Log Sistem
            </div>
            <div class="info-row" style="font-size: 12px;">
                <div class="info-label">Terdaftar pada</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($siswa['created_at'])) ?> WIB</div>
            </div>
            <div class="info-row" style="font-size: 12px;">
                <div class="info-label">Pembaruan terakhir</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($siswa['updated_at'])) ?> WIB</div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>