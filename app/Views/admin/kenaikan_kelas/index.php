<?= $this->include('admin/layouts/header') ?>

<style>
.filter-row { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
@media (min-width: 640px) { .filter-row { flex-direction: row; } }
.filter-row select { width: 100%; }
@media (min-width: 640px) { .filter-row select { width: 260px; } }

.kelas-info { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 16px 20px; background: var(--brand-bg); border: 1px solid var(--brand-light); border-radius: var(--r-md); margin-bottom: 16px; flex-wrap: wrap; }
.kelas-info .name { font-size: 15px; font-weight: 800; color: var(--brand-darker); }
.kelas-info .sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

.siswa-row { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--border-soft); }
.siswa-row:last-child { border-bottom: none; }
.siswa-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--brand); flex-shrink: 0; }
.siswa-row .avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--border-soft); color: var(--muted); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12.5px; flex-shrink: 0; }
.siswa-row .name { font-size: 13px; font-weight: 700; color: var(--ink); }
.siswa-row .meta { font-size: 11.5px; color: var(--muted); }

.action-bar { position: sticky; bottom: 16px; margin-top: 16px; background: var(--ink); color: #fff; padding: 14px 18px; border-radius: var(--r-lg); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; box-shadow: var(--shadow-lg); }
.action-bar .count { font-size: 13px; font-weight: 700; }
.action-bar .btns { display: flex; gap: 8px; flex-wrap: wrap; }
.action-bar .btn-white { background: #fff; color: var(--ink); }
.action-bar .btn-white:hover { background: var(--border-soft); }
.action-bar .btn-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.3); }
.action-bar .btn-outline:hover { background: rgba(255,255,255,.1); }
</style>

<div class="page-title" style="margin-bottom:4px;">Kenaikan Kelas</div>
<div class="page-subtitle" style="margin-bottom:18px;">Pilih kelas untuk naikkan atau luluskan siswanya sekaligus.</div>

<form method="GET" class="filter-row" id="filterForm">
    <select class="input" name="id_tahun_ajaran" onchange="this.form.submit()">
        <option value="">Semua Tahun Ajaran</option>
        <?php foreach ($tahun_ajaran as $ta): ?>
            <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= $id_tahun_ajaran == $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
    </select>
    <select class="input" name="id_kelas" onchange="this.form.submit()">
        <option value="">— Pilih Kelas —</option>
        <?php foreach ($kelas as $k): ?>
            <?php if (!$id_tahun_ajaran || $k['id_tahun_ajaran'] == $id_tahun_ajaran): ?>
            <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran'] ?? '-') ?>)</option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($kelas_detail)): ?>

    <div class="kelas-info">
        <div>
            <div class="name"><i class="fa-solid fa-chalkboard"></i> <?= esc($kelas_detail['nama_kelas']) ?></div>
            <div class="sub"><?= esc($kelas_detail['nama_tahun_ajaran']) ?> · <?= count($siswa) ?> siswa aktif</div>
        </div>
    </div>

    <?php if (empty($siswa)): ?>
        <div class="card empty-state"><i class="fa-solid fa-user-slash"></i><p>Tidak ada siswa aktif di kelas ini.</p></div>
    <?php else: ?>
        <div class="card" style="overflow:hidden;">
            <div class="siswa-row" style="background:var(--border-soft); font-size:12.5px; font-weight:700; color:var(--muted);">
                <input type="checkbox" id="selectAll" onchange="document.querySelectorAll('.siswa-check').forEach(cb=>{cb.checked=this.checked}); updateActionBar();">
                <span>PILIH SEMUA</span>
            </div>
            <?php foreach ($siswa as $s): ?>
            <div class="siswa-row">
                <input type="checkbox" class="siswa-check" value="<?= $s['id_siswa'] ?>" onchange="updateActionBar()">
                <div class="avatar"><?= esc(strtoupper(substr($s['nama_lengkap'], 0, 1))) ?></div>
                <div>
                    <div class="name"><?= esc($s['nama_lengkap']) ?></div>
                    <div class="meta">NIS <?= esc($s['nis']) ?> · <?= $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="action-bar" id="actionBar" style="display:none;">
            <span class="count"><span id="selectedCount">0</span> siswa dipilih</span>
            <div class="btns">
                <button type="button" class="btn btn-white" onclick="openNaikModal()"><i class="fa-solid fa-arrow-up-right-dots"></i> Naikkan Kelas</button>
                <button type="button" class="btn btn-outline" onclick="openLulusModal()"><i class="fa-solid fa-graduation-cap"></i> Luluskan</button>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="card empty-state"><i class="fa-solid fa-hand-pointer"></i><p>Pilih kelas di atas untuk mulai memproses kenaikan kelas atau kelulusan.</p></div>
<?php endif; ?>

<!-- ===================== MODAL: Naik Kelas ===================== -->
<div class="inline-panel" id="naikPanel">
    <div class="inline-panel-header"><h3>Naikkan Kelas</h3><button type="button" class="inline-panel-close" onclick="closePanel('naikPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="<?= base_url('admin/kenaikan-kelas/proses') ?>" method="POST" id="formNaik">
        <input type="hidden" name="id_kelas_asal" value="<?= $kelas_detail['id_kelas'] ?? '' ?>">
        <div id="naikSiswaIds"></div>
        <div class="inline-panel-body">
            <p style="font-size:13px; color:var(--body); margin-bottom:16px;"><strong id="naikCount">0</strong> siswa akan dipindahkan dari <strong><?= esc($kelas_detail['nama_kelas'] ?? '') ?></strong> ke kelas tujuan berikut:</p>
            <div class="field">
                <label class="required">Kelas Tujuan</label>
                <select class="input" name="id_kelas_tujuan" required>
                    <option value="">— Pilih —</option>
                    <?php foreach ($kelas as $k): ?>
                        <?php if (empty($kelas_detail) || $k['id_kelas'] != $kelas_detail['id_kelas']): ?>
                        <option value="<?= $k['id_kelas'] ?>"><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran'] ?? '-') ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('naikPanel')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Proses Kenaikan Kelas</button>
        </div>
    </form>
</div>

<!-- ===================== MODAL: Kelulusan ===================== -->
<div class="inline-panel" id="lulusPanel">
    <div class="inline-panel-header"><h3>Luluskan Siswa</h3><button type="button" class="inline-panel-close" onclick="closePanel('lulusPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="<?= base_url('admin/kenaikan-kelas/proses-kelulusan') ?>" method="POST" id="formLulus">
        <input type="hidden" name="id_kelas" value="<?= $kelas_detail['id_kelas'] ?? '' ?>">
        <div id="lulusSiswaIds"></div>
        <div class="inline-panel-body">
            <div class="hint-box" style="background:var(--warning-bg); border:1px solid var(--warning-border); border-radius:var(--r-md); padding:12px 14px; font-size:12.5px; color:#78350f; display:flex; gap:10px;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-top:1px;"></i>
                <span><strong id="lulusCount">0</strong> siswa akan ditandai <strong>lulus</strong> dan dikeluarkan dari kelas <strong><?= esc($kelas_detail['nama_kelas'] ?? '') ?></strong>. Tindakan ini tidak membatalkan tagihan yang sudah ada.</span>
            </div>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('lulusPanel')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-graduation-cap"></i> Proses Kelulusan</button>
        </div>
    </form>
</div>

<script>
function updateActionBar() {
    const checked = document.querySelectorAll('.siswa-check:checked');
    document.getElementById('actionBar').style.display = checked.length > 0 ? 'flex' : 'none';
    document.getElementById('selectedCount').textContent = checked.length;
}

function getSelectedIds() { return Array.from(document.querySelectorAll('.siswa-check:checked')).map(cb => cb.value); }

function buildHiddenInputs(containerId, ids) {
    const container = document.getElementById(containerId);
    container.innerHTML = ids.map(id => `<input type="hidden" name="siswa_ids[]" value="${id}">`).join('');
}

function openNaikModal() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    document.getElementById('naikCount').textContent = ids.length;
    buildHiddenInputs('naikSiswaIds', ids);
    openPanel('naikPanel');
}
function openLulusModal() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    document.getElementById('lulusCount').textContent = ids.length;
    buildHiddenInputs('lulusSiswaIds', ids);
    openPanel('lulusPanel');
}
</script>

<?= $this->include('admin/layouts/footer') ?>
