<?= $this->include('admin/layouts/header') ?>

<style>
.ta-card { padding: 18px 20px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.ta-card .body { flex: 1; min-width: 200px; }
.ta-card .name { font-size: 15px; font-weight: 800; color: var(--ink); }
.ta-card .range { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.ta-card .actions { display: flex; gap: 8px; flex-wrap: wrap; }
.ta-list { display: flex; flex-direction: column; gap: 12px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; gap:10px;">
    <div class="page-title">Tahun Ajaran</div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah</span></button>
</div>

<div class="ta-list" id="taList"></div>
<div id="emptyState" style="display:none;"></div>

<!-- ===================== PANEL: Tambah / Edit (inline) ===================== -->
<div class="inline-panel" id="taPanel">
    <div class="inline-panel-header"><h3 id="taModalTitle">Tambah Tahun Ajaran</h3><button type="button" class="inline-panel-close" onclick="closePanel('taPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="taForm" action="<?= base_url('admin/tahun-ajaran/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="inline-panel-body">
            <div class="field">
                <label class="required">Nama Tahun Ajaran</label>
                <input class="input <?= isset($errors['nama_tahun_ajaran']) ? 'is-invalid' : '' ?>" name="nama_tahun_ajaran" id="f_nama" placeholder="Contoh: TA 2026/2027" value="<?= esc(old('nama_tahun_ajaran', '')) ?>" required>
                <?php if (isset($errors['nama_tahun_ajaran'])): ?><div class="field-error"><?= $errors['nama_tahun_ajaran'] ?></div><?php endif; ?>
            </div>
            <div class="field-row">
                <div class="field">
                    <label class="required">Tanggal Mulai</label>
                    <input type="date" class="input" name="tanggal_mulai" id="f_mulai" value="<?= esc(old('tanggal_mulai', '')) ?>" required>
                </div>
                <div class="field">
                    <label class="required">Tanggal Selesai</label>
                    <input type="date" class="input" name="tanggal_selesai" id="f_selesai" value="<?= esc(old('tanggal_selesai', '')) ?>" required>
                </div>
            </div>
            <label style="display:flex; align-items:flex-start; gap:10px; padding:14px; background:#fff; border:1.5px solid var(--brand-light); border-radius:var(--r-md); cursor:pointer;">
                <input type="checkbox" name="set_aktif" id="f_set_aktif" value="1" style="width:18px; height:18px; accent-color:var(--brand); margin-top:1px;">
                <span style="font-size:13px; color:var(--brand-darker);"><strong id="f_set_aktif_label">Jadikan tahun ajaran aktif</strong><br><span style="font-size:11.5px;">Tahun ajaran aktif lain otomatis ditandai selesai.</span></span>
            </label>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('taPanel')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const HAS_ERRORS = <?= !empty($errors) ? 'true' : 'false' ?>;
const OLD_EDITING_ID = <?= json_encode(old('editing_id', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const DATA = <?= json_encode($tahun_ajaran, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmtDate(d) { const dt = new Date(d); return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); }
function statusBadge(s) {
    if (s === 'aktif') return '<span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Aktif</span>';
    if (s === 'selesai') return '<span class="badge badge-neutral">Selesai</span>';
    return '<span class="badge badge-warning">Belum Aktif</span>';
}

function render() {
    const list = document.getElementById('taList');
    const emptyState = document.getElementById('emptyState');
    if (DATA.length === 0) {
        list.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-calendar-xmark"></i><p>Belum ada tahun ajaran.</p></div>';
        return;
    }
    emptyState.style.display = 'none';
    list.innerHTML = DATA.map(t => `
        <div class="card ta-card">
            <div class="body">
                <div class="name">${esc(t.nama_tahun_ajaran)} ${statusBadge(t.status)}</div>
                <div class="range">${fmtDate(t.tanggal_mulai)} — ${fmtDate(t.tanggal_selesai)}</div>
            </div>
            <div class="actions">
                ${t.status !== 'aktif' ? `<form method="POST" action="${BASE_URL}/admin/tahun-ajaran/activate/${t.id_tahun_ajaran}" onsubmit="return confirm('Aktifkan ' + ${JSON.stringify(t.nama_tahun_ajaran)} + '? Tahun ajaran aktif lain akan ditandai selesai.')"><button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-circle-check"></i> Aktifkan</button></form>` : ''}
                ${t.status === 'aktif' ? `<form method="POST" action="${BASE_URL}/admin/tahun-ajaran/close/${t.id_tahun_ajaran}" onsubmit="return confirm('Tutup ' + ${JSON.stringify(t.nama_tahun_ajaran)} + '?')"><button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-lock"></i> Tutup</button></form>` : ''}
                <button class="icon-action" title="Edit" onclick="openEditModal(${t.id_tahun_ajaran})"><i class="fa-solid fa-pencil"></i></button>
                <form method="POST" action="${BASE_URL}/admin/tahun-ajaran/delete/${t.id_tahun_ajaran}" onsubmit="return confirm('Hapus ' + ${JSON.stringify(t.nama_tahun_ajaran)} + '? Hanya bisa dihapus kalau belum ada kelas terkait.')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
            </div>
        </div>
    `).join('');
}

function resetForm() { document.getElementById('taForm').reset(); document.getElementById('f_editing_id').value = ''; }

function openCreateModal() {
    resetForm();
    document.getElementById('taModalTitle').textContent = 'Tambah Tahun Ajaran';
    document.getElementById('taForm').action = BASE_URL + '/admin/tahun-ajaran/store';
    document.getElementById('f_set_aktif_label').textContent = 'Jadikan tahun ajaran aktif';
    openPanel('taPanel');
}

function openEditModal(id) {
    const t = DATA.find(x => x.id_tahun_ajaran == id);
    if (!t) return;
    resetForm();
    document.getElementById('f_editing_id').value = id;
    document.getElementById('taModalTitle').textContent = 'Edit — ' + t.nama_tahun_ajaran;
    document.getElementById('taForm').action = BASE_URL + '/admin/tahun-ajaran/update/' + id;
    document.getElementById('f_nama').value = t.nama_tahun_ajaran;
    document.getElementById('f_mulai').value = t.tanggal_mulai;
    document.getElementById('f_selesai').value = t.tanggal_selesai;
    document.getElementById('f_set_aktif_label').textContent = t.status === 'aktif' ? 'Tetap aktifkan tahun ajaran ini' : 'Jadikan tahun ajaran aktif';
    document.getElementById('f_set_aktif').checked = t.status === 'aktif';
    openPanel('taPanel');
}

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

render();
if (HAS_ERRORS) {
    if (OLD_EDITING_ID) {
        document.getElementById('taModalTitle').textContent = 'Edit Tahun Ajaran';
        document.getElementById('taForm').action = BASE_URL + '/admin/tahun-ajaran/update/' + OLD_EDITING_ID;
        document.getElementById('f_editing_id').value = OLD_EDITING_ID;
    } else {
        document.getElementById('taForm').action = BASE_URL + '/admin/tahun-ajaran/store';
    }
    openPanel('taPanel');
} else if (location.hash) {
    handleHash();
}
</script>

<?= $this->include('admin/layouts/footer') ?>
