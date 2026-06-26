<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL BILLING DETAIL THEME ==================== */
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
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 8px; }
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Grid System */
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
}

/* Sidebar Profile & Stats */
.profile-card { padding: 32px 24px; text-align: center; }
.profile-avatar {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, #06b6d4 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; font-weight: 800; color: white;
    margin: 0 auto 16px;
    box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.2);
}

.profile-name { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.profile-nis { font-size: 13px; color: var(--secondary); margin-bottom: 16px; font-weight: 600; }

.va-box {
    background: var(--primary-bg);
    border: 2px dashed var(--primary);
    border-radius: 12px;
    padding: 12px; margin-bottom: 20px;
}
.va-label { font-size: 10px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.va-number { font-size: 16px; font-weight: 800; color: #0e7490; font-family: 'JetBrains Mono', monospace; }

.stats-container { display: grid; grid-template-columns: 1fr; gap: 10px; text-align: left; }
.stat-item {
    background: #f8fafc; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border);
}
.stat-label { font-size: 11px; font-weight: 700; color: var(--secondary); text-transform: uppercase; margin-bottom: 4px; }
.stat-value { font-size: 16px; font-weight: 800; }

/* Main Content: Tahun Sections */
.tahun-section { margin-bottom: 28px; overflow: hidden; }
.tahun-header {
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-hover) 100%);
    color: white; padding: 16px 24px;
    display: flex; justify-content: space-between; align-items: center;
}
.tahun-title { font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px; }

/* Table Refinement */
.table-responsive { border: 1px solid var(--border); border-top: none; border-radius: 0 0 16px 16px; background: white; }
table { width: 100%; border-collapse: collapse; }
table th {
    background: #f8fafc; padding: 14px 20px; text-align: left;
    font-size: 11px; font-weight: 800; color: var(--secondary);
    text-transform: uppercase; letter-spacing: 0.5px;
}
table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
table tr:last-child td { border-bottom: none; }

.tagihan-info-main { font-weight: 700; color: #1e293b; display: block; }
.tagihan-info-sub { font-size: 11px; color: var(--secondary); font-weight: 500; }

/* Badges */
.badge {
    padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 800;
    display: inline-flex; align-items: center; gap: 4px;
}
.badge-success { background: #dcfce7; color: #15803d; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #b91c1c; }

/* Summary Row */
.row-total { background: #f8fafc; font-weight: 800; }
.row-total td { border-top: 2px solid var(--border); color: #0f172a; }

.btn-back {
    margin-top: 20px; background: white; color: var(--secondary);
    border: 1.5px solid var(--border); font-weight: 700; width: 100%;
    padding: 12px; border-radius: 10px; cursor: pointer; display: block;
    text-align: center; text-decoration: none; transition: all 0.2s;
}
.btn-back:hover { background: #f1f5f9; color: #0f172a; }

@media (max-width: 850px) {
    .content-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <h1 class="page-title">Ikhtisar Tagihan Siswa</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/tagihan') ?>">Data Tagihan</a> &nbsp;•&nbsp; 
        <span>Detail Pembayaran</span>
    </div>
</div>

<div class="content-grid">
    <div class="sticky-sidebar">
        <div class="card profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
            </div>
            
            <div class="profile-name"><?= esc($siswa['nama_lengkap']) ?></div>
            <div class="profile-nis">NIS: <?= esc($siswa['nis']) ?></div>
            
            <div class="va-box">
                <div class="va-label">ID Virtual Account</div>
                <div class="va-number"><?= esc($siswa['virtual_account']) ?></div>
            </div>

            <?php
                $totalTagihan = 0; $totalDibayar = 0; $totalTunggakan = 0;
                foreach ($tagihan_by_tahun as $tahun => $tagihanList) {
                    foreach ($tagihanList as $t) {
                        $totalTagihan += $t['nominal_akhir'];
                        $totalDibayar += $t['nominal_dibayar'];
                        $totalTunggakan += $t['sisa_tagihan'];
                    }
                }
            ?>
            
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-label">Total Kewajiban</div>
                    <div class="stat-value">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></div>
                </div>
                <div class="stat-item" style="border-left: 4px solid #10b981;">
                    <div class="stat-label" style="color: #10b981;">Telah Dibayar</div>
                    <div class="stat-value" style="color: #059669;">Rp <?= number_format($totalDibayar, 0, ',', '.') ?></div>
                </div>
                <div class="stat-item" style="border-left: 4px solid #ef4444;">
                    <div class="stat-label" style="color: #ef4444;">Sisa Tunggakan</div>
                    <div class="stat-value" style="color: #dc2626;">Rp <?= number_format($totalTunggakan, 0, ',', '.') ?></div>
                </div>
            </div>
            
            <a href="<?= base_url('admin/tagihan') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> &nbsp; Kembali
            </a>
        </div>
    </div>
    
    <div>
        <?php if (empty($tagihan_by_tahun)): ?>
            <div class="card" style="padding: 60px 20px; text-align: center;">
                <div style="font-size: 50px; margin-bottom: 20px;">📂</div>
                <h3 style="font-weight: 800; color: #1e293b;">Data Tidak Ditemukan</h3>
                <p style="color: var(--secondary);">Siswa ini belum memiliki riwayat tagihan terdaftar.</p>
            </div>
        <?php else: ?>
            
            <?php foreach ($tagihan_by_tahun as $tahun => $tagihanList): ?>
                <?php
                    $tahunTotal = 0; $tahunDibayar = 0; $tahunSisa = 0;
                    foreach ($tagihanList as $t) {
                        $tahunTotal += $t['nominal_akhir'];
                        $tahunDibayar += $t['nominal_dibayar'];
                        $tahunSisa += $t['sisa_tagihan'];
                    }
                ?>
                
                <div class="tahun-section card">
                    <div class="tahun-header">
                        <div>
                            <div class="tahun-title"><i class="fas fa-calendar-check"></i> TA <?= esc($tahun) ?></div>
                            <div style="font-size: 11px; opacity: 0.8; font-weight: 600; text-transform: uppercase;">
                                <?= count($tagihanList) ?> Item Penagihan
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; opacity: 0.8; font-weight: 700; text-transform: uppercase;">Outstanding TA</div>
                            <div style="font-size: 20px; font-weight: 900;">Rp <?= number_format($tahunSisa, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Deskripsi Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Dibayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tagihanList as $t): ?>
                                <tr>
                                    <td>
                                        <span class="tagihan-info-main"><?= esc($t['nama_tagihan']) ?></span>
                                        <span class="tagihan-info-sub">
                                            <?= $t['bulan_tagihan'] ? 'Periode: Bulan '.$t['bulan_tagihan'] : 'Non-periodik' ?>
                                            <?= $t['nominal_potongan'] > 0 ? ' • Disc: Rp '.number_format($t['nominal_potongan'], 0, ',', '.') : '' ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700;">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></td>
                                    <td style="color: #059669; font-weight: 700;">Rp <?= number_format($t['nominal_dibayar'], 0, ',', '.') ?></td>
                                    <td style="color: #dc2626; font-weight: 700;">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($t['status_tagihan'] === 'lunas'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> LUNAS</span>
                                        <?php elseif ($t['status_tagihan'] === 'cicil'): ?>
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> DICICIL</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> TUNGGAKAN</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <tr class="row-total">
                                    <td>RINGKASAN TAHUN AJARAN</td>
                                    <td>Rp <?= number_format($tahunTotal, 0, ',', '.') ?></td>
                                    <td style="color: #059669;">Rp <?= number_format($tahunDibayar, 0, ',', '.') ?></td>
                                    <td style="color: #dc2626;">Rp <?= number_format($tahunSisa, 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($tahunSisa == 0): ?>
                                            <span class="badge badge-success">✓ CLEAR</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">⚠ PENDING</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>