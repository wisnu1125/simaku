<?= $this->include('admin/layouts/header') ?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 18px; }
@media (min-width: 640px) { .stat-grid { gap: 16px; } }
.stat-card { padding: 16px; }
.stat-card .value { font-size: 15px; font-weight: 900; }
@media (min-width: 640px) { .stat-card .value { font-size: 19px; } }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }
.filter-bar { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; margin-bottom: 18px; }
.filter-bar .field { margin: 0; flex: 1; min-width: 140px; }
.filter-bar .btns { display: flex; gap: 8px; }
.row-list .p-card { padding: 14px; }
.row-list .p-card .top { display: flex; justify-content: space-between; }
.row-list .p-card .name { font-weight: 700; color: var(--ink); font-size: 13.5px; }
.row-list .p-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 3px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:10px; flex-wrap:wrap;">
    <div>
        <div class="page-title">Laporan Pembayaran</div>
        <div class="page-subtitle"><a href="<?= base_url('admin/laporan') ?>" style="color:var(--brand); text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Semua Laporan</a></div>
    </div>
    <a href="<?= base_url('admin/laporan/export-pembayaran?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-secondary"><i class="fa-solid fa-file-excel" style="color:var(--success);"></i> Ekspor Excel</a>
</div>

<form method="GET" class="filter-bar card card-pad" style="margin-top:16px;">
    <div class="field"><label>Dari Tanggal</label><input type="date" class="input" name="tanggal_mulai" value="<?= esc($tanggal_mulai) ?>"></div>
    <div class="field"><label>Sampai Tanggal</label><input type="date" class="input" name="tanggal_selesai" value="<?= esc($tanggal_selesai) ?>"></div>
    <div class="field">
        <label>Tahun Ajaran</label>
        <select class="input" name="id_tahun_ajaran">
            <option value="">Semua</option>
            <?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>" <?= $id_tahun_ajaran == $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="btns"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Terapkan</button></div>
</form>

<div class="stat-grid">
    <div class="card stat-card"><div class="value" style="color:var(--success);">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></div><div class="label">Total Pembayaran</div></div>
    <div class="card stat-card"><div class="value">Rp <?= number_format($total_tunai, 0, ',', '.') ?></div><div class="label">Tunai</div></div>
    <div class="card stat-card"><div class="value">Rp <?= number_format($total_transfer, 0, ',', '.') ?></div><div class="label">Transfer</div></div>
</div>

<div class="card" style="overflow:hidden;">
    <?php if (empty($pembayaran)): ?>
        <div class="empty-state"><i class="fa-solid fa-receipt"></i><p>Tidak ada pembayaran pada rentang tanggal ini.</p></div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Waktu</th><th>Kwitansi</th><th>Siswa</th><th>Kelas</th><th>Tagihan</th><th style="text-align:right;">Nominal</th><th>Metode</th><th>Petugas</th></tr></thead>
            <tbody>
                <?php foreach ($pembayaran as $p): ?>
                <tr>
                    <td style="white-space:nowrap; font-size:12.5px;"><?= date('d/m/Y H:i', strtotime($p['tanggal_bayar'])) ?></td>
                    <td class="mono" style="font-size:12px;"><?= esc($p['nomor_kwitansi']) ?></td>
                    <td style="font-weight:700; color:var(--ink);"><?= esc($p['nama_siswa']) ?><div style="font-size:11px; color:var(--muted); font-weight:400;"><?= esc($p['nis']) ?></div></td>
                    <td><?= esc($p['nama_kelas'] ?? '-') ?></td>
                    <td><?= esc($p['nama_tagihan']) ?></td>
                    <td style="text-align:right; font-weight:700; font-family:'Roboto Mono',monospace;">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></td>
                    <td><?= $p['metode_pembayaran'] === 'tunai' ? '<span class="badge badge-success">Tunai</span>' : '<span class="badge badge-info">Transfer</span>' ?></td>
                    <td style="font-size:12.5px;"><?= esc($p['nama_petugas'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="row-list" style="display:none; padding:10px;">
        <?php foreach ($pembayaran as $p): ?>
        <div class="card p-card">
            <div class="top"><span class="name"><?= esc($p['nama_siswa']) ?></span><span class="mono" style="font-weight:700;">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></span></div>
            <div class="meta"><?= esc($p['nama_tagihan']) ?> · <?= date('d/m/Y H:i', strtotime($p['tanggal_bayar'])) ?></div>
            <div style="margin-top:6px;"><?= $p['metode_pembayaran'] === 'tunai' ? '<span class="badge badge-success">Tunai</span>' : '<span class="badge badge-info">Transfer</span>' ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>@media (max-width: 860px) { .data-table { display: none; } .row-list { display: flex !important; flex-direction: column; gap: 10px; } }</style>

<?= $this->include('admin/layouts/footer') ?>
