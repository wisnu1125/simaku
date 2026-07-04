<?= $this->include('admin/layouts/header') ?>

<style>
.dt-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 900px) { .dt-grid { grid-template-columns: 300px 1fr; align-items: start; } }

.profile-card { padding: 28px 22px; text-align: center; position: sticky; top: 84px; }
.profile-avatar { width: 84px; height: 84px; border-radius: 50%; background: linear-gradient(135deg, var(--brand), var(--brand-dark)); display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; color: #fff; margin: 0 auto 14px; }
.profile-name { font-size: 16px; font-weight: 800; color: var(--ink); }
.profile-nis { font-size: 12.5px; color: var(--muted); margin-top: 2px; }
.va-box { margin-top: 16px; padding: 10px; background: var(--border-soft); border-radius: var(--r-sm); }
.va-box .lbl { font-size: 10px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: .4px; }
.va-box .val { font-family: 'Roboto Mono', monospace; font-size: 12.5px; color: var(--ink); font-weight: 700; margin-top: 2px; }

.dt-stats { margin-top: 18px; display: flex; flex-direction: column; gap: 8px; }
.dt-stat { padding: 12px 14px; border-radius: var(--r-sm); text-align: left; }
.dt-stat .lbl { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
.dt-stat .val { font-size: 15px; font-weight: 900; margin-top: 2px; }

.ta-section { margin-bottom: 16px; overflow: hidden; }
.ta-header { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: #fff; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
.ta-header .title { font-size: 14.5px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
.ta-header .sub { font-size: 10.5px; opacity: .85; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }
.ta-header .amount { font-size: 18px; font-weight: 900; text-align: right; }

.ta-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 13px 20px; border-bottom: 1px solid var(--border-soft); }
.ta-row:last-child { border-bottom: none; }
.ta-row .desc strong { font-size: 13.5px; color: var(--ink); display: block; }
.ta-row .desc span { font-size: 11.5px; color: var(--muted); }
.ta-row .amounts { display: flex; gap: 18px; align-items: center; flex-shrink: 0; }
.ta-row .amounts .col { text-align: right; min-width: 92px; }
.ta-row .amounts .col .l { font-size: 9.5px; color: var(--muted); text-transform: uppercase; font-weight: 700; }
.ta-row .amounts .col .v { font-size: 12.5px; font-weight: 700; font-family: 'Roboto Mono', monospace; }

.ta-summary { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; background: var(--border-soft); font-size: 12.5px; font-weight: 700; }

@media (max-width: 640px) {
    .ta-row { flex-direction: column; align-items: flex-start; gap: 8px; }
    .ta-row .amounts { width: 100%; justify-content: space-between; }
    .ta-row .amounts .col { text-align: left; }
}
</style>

<div class="page-header" style="margin-bottom:18px;">
    <div class="page-title">Ikhtisar Tagihan Siswa</div>
    <div class="page-subtitle"><a href="<?= base_url('admin/tagihan') ?>" style="color:var(--brand); text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tagihan</a></div>
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

<div class="dt-grid">
    <div class="card profile-card">
        <div class="profile-avatar"><?= esc(strtoupper(substr($siswa['nama_lengkap'], 0, 1))) ?></div>
        <div class="profile-name"><?= esc($siswa['nama_lengkap']) ?></div>
        <div class="profile-nis">NIS <?= esc($siswa['nis']) ?></div>
        <div class="va-box"><div class="lbl">Virtual Account</div><div class="val"><?= esc($siswa['virtual_account']) ?></div></div>

        <div class="dt-stats">
            <div class="dt-stat" style="background:var(--border-soft);"><div class="lbl" style="color:var(--muted);">Total Kewajiban</div><div class="val" style="color:var(--ink);">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></div></div>
            <div class="dt-stat" style="background:var(--success-bg);"><div class="lbl" style="color:var(--success);">Telah Dibayar</div><div class="val" style="color:var(--success);">Rp <?= number_format($totalDibayar, 0, ',', '.') ?></div></div>
            <div class="dt-stat" style="background:var(--danger-bg);"><div class="lbl" style="color:var(--danger);">Sisa Tunggakan</div><div class="val" style="color:var(--danger);">Rp <?= number_format($totalTunggakan, 0, ',', '.') ?></div></div>
        </div>

        <a href="<?= base_url('admin/pembayaran#bayar-' . $siswa['id_siswa']) ?>" class="btn btn-primary btn-block" style="margin-top:16px;"><i class="fa-solid fa-wallet"></i> Input Pembayaran</a>
    </div>

    <div>
        <?php if (empty($tagihan_by_tahun)): ?>
            <div class="card empty-state"><i class="fa-solid fa-folder-open"></i><p>Siswa ini belum memiliki riwayat tagihan.</p></div>
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
                <div class="card ta-section">
                    <div class="ta-header">
                        <div>
                            <div class="title"><i class="fa-solid fa-calendar-check"></i> TA <?= esc($tahun) ?></div>
                            <div class="sub"><?= count($tagihanList) ?> item tagihan</div>
                        </div>
                        <div>
                            <div class="sub" style="text-align:right;">Sisa Tahun Ini</div>
                            <div class="amount">Rp <?= number_format($tahunSisa, 0, ',', '.') ?></div>
                        </div>
                    </div>

                    <?php foreach ($tagihanList as $t): ?>
                    <div class="ta-row">
                        <div class="desc">
                            <strong><?= esc($t['nama_tagihan']) ?></strong>
                            <span>
                                <?= $t['bulan_tagihan'] ? 'Bulan ' . $t['bulan_tagihan'] : 'Non-periodik' ?>
                                <?= $t['nominal_potongan'] > 0 ? ' · Potongan Rp ' . number_format($t['nominal_potongan'], 0, ',', '.') : '' ?>
                            </span>
                        </div>
                        <div class="amounts">
                            <div class="col"><div class="l">Nominal</div><div class="v"><?= number_format($t['nominal_akhir'], 0, ',', '.') ?></div></div>
                            <div class="col"><div class="l">Dibayar</div><div class="v" style="color:var(--success);"><?= number_format($t['nominal_dibayar'], 0, ',', '.') ?></div></div>
                            <div class="col"><div class="l">Sisa</div><div class="v" style="color:var(--danger);"><?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></div></div>
                            <?php if ($t['status_tagihan'] === 'lunas'): ?>
                                <span class="badge badge-success">Lunas</span>
                            <?php elseif ($t['status_tagihan'] === 'cicil'): ?>
                                <span class="badge badge-warning">Cicil</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tunggakan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="ta-summary">
                        <span>Ringkasan TA <?= esc($tahun) ?></span>
                        <span><?= $tahunSisa == 0 ? '<span class="badge badge-success">Lunas Semua</span>' : '<span style="color:var(--danger);">Rp ' . number_format($tahunSisa, 0, ',', '.') . ' belum lunas</span>' ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
