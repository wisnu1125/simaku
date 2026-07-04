<?= $this->include('admin/layouts/header') ?>

<style>
.kelas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 14px; }
.kelas-card { padding: 18px; }
.kelas-card .top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 10px; }
.kelas-card .name { font-size: 15px; font-weight: 800; color: var(--ink); }
.kelas-card .ta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.kelas-card .count { font-size: 12.5px; color: var(--body); display: flex; align-items: center; gap: 6px; margin-top: 10px; }
.kelas-card .actions { display: flex; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
.toolbar { margin-bottom: 16px; max-width: 280px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; gap:10px;">
    <div class="page-title">Kelas</div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah Kelas</span></button>
</div>

<div class="toolbar">
    <select class="input" id="fTA">
        <option value="">Semua Tahun Ajaran</option>
        <?php foreach ($tahun_ajaran as $ta): ?>
            <option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?><?= $ta['status'] === 'aktif' ? ' (Aktif)' : '' ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="kelas-grid" id="kelasGrid"></div>
<div id="emptyState" style="display:none;"></div>

<!-- ===================== MODAL: Tambah / Edit ===================== -->
<div class="overlay" id="kelasModal_overlay" onclick="closeModal('kelasModal')"></div>
<div class="modal" id="kelasModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3 id="kelasModalTitle">Tambah Kelas</h3><button type="button" class="modal-close" onclick="closeModal('kelasModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="kelasForm" action="<?= base_url('admin/kelas/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="modal-body">
            <div class="field">
                <label class="required">Nama Kelas</label>
                <input class="input" name="nama_kelas" id="f_nama" placeholder="Contoh: 7 Bung Tomo" value="<?= esc(old('nama_kelas', '')) ?>" required>
            </div>
            <div class="field-row">
                <div class="field">
                    <label class="required">Tingkat</label>
                    <input type="number" class="input" name="tingkat" id="f_tingkat" min="1" max="9" placeholder="1-9" value="<?= esc(old('tingkat', '')) ?>" required>
                </div>
                <div class="field">
                    <label class="required">Tahun Ajaran</label>
                    <select class="input" name="id_tahun_ajaran" id="f_ta" required>
                        <option value="">— Pilih —</option>
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('kelasModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const HAS_ERRORS = <?= !empty($errors) ? 'true' : 'false' ?>;
const OLD_EDITING_ID = <?= json_encode(old('editing_id', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const DATA = <?= json_encode($kelas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const TAHUN_AJARAN = <?= json_encode($tahun_ajaran, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
let fTA = '';

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }

function render() {
    const grid = document.getElementById('kelasGrid');
    const emptyState = document.getElementById('emptyState');
    const filtered = fTA ? DATA.filter(k => k.id_tahun_ajaran == fTA) : DATA;

    if (filtered.length === 0) {
        grid.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-chalkboard"></i><p>' + (DATA.length === 0 ? 'Belum ada kelas.' : 'Tidak ada kelas di tahun ajaran ini.') + '</p></div>';
        return;
    }
    emptyState.style.display = 'none';
    grid.innerHTML = filtered.map(k => `
        <div class="card kelas-card">
            <div class="top">
                <div>
                    <div class="name">${esc(k.nama_kelas)}</div>
                    <div class="ta">Tingkat ${k.tingkat} · ${esc(k.nama_tahun_ajaran || '-')}</div>
                </div>
                ${k.status_tahun_ajaran === 'aktif' ? '<span class="badge badge-success">TA Aktif</span>' : ''}
            </div>
            <div class="count"><i class="fa-solid fa-user-graduate" style="color:var(--faint);"></i> ${k.jumlah_siswa} siswa</div>
            <div class="actions">
                <button class="btn btn-secondary btn-sm btn-block" onclick="openEditModal(${k.id_kelas})"><i class="fa-solid fa-pencil"></i> Edit</button>
                <form method="POST" action="${BASE_URL}/admin/kelas/delete/${k.id_kelas}" onsubmit="return confirm('Hapus kelas ' + ${JSON.stringify(k.nama_kelas)} + '?')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
            </div>
        </div>
    `).join('');
}

function resetForm() { document.getElementById('kelasForm').reset(); document.getElementById('f_editing_id').value = ''; }

function openCreateModal() {
    resetForm();
    document.getElementById('kelasModalTitle').textContent = 'Tambah Kelas';
    document.getElementById('kelasForm').action = BASE_URL + '/admin/kelas/store';
    document.getElementById('f_ta').value = fTA || (TAHUN_AJARAN.find(t => t.status === 'aktif')?.id_tahun_ajaran ?? '');
    openModal('kelasModal');
}

function openEditModal(id) {
    const k = DATA.find(x => x.id_kelas == id);
    if (!k) return;
    resetForm();
    document.getElementById('f_editing_id').value = id;
    document.getElementById('kelasModalTitle').textContent = 'Edit — ' + k.nama_kelas;
    document.getElementById('kelasForm').action = BASE_URL + '/admin/kelas/update/' + id;
    document.getElementById('f_nama').value = k.nama_kelas;
    document.getElementById('f_tingkat').value = k.tingkat;
    document.getElementById('f_ta').value = k.id_tahun_ajaran;
    openModal('kelasModal');
}

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

document.getElementById('fTA').addEventListener('change', function () { fTA = this.value; render(); });

// Default: tampilkan kelas dari tahun ajaran yang sedang aktif dulu (kalau ada).
// Pengguna tetap bisa ganti ke "Semua Tahun Ajaran" atau tahun lain lewat dropdown.
const activeTA = TAHUN_AJARAN.find(t => t.status === 'aktif');
if (activeTA) {
    fTA = String(activeTA.id_tahun_ajaran);
    document.getElementById('fTA').value = fTA;
}

render();
if (HAS_ERRORS) {
    if (OLD_EDITING_ID) {
        document.getElementById('kelasModalTitle').textContent = 'Edit Kelas';
        document.getElementById('kelasForm').action = BASE_URL + '/admin/kelas/update/' + OLD_EDITING_ID;
        document.getElementById('f_editing_id').value = OLD_EDITING_ID;
    } else {
        document.getElementById('kelasForm').action = BASE_URL + '/admin/kelas/store';
    }
    openModal('kelasModal');
} else if (location.hash) {
    handleHash();
}
</script>

<?= $this->include('admin/layouts/footer') ?>