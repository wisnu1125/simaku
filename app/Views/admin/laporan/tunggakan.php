<?= $this->include('admin/layouts/header') ?>

<style>
.filter-bar { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; margin-bottom: 18px; }
.filter-bar .field { margin: 0; flex: 1; min-width: 160px; }
.total-banner { background: linear-gradient(135deg, var(--danger), #b91c1c); color: #fff; border-radius: var(--r-lg); padding: 20px 24px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.total-banner .label { font-size: 12px; font-weight: 600; opacity: .9; }
.total-banner .value { font-size: 24px; font-weight: 900; }

.siswa-tunggakan-card { overflow: hidden; margin-bottom: 12px; }
.siswa-tunggakan-header { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; cursor: pointer; }
.siswa-tunggakan-header .name { font-size: 13.5px; font-weight: 800; color: var(--ink); }
.siswa-tunggakan-header .meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.siswa-tunggakan-header .amount { font-size: 15px; font-weight: 900; color: var(--danger); font-family: 'Roboto Mono', monospace; }
.siswa-tunggakan-detail { display: none; border-top: 1px solid var(--border-soft); }
.siswa-tunggakan-detail.open { display: block; }
.td-row { display: flex; justify-content: space-between; padding: 10px 18px; border-bottom: 1px solid var(--border-soft); font-size: 12.5px; }
.td-row:last-child { border-bottom: none; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:10px; flex-wrap:wrap;">
    <div>
        <div class="page-title">Laporan Tunggakan</div>
        <div class="page-subtitle"><a href="<?= base_url('admin/laporan') ?>" style="color:var(--brand); text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Semua Laporan</a></div>
    </div>
    <a href="<?= base_url('admin/laporan/export-tunggakan?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-secondary"><i class="fa-solid fa-file-excel" style="color:var(--success);"></i> Ekspor Excel</a>
</div>

<form method="GET" class="filter-bar card card-pad" style="margin-top:16px;">
    <div class="field">
        <label>Tahun Ajaran</label>
        <select class="input" name="id_tahun_ajaran">
            <option value="">Semua</option>
            <?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>" <?= $id_tahun_ajaran == $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label>Kelas</label>
        <select class="input" name="id_kelas">
            <option value="">Semua</option>
            <?php foreach ($kelas as $k): ?><option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div><button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Terapkan</button></div>
</form>

<div class="total-banner">
    <span class="label"><i class="fa-solid fa-triangle-exclamation"></i> TOTAL TUNGGAKAN KESELURUHAN</span>
    <span class="value">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></span>
</div>

<?php if (empty($tunggakan_by_siswa)): ?>
    <div class="card empty-state"><i class="fa-solid fa-face-smile"></i><p>Tidak ada tunggakan pada filter ini 🎉</p></div>
<?php else: ?>
    <?php foreach ($tunggakan_by_siswa as $i => $siswa): ?>
    <div class="card siswa-tunggakan-card">
        <div class="siswa-tunggakan-header" onclick="document.getElementById('td_<?= $i ?>').classList.toggle('open')">
            <div>
                <div class="name"><?= esc($siswa['nama_siswa']) ?></div>
                <div class="meta">NIS <?= esc($siswa['nis']) ?> · <?= esc($siswa['nama_kelas'] ?? '-') ?><?= $siswa['telp_wali'] ? ' · ' . esc($siswa['telp_wali']) : '' ?></div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="amount">Rp <?= number_format($siswa['total_tunggakan'], 0, ',', '.') ?></span>
                <i class="fa-solid fa-chevron-down" style="color:var(--faint);"></i>
            </div>
        </div>
        <div class="siswa-tunggakan-detail" id="td_<?= $i ?>">
            <?php foreach ($siswa['tagihan'] as $t): ?>
            <div class="td-row">
                <span><?= esc($t['nama_tagihan']) ?><?= $t['bulan_tagihan'] ? ' (Bln ' . $t['bulan_tagihan'] . ')' : '' ?> · <?= esc($t['nama_tahun_ajaran']) ?></span>
                <span style="font-weight:700; color:var(--danger); font-family:'Roboto Mono',monospace;">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->include('admin/layouts/footer') ?>
