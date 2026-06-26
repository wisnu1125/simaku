<?= $this->include('admin/layouts/header') ?>

<style>
    .page-header {
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .breadcrumb {
        font-size: 14px;
        color: #6b7280;
    }
    
    .breadcrumb a {
        color: #14b8a6;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        max-width: 900px;
        margin-bottom: 20px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 14px;
    }
    
    .info-value {
        color: #1f2937;
        font-size: 14px;
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        display: inline-block;
    }
    
    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin: 24px 0 12px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .code-block {
        background: #1f2937;
        color: #f9fafb;
        padding: 16px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        transition: all 0.2s;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: #ffffff;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Detail Audit Log</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> / 
        <a href="<?= base_url('admin/audit-log') ?>">Audit Log</a> / 
        Detail
    </div>
</div>

<div class="card">
    <div class="info-grid">
        <div class="info-label">ID Log</div>
        <div class="info-value">#<?= $log['id_log'] ?></div>
        
        <div class="info-label">Waktu</div>
        <div class="info-value">
            <?= date('d F Y, H:i:s', strtotime($log['created_at'])) ?> WIB
        </div>
        
        <div class="info-label">User</div>
        <div class="info-value">
            <strong><?= esc($log['nama_lengkap']) ?></strong> (<?= esc($log['username']) ?>)<br>
            <small style="color: #6b7280;"><?= esc($log['email']) ?></small>
        </div>
        
        <div class="info-label">Modul</div>
        <div class="info-value">
            <span class="badge badge-info"><?= esc(ucfirst($log['modul'])) ?></span>
        </div>
        
        <div class="info-label">Aksi</div>
        <div class="info-value">
            <?php if ($log['aksi'] === 'create'): ?>
                <span class="badge badge-success">CREATE</span>
            <?php elseif ($log['aksi'] === 'update'): ?>
                <span class="badge badge-warning">UPDATE</span>
            <?php elseif ($log['aksi'] === 'delete'): ?>
                <span class="badge badge-danger">DELETE</span>
            <?php else: ?>
                <span class="badge badge-info"><?= strtoupper($log['aksi']) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="info-label">Keterangan</div>
        <div class="info-value"><?= esc($log['keterangan']) ?></div>
        
        <div class="info-label">IP Address</div>
        <div class="info-value">
            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px;">
                <?= esc($log['ip_address']) ?>
            </code>
        </div>
        
        <div class="info-label">User Agent</div>
        <div class="info-value">
            <small style="color: #6b7280;"><?= esc($log['user_agent']) ?></small>
        </div>
    </div>
    
    <?php if ($log['data_lama']): ?>
    <div class="section-title">Data Lama (Before)</div>
    <div class="code-block"><?= htmlspecialchars(json_encode(json_decode($log['data_lama']), JSON_PRETTY_PRINT)) ?></div>
    <?php endif; ?>
    
    <?php if ($log['data_baru']): ?>
    <div class="section-title">Data Baru (After)</div>
    <div class="code-block"><?= htmlspecialchars(json_encode(json_decode($log['data_baru']), JSON_PRETTY_PRINT)) ?></div>
    <?php endif; ?>
    
    <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
        <a href="<?= base_url('admin/audit-log') ?>" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>