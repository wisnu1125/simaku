<?= $this->include('admin/layouts/header') ?>

<style>
.jt-toolbar { margin-bottom:14px; position:relative; max-width:360px; }
.jt-toolbar i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--faint); }
.jt-toolbar input { padding-left:38px; }
.kode-badge { font-family:'Roboto Mono', monospace; background:var(--border-soft); padding:3px 8px; border-radius:6px; font-size:12px; color:var(--brand-darker); }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; gap:10px;">
    <div class="page-title">Jenis Tagihan</div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah</span></button>
</div>

<div class="jt-toolbar"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari nama atau kode…"></div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Nama Tagihan</th><th>Kode</th><th>Tipe</th><th>Grup</th><th>Status</th><th style="text-align:right;">Aksi</th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:10px;"></div>
    <div id="emptyState" style="display:none;"></div>
</div>

<!-- ===================== MODAL: Tambah / Edit ===================== -->
<div class="overlay" id="jtModal_overlay" onclick="closeModal('jtModal')"></div>
<div class="modal" id="jtModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3 id="jtModalTitle">Tambah Jenis Tagihan</h3><button type="button" class="modal-close" onclick="closeModal('jtModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="jtForm" action="<?= base_url('admin/jenis-tagihan/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="modal-body">
            <div class="field">
                <label class="required">Nama Tagihan</label>
                <input class="input" name="nama_tagihan" id="f_nama" placeholder="Contoh: SPP Juli" value="<?= esc(old('nama_tagihan', '')) ?>" required>
            </div>
            <div class="field-row">
                <div class="field">
                    <label class="required">Kode Tagihan</label>
                    <input class="input <?= isset($errors['kode_tagihan']) ? 'is-invalid' : '' ?>" name="kode_tagihan" id="f_kode" placeholder="Contoh: SPP-JUL" style="text-transform:uppercase;" value="<?= esc(old('kode_tagihan', '')) ?>" required>
                    <?php if (isset($errors['kode_tagihan'])): ?><div class="field-error"><?= $errors['kode_tagihan'] ?></div><?php endif; ?>
                </div>
                <div class="field">
                    <label class="required">Tipe</label>
                    <select class="input" name="tipe_tagihan" id="f_tipe" required>
                        <option value="bulanan">Bulanan (auto 12x)</option>
                        <option value="tahunan">Tahunan</option>
                        <option value="sekali">Sekali Bayar</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Grup Tagihan <span style="font-weight:400; color:var(--muted); text-transform:none;">(opsional, buat pengelompokan)</span></label>
                <input class="input" name="grup_tagihan" id="f_grup" placeholder="Contoh: SPP" value="<?= esc(old('grup_tagihan', '')) ?>">
            </div>
            <div class="field">
                <label>Keterangan</label>
                <textarea class="input" name="keterangan" id="f_ket" rows="2"><?= esc(old('keterangan', '')) ?></textarea>
            </div>
            <div class="field" id="f_status_wrap" style="display:none;">
                <label class="required">Status</label>
                <select class="input" name="status" id="f_status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('jtModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const HAS_ERRORS = <?= !empty($errors) ? 'true' : 'false' ?>;
const OLD_EDITING_ID = <?= json_encode(old('editing_id', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const DATA = <?= json_encode($jenis_tagihan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
let filtered = DATA.slice();

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function tipeBadge(t) {
    if (t === 'bulanan') return '<span class="badge badge-info"><i class="fa-regular fa-calendar"></i> Bulanan</span>';
    if (t === 'tahunan') return '<span class="badge badge-brand"><i class="fa-solid fa-calendar-days"></i> Tahunan</span>';
    return '<span class="badge badge-neutral">Sekali Bayar</span>';
}
function statusBadge(s) { return s === 'aktif' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>'; }

function render() {
    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (filtered.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-tags"></i><p>Tidak ada jenis tagihan yang cocok.</p></div>';
        return;
    }
    emptyState.style.display = 'none';

    tbody.innerHTML = filtered.map(jt => `
        <tr>
            <td style="font-weight:700; color:var(--ink);">${esc(jt.nama_tagihan)}</td>
            <td><span class="kode-badge">${esc(jt.kode_tagihan)}</span></td>
            <td>${tipeBadge(jt.tipe_tagihan)}</td>
            <td>${jt.grup_tagihan ? esc(jt.grup_tagihan) : '<span style="color:var(--faint);">—</span>'}</td>
            <td>${statusBadge(jt.status)}</td>
            <td style="text-align:right;">
                <button class="icon-action" title="Edit" onclick="openEditModal(${jt.id_jenis_tagihan})"><i class="fa-solid fa-pencil"></i></button>
                <form method="POST" action="${BASE_URL}/admin/jenis-tagihan/delete/${jt.id_jenis_tagihan}" style="display:inline;" onsubmit="return confirm('Hapus ' + ${JSON.stringify(jt.nama_tagihan)} + '?')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
            </td>
        </tr>
    `).join('');

    cardList.innerHTML = filtered.map(jt => `
        <div class="card" style="padding:14px; display:flex; align-items:center; gap:12px;">
            <div style="flex:1; min-width:0;" onclick="openEditModal(${jt.id_jenis_tagihan})">
                <div style="font-weight:700; color:var(--ink); font-size:13.5px;">${esc(jt.nama_tagihan)}</div>
                <div style="margin-top:5px; display:flex; gap:6px; flex-wrap:wrap; align-items:center;"><span class="kode-badge">${esc(jt.kode_tagihan)}</span>${tipeBadge(jt.tipe_tagihan)}${statusBadge(jt.status)}</div>
            </div>
            <button class="icon-action" title="Edit" onclick="openEditModal(${jt.id_jenis_tagihan})"><i class="fa-solid fa-pencil"></i></button>
        </div>
    `).join('');
}

document.getElementById('q').addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    filtered = DATA.filter(jt => !q || jt.nama_tagihan.toLowerCase().includes(q) || jt.kode_tagihan.toLowerCase().includes(q));
    render();
});

function resetForm() { document.getElementById('jtForm').reset(); document.getElementById('f_editing_id').value = ''; }

function openCreateModal() {
    resetForm();
    document.getElementById('jtModalTitle').textContent = 'Tambah Jenis Tagihan';
    document.getElementById('jtForm').action = BASE_URL + '/admin/jenis-tagihan/store';
    document.getElementById('f_status_wrap').style.display = 'none';
    document.getElementById('f_status').removeAttribute('required');
    openModal('jtModal');
}

function openEditModal(id) {
    const jt = DATA.find(x => x.id_jenis_tagihan == id);
    if (!jt) return;
    resetForm();
    document.getElementById('f_editing_id').value = id;
    document.getElementById('jtModalTitle').textContent = 'Edit — ' + jt.nama_tagihan;
    document.getElementById('jtForm').action = BASE_URL + '/admin/jenis-tagihan/update/' + id;
    document.getElementById('f_nama').value = jt.nama_tagihan;
    document.getElementById('f_kode').value = jt.kode_tagihan;
    document.getElementById('f_tipe').value = jt.tipe_tagihan;
    document.getElementById('f_grup').value = jt.grup_tagihan || '';
    document.getElementById('f_ket').value = jt.keterangan || '';
    document.getElementById('f_status_wrap').style.display = 'block';
    document.getElementById('f_status').setAttribute('required', 'required');
    document.getElementById('f_status').value = jt.status;
    openModal('jtModal');
}

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

render();
if (HAS_ERRORS) {
    if (OLD_EDITING_ID) {
        document.getElementById('jtModalTitle').textContent = 'Edit Jenis Tagihan';
        document.getElementById('jtForm').action = BASE_URL + '/admin/jenis-tagihan/update/' + OLD_EDITING_ID;
        document.getElementById('f_editing_id').value = OLD_EDITING_ID;
        document.getElementById('f_status_wrap').style.display = 'block';
        document.getElementById('f_status').setAttribute('required', 'required');
    } else {
        document.getElementById('jtForm').action = BASE_URL + '/admin/jenis-tagihan/store';
    }
    openModal('jtModal');
} else if (location.hash) {
    handleHash();
}
</script>

<?= $this->include('admin/layouts/footer') ?>
