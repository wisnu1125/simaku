<?= $this->include('admin/layouts/header') ?>

<style>
    /* ==================== BLUISH TEAL THEME CSS ==================== */
    :root {
        --primary: #0891b2;       /* Warna Utama */
        --primary-hover: #0e7490;
        --primary-light: #cffafe;
        --primary-bg: #ecfeff;
        --secondary: #64748b;
        --bg-body: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --radius: 12px;
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-main);
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    .page-header {
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }
    
    .breadcrumb {
        font-size: 14px;
        color: var(--text-muted);
    }
    
    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
        color: var(--primary-hover);
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        max-width: 800px;
    }
    
    .card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }
    
    .card-header {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
        color: white;
        padding: 24px;
        border-radius: 12px 12px 0 0;
        margin: -24px -24px 24px -24px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .kwitansi-number {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
        font-family: monospace;
        position: relative; z-index: 2;
        letter-spacing: 1px;
    }
    
    .kwitansi-date {
        font-size: 14px;
        opacity: 0.9;
        position: relative; z-index: 2;
        font-weight: 500;
    }
    
    .info-section {
        margin-bottom: 24px;
    }
    
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border);
        display: flex; align-items: center; gap: 8px;
    }
    
    .info-section-title i {
        color: var(--primary);
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .info-value {
        font-size: 14px;
        color: var(--text-main);
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    
    .badge-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .badge-info {
        background: var(--primary-bg);
        color: var(--primary);
        border: 1px solid var(--primary-light);
    }
    
    .nominal-highlight {
        font-size: 32px;
        font-weight: 800;
        color: var(--primary);
        text-align: center;
        padding: 24px;
        background: var(--primary-bg);
        border: 1px solid var(--primary-light);
        border-radius: 12px;
        margin: 24px 0;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.2);
    }
    
    .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.3);
    }
    
    .btn-secondary {
        background: #ffffff;
        color: var(--secondary);
        border: 1px solid var(--border);
    }
    
    .btn-secondary:hover {
        background: #f8fafc;
        color: var(--text-main);
        border-color: #cbd5e1;
    }
    
    .btn-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    
    .btn-danger:hover {
        background: #fecaca;
        color: #991b1b;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }
    
    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    /* Modal Styling updated to Theme */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
    }
    
    .modal-content {
        background: white;
        margin: 10% auto;
        padding: 0;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--bg-body);
    }
    
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
    }
    
    .modal-body {
        padding: 24px;
    }
    
    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        background: var(--bg-body);
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        transition: 0.2s;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-bg);
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    
    /* Code styling adjustment */
    code {
        background: var(--bg-body) !important;
        color: var(--text-main);
        border: 1px solid var(--border);
    }
    
    @media print {
        .page-header,
        .breadcrumb,
        .action-buttons,
        .btn,
        button,
        #batalModal {
            display: none !important;
        }
        
        body {
            background: white;
            padding: 0;
        }
        
        .content-grid {
            max-width: 100%;
        }
        
        .card {
            border: 2px solid #333;
            box-shadow: none;
            page-break-inside: avoid;
        }
        
        .card-header {
            background: #0891b2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .nominal-highlight {
            background: #ecfeff !important;
            color: #0891b2 !important;
            border: 1px solid #cffafe;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .alert-danger {
            border: 2px solid #ef4444;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .badge {
            border: 1px solid currentColor;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .info-row {
            page-break-inside: avoid;
        }
    }
    
    @media (max-width: 768px) {
        .info-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">Detail Pembayaran</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> / 
        <a href="<?= base_url('admin/pembayaran') ?>">Pembayaran</a> / 
        Detail
    </div>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <div class="kwitansi-number"><?= esc($pembayaran['nomor_kwitansi']) ?></div>
            <div class="kwitansi-date">
                <?= date('d F Y, H:i', strtotime($pembayaran['tanggal_bayar'])) ?> WIB
            </div>
        </div>
        
        <?php if ($pembayaran['status_pembayaran'] === 'dibatalkan'): ?>
        <div class="alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> Pembayaran Dibatalkan</strong><br>
            Alasan: <?= esc($pembayaran['alasan_batal']) ?><br>
            Dibatalkan oleh: <?= esc($pembayaran['nama_pembatal']) ?><br>
            Tanggal batal: <?= date('d F Y, H:i', strtotime($pembayaran['tanggal_batal'])) ?> WIB
        </div>
        <?php endif; ?>
        
        <div class="nominal-highlight">
            Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.') ?>
        </div>
        
        <div class="info-section">
            <div class="info-section-title"><i class="fas fa-user"></i> Data Siswa</div>
            
            <div class="info-row">
                <div class="info-label">NIS</div>
                <div class="info-value"><strong><?= esc($pembayaran['nis']) ?></strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value"><?= esc($pembayaran['nama_siswa']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Kelas</div>
                <div class="info-value">
                    <?= $pembayaran['nama_kelas'] ? esc($pembayaran['nama_kelas']) : '-' ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Virtual Account</div>
                <div class="info-value">
                    <code style="padding: 4px 8px; border-radius: 4px;">
                        <?= esc($pembayaran['virtual_account']) ?>
                    </code>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-section-title"><i class="fas fa-file-invoice-dollar"></i> Data Tagihan</div>
            
            <div class="info-row">
                <div class="info-label">Jenis Tagihan</div>
                <div class="info-value">
                    <?= esc($pembayaran['nama_tagihan']) ?>
                    <?php if ($pembayaran['bulan_tagihan']): ?>
                        (Bulan <?= $pembayaran['bulan_tagihan'] ?>)
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tahun Ajaran</div>
                <div class="info-value"><?= esc($pembayaran['nama_tahun_ajaran']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Total Tagihan</div>
                <div class="info-value">Rp <?= number_format($pembayaran['nominal_akhir'], 0, ',', '.') ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Total Dibayar</div>
                <div class="info-value" style="color: #0891b2; font-weight: 600;">
                    Rp <?= number_format($pembayaran['nominal_dibayar'], 0, ',', '.') ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Sisa Tagihan</div>
                <div class="info-value" style="color: #ef4444; font-weight: 600;">
                    Rp <?= number_format($pembayaran['sisa_tagihan'], 0, ',', '.') ?>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-section-title"><i class="fas fa-receipt"></i> Detail Pembayaran</div>
            
            <div class="info-row">
                <div class="info-label">Nomor Kwitansi</div>
                <div class="info-value">
                    <code style="padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                        <?= esc($pembayaran['nomor_kwitansi']) ?>
                    </code>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tanggal Bayar</div>
                <div class="info-value">
                    <?= date('d F Y, H:i', strtotime($pembayaran['tanggal_bayar'])) ?> WIB
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nominal Bayar</div>
                <div class="info-value" style="font-weight: 600; color: #0891b2;">
                    Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.') ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">
                    <?php if ($pembayaran['metode_pembayaran'] === 'tunai'): ?>
                        <span class="badge badge-success"><i class="fas fa-money-bill-wave"></i> Tunai</span>
                    <?php else: ?>
                        <span class="badge badge-info"><i class="fas fa-university"></i> Transfer</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <?php if ($pembayaran['status_pembayaran'] === 'valid'): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Valid</span>
                    <?php else: ?>
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Dibatalkan</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($pembayaran['keterangan']): ?>
            <div class="info-row">
                <div class="info-label">Keterangan</div>
                <div class="info-value"><?= nl2br(esc($pembayaran['keterangan'])) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="info-row">
                <div class="info-label">Petugas</div>
                <div class="info-value"><?= esc($pembayaran['nama_petugas']) ?></div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="<?= base_url('admin/pembayaran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <a href="<?= base_url('admin/pembayaran/print/' . $pembayaran['id_pembayaran']) ?>" class="btn btn-primary" target="_blank">
    <i class="fas fa-print"></i> Cetak Kwitansi
</a>
            
            <?php if ($pembayaran['status_pembayaran'] === 'valid'): ?>
            <button type="button" class="btn btn-danger" onclick="showBatalModal()">
                <i class="fas fa-ban"></i> Batalkan Pembayaran
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="batalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Batalkan Pembayaran</h3>
        </div>
        <form action="<?= base_url('admin/pembayaran/batal/' . $pembayaran['id_pembayaran']) ?>" method="POST">
            <div class="modal-body">
                <p style="color: #6b7280; margin-bottom: 16px;">
                    Pembayaran dengan nomor kwitansi <strong><?= esc($pembayaran['nomor_kwitansi']) ?></strong> 
                    akan dibatalkan dan nominal akan dikembalikan ke tagihan.
                </p>
                
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937;">
                    Alasan Pembatalan <span style="color: #ef4444;">*</span>
                </label>
                <textarea 
                    class="form-control" 
                    name="alasan_batal" 
                    placeholder="Jelaskan alasan pembatalan..."
                    required
                ></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBatalModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-check"></i> Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showBatalModal() {
    document.getElementById('batalModal').style.display = 'block';
}

function closeBatalModal() {
    document.getElementById('batalModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('batalModal');
    if (event.target == modal) {
        closeBatalModal();
    }
}
</script>

<?= $this->include('admin/layouts/footer') ?>