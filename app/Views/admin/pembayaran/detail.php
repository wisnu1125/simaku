<?= $this->include('admin/layouts/header') ?>

<style>
.receipt-card { max-width: 640px; margin: 0 auto; overflow: hidden; }
.receipt-head { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: #fff; padding: 24px 24px 20px; text-align: center; }
.receipt-head .kwitansi { font-family: 'Roboto Mono', monospace; font-size: 13px; opacity: .9; letter-spacing: .5px; }
.receipt-head .nominal { font-size: 28px; font-weight: 900; margin-top: 10px; }
.receipt-head .date { font-size: 12px; opacity: .85; margin-top: 4px; }

.info-section { padding: 18px 22px; border-bottom: 1px solid var(--border-soft); }
.info-section:last-of-type { border-bottom: none; }
.info-section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.info-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 7px 0; font-size: 13px; }
.info-row .k { color: var(--muted); }
.info-row .v { color: var(--ink); font-weight: 600; text-align: right; }
.info-row .v.mono { font-family: 'Roboto Mono', monospace; font-weight: 500; }

.action-row { display: flex; flex-wrap: wrap; gap: 10px; padding: 18px 22px; }
.action-row .btn { flex: 1; min-width: 140px; }

.cancel-notice { margin: 18px 22px 0; padding: 14px 16px; background: var(--danger-bg); border: 1px solid var(--danger-border); border-radius: var(--r-md); font-size: 12.5px; color: #7f1d1d; }
.cancel-notice strong { display: block; margin-bottom: 4px; }
</style>

<div class="page-header" style="margin-bottom:18px;">
    <div class="page-title">Detail Pembayaran</div>
    <div class="page-subtitle"><a href="<?= base_url('admin/pembayaran') ?>" style="color:var(--brand); text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Pembayaran</a></div>
</div>

<div class="card receipt-card">
    <div class="receipt-head">
        <div class="kwitansi"><?= esc($pembayaran['nomor_kwitansi']) ?></div>
        <div class="nominal">Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.') ?></div>
        <div class="date"><?= date('d F Y, H:i', strtotime($pembayaran['tanggal_bayar'])) ?> WIB</div>
    </div>

    <?php if ($pembayaran['status_pembayaran'] === 'dibatalkan'): ?>
    <div class="cancel-notice">
        <strong><i class="fa-solid fa-triangle-exclamation"></i> Pembayaran ini sudah dibatalkan</strong>
        Alasan: <?= esc($pembayaran['alasan_batal']) ?><br>
        Dibatalkan oleh <?= esc($pembayaran['nama_pembatal']) ?> · <?= date('d M Y, H:i', strtotime($pembayaran['tanggal_batal'])) ?> WIB
    </div>
    <?php endif; ?>

    <div class="info-section" style="margin-top:8px;">
        <div class="info-section-title"><i class="fa-solid fa-user"></i> Data Siswa</div>
        <div class="info-row"><span class="k">NIS</span><span class="v mono"><?= esc($pembayaran['nis']) ?></span></div>
        <div class="info-row"><span class="k">Nama Lengkap</span><span class="v"><?= esc($pembayaran['nama_siswa']) ?></span></div>
        <div class="info-row"><span class="k">Kelas</span><span class="v"><?= $pembayaran['nama_kelas'] ? esc($pembayaran['nama_kelas']) : '-' ?></span></div>
        <div class="info-row"><span class="k">Virtual Account</span><span class="v mono"><?= esc($pembayaran['virtual_account']) ?></span></div>
    </div>

    <div class="info-section">
        <div class="info-section-title"><i class="fa-solid fa-file-invoice-dollar"></i> Data Tagihan</div>
        <div class="info-row">
            <span class="k">Jenis Tagihan</span>
            <span class="v"><?= esc($pembayaran['nama_tagihan']) ?><?= $pembayaran['bulan_tagihan'] ? ' (Bln ' . $pembayaran['bulan_tagihan'] . ')' : '' ?></span>
        </div>
        <div class="info-row"><span class="k">Tahun Ajaran</span><span class="v"><?= esc($pembayaran['nama_tahun_ajaran']) ?></span></div>
        <div class="info-row"><span class="k">Total Tagihan</span><span class="v mono">Rp <?= number_format($pembayaran['nominal_akhir'], 0, ',', '.') ?></span></div>
        <div class="info-row"><span class="k">Total Dibayar</span><span class="v mono" style="color:var(--success);">Rp <?= number_format($pembayaran['nominal_dibayar'], 0, ',', '.') ?></span></div>
        <div class="info-row"><span class="k">Sisa Tagihan</span><span class="v mono" style="color:var(--danger);">Rp <?= number_format($pembayaran['sisa_tagihan'], 0, ',', '.') ?></span></div>
    </div>

    <div class="info-section">
        <div class="info-section-title"><i class="fa-solid fa-receipt"></i> Detail Transaksi</div>
        <div class="info-row"><span class="k">Metode</span><span class="v"><?= $pembayaran['metode_pembayaran'] === 'tunai' ? '<span class="badge badge-success">Tunai</span>' : '<span class="badge badge-info">Transfer</span>' ?></span></div>
        <div class="info-row"><span class="k">Status</span><span class="v"><?= $pembayaran['status_pembayaran'] === 'valid' ? '<span class="badge badge-success">Valid</span>' : '<span class="badge badge-danger">Dibatalkan</span>' ?></span></div>
        <?php if ($pembayaran['keterangan']): ?>
        <div class="info-row"><span class="k">Keterangan</span><span class="v"><?= nl2br(esc($pembayaran['keterangan'])) ?></span></div>
        <?php endif; ?>
        <div class="info-row"><span class="k">Petugas</span><span class="v"><?= esc($pembayaran['nama_petugas']) ?></span></div>
    </div>

    <div class="action-row">
        <a href="<?= base_url('admin/pembayaran/print/' . $pembayaran['id_pembayaran']) ?>" class="btn btn-secondary" target="_blank"><i class="fa-solid fa-print"></i> Cetak Kwitansi</a>
        <?php if ($pembayaran['status_pembayaran'] === 'valid'): ?>
        <button type="button" class="btn btn-danger" onclick="openPanel('batalPanel')" style="flex:1; min-width:140px;"><i class="fa-solid fa-ban"></i> Batalkan</button>
        <?php endif; ?>
    </div>
</div>

<?php if ($pembayaran['status_pembayaran'] === 'valid'): ?>
<!-- ===================== PANEL: Batalkan Pembayaran (inline) ===================== -->
<div class="inline-panel" id="batalPanel" style="max-width:640px; margin:16px auto 0;">
    <div class="inline-panel-header"><h3><i class="fa-solid fa-triangle-exclamation" style="color:var(--danger); margin-right:6px;"></i>Batalkan Pembayaran</h3><button type="button" class="inline-panel-close" onclick="closePanel('batalPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="<?= base_url('admin/pembayaran/batal/' . $pembayaran['id_pembayaran']) ?>" method="POST">
        <div class="inline-panel-body">
            <p style="font-size:13px; color:var(--body); margin-bottom:16px;">
                Pembayaran dengan nomor kwitansi <strong><?= esc($pembayaran['nomor_kwitansi']) ?></strong> akan dibatalkan dan nominalnya dikembalikan ke tagihan (tagihan akan kembali berstatus belum lunas sebesar nilai ini).
            </p>
            <div class="field" style="margin-bottom:0;">
                <label class="required">Alasan Pembatalan</label>
                <textarea class="input" name="alasan_batal" rows="3" placeholder="Jelaskan alasan pembatalan…" required></textarea>
            </div>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('batalPanel')">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-check"></i> Ya, Batalkan Pembayaran</button>
        </div>
    </form>
</div>
<?php endif; ?>

<?= $this->include('admin/layouts/footer') ?>
