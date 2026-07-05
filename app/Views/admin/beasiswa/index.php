<?= $this->include('admin/layouts/header') ?>

<style>
.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 768px) { .toolbar { flex-direction: row; } }
.toolbar .search-wrap { position: relative; flex: 1; }
.toolbar .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar .search-wrap input { padding-left: 38px; }
.toolbar select { width: 100%; }
@media (min-width: 768px) { .toolbar select { width: 200px; flex-shrink: 0; } }
.pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-soft); font-size: 12.5px; color: var(--muted); }
.pager .btns { display: flex; gap: 6px; }
.bs-card { padding: 14px; }
.bs-card .top { display: flex; justify-content: space-between; gap: 8px; }
.bs-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.bs-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 3px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:10px;">
    <div>
        <div class="page-title">Beasiswa</div>
        <div class="page-subtitle" id="resultCount">Memuat…</div>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah</span></button>
</div>

<div class="toolbar">
    <div class="search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari nama, NIS, atau nama beasiswa…"></div>
    <select class="input" id="fTA"><option value="">Semua Tahun Ajaran</option><?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>" <?= (string) ($filter_tahun_ajaran ?? '') === (string) $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?><?= $ta['status'] === 'aktif' ? ' (Aktif)' : '' ?></option><?php endforeach; ?></select>
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Siswa</th><th>Beasiswa</th><th>Jenis Tagihan</th><th style="text-align:right;">Nilai</th><th>Status</th><th></th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:10px;"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<!-- ===================== MODAL: Tambah / Edit ===================== -->
<div class="inline-panel" id="bsPanel">
    <div class="inline-panel-header"><h3 id="bsModalTitle">Tambah Beasiswa</h3><button type="button" class="inline-panel-close" onclick="closePanel('bsPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="bsForm" action="<?= base_url('admin/beasiswa/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="inline-panel-body">

            <div class="field" id="f_mode_wrap">
                <label class="required">Mode</label>
                <div class="segmented">
                    <label><input type="radio" name="mode_beasiswa" value="single" id="f_mode_single" checked onchange="setBsMode('single')"> Satu Jenis Tagihan</label>
                    <label><input type="radio" name="mode_beasiswa" value="bulk" id="f_mode_bulk" onchange="setBsMode('bulk')"> Satu Grup Sekaligus</label>
                </div>
                <div class="field-hint">"Satu Grup" berguna untuk mis. memberi diskon SPP di semua 12 bulan sekaligus.</div>
            </div>

            <div class="field">
                <label class="required">Cari Siswa</label>
                <div class="search-box">
                    <input type="text" class="input" id="f_siswa_search" placeholder="Ketik NIS atau nama…" autocomplete="off">
                    <div class="search-results" id="f_siswa_results"></div>
                </div>
                <input type="hidden" name="id_siswa" id="f_id_siswa" required>
                <div id="f_selected_siswa" style="margin-top:8px;"></div>
            </div>

            <div class="field" id="f_jenis_wrap">
                <label class="required">Jenis Tagihan</label>
                <select class="input" name="id_jenis_tagihan" id="f_jenis">
                    <option value="">— Pilih —</option>
                    <?php foreach ($jenis_tagihan as $jt): ?>
                        <option value="<?= $jt['id_jenis_tagihan'] ?>"><?= esc($jt['nama_tagihan']) ?><?= $jt['grup_tagihan'] ? ' (' . esc($jt['grup_tagihan']) . ')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field" id="f_grup_wrap" style="display:none;">
                <label class="required">Grup Tagihan</label>
                <select class="input" name="selected_grup" id="f_grup">
                    <option value="">— Pilih —</option>
                    <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
                        <option value="<?= esc($grup) ?>"><?= esc($grup) ?> (<?= count($items) ?> item)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="required">Nama Beasiswa</label>
                    <input class="input" name="nama_beasiswa" id="f_nama" placeholder="Contoh: Yatim Piatu" value="<?= esc(old('nama_beasiswa', '')) ?>" required>
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

            <div class="field-row">
                <div class="field">
                    <label class="required">Tipe Potongan</label>
                    <div class="segmented">
                        <label><input type="radio" name="tipe_beasiswa" value="nominal" id="f_tipe_nominal" checked onchange="setBsTipe()"> Rp Nominal</label>
                        <label><input type="radio" name="tipe_beasiswa" value="persentase" id="f_tipe_persen" onchange="setBsTipe()"> % Persen</label>
                    </div>
                </div>
                <div class="field">
                    <label class="required" id="f_nilai_label">Nilai (Rp)</label>
                    <input type="number" class="input" name="nilai_beasiswa" id="f_nilai" min="0" step="1" value="<?= esc(old('nilai_beasiswa', '')) ?>" required>
                </div>
            </div>

            <div class="field" id="f_status_wrap" style="display:none;">
                <label class="required">Status</label>
                <select class="input" name="status" id="f_status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="field">
                <label>Keterangan</label>
                <textarea class="input" name="keterangan" id="f_ket" rows="2"><?= esc(old('keterangan', '')) ?></textarea>
            </div>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('bsPanel')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/beasiswa') ?>';
let page = 1;
const PER_PAGE = 15;
let q = '', fTA = '<?= esc($filter_tahun_ajaran ?? '') ?>';
let currentRows = [];
let fetchToken = 0;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function nilaiFmt(b) { return b.tipe_beasiswa === 'nominal' ? 'Rp ' + fmt(b.nilai_beasiswa) : fmt(b.nilai_beasiswa) + '%'; }
function statusBadge(s) { return s === 'aktif' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>'; }

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 5; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:60%;"></div><div class="skeleton-bar" style="width:35%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="padding:0;">' + html + '</td></tr>';
}

async function loadPage() {
    const myToken = ++fetchToken;
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';
    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (q) params.set('keyword', q);
    if (fTA) params.set('filter_tahun_ajaran', fTA);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) { document.getElementById('resultCount').textContent = 'Gagal memuat data.'; return; }
    if (myToken !== fetchToken) return;

    currentRows = data.rows;
    document.getElementById('resultCount').textContent = data.total + ' beasiswa';

    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (currentRows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-award"></i><p>Belum ada beasiswa yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';
        tbody.innerHTML = currentRows.map(b => `
            <tr>
                <td><div style="font-weight:700; color:var(--ink);">${esc(b.nama_siswa)}</div><div style="font-size:11.5px; color:var(--muted);">${esc(b.nis)}</div></td>
                <td>${esc(b.nama_beasiswa)}</td>
                <td>${esc(b.nama_tagihan || '-')}</td>
                <td style="text-align:right; font-weight:700; font-family:'Roboto Mono',monospace;">${nilaiFmt(b)}</td>
                <td>${statusBadge(b.status)}</td>
                <td style="text-align:right;">
                    <button class="icon-action" title="Edit" onclick="openEditModal(${b.id_beasiswa})"><i class="fa-solid fa-pencil"></i></button>
                    <form method="POST" action="${BASE_URL}/admin/beasiswa/delete/${b.id_beasiswa}" style="display:inline;" onsubmit="return confirm('Hapus beasiswa ' + ${JSON.stringify(b.nama_beasiswa)} + '? Tagihan terkait akan dihitung ulang.')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
                </td>
            </tr>`).join('');
        cardList.innerHTML = currentRows.map(b => `
            <div class="card bs-card" onclick="openEditModal(${b.id_beasiswa})">
                <div class="top"><span class="name">${esc(b.nama_siswa)}</span>${statusBadge(b.status)}</div>
                <div class="meta">${esc(b.nama_beasiswa)} · ${esc(b.nama_tagihan || '-')}</div>
                <div class="meta" style="margin-top:6px; font-weight:700; color:var(--brand-darker);">${nilaiFmt(b)}</div>
            </div>`).join('');
    }

    const pager = document.getElementById('pager');
    if (data.total_pages > 1) {
        pager.style.display = 'flex';
        pager.innerHTML = `<span>Halaman ${data.page} dari ${data.total_pages}</span><div class="btns">
            <button class="icon-action" ${data.page <= 1 ? 'disabled' : ''} onclick="gotoPage(${data.page - 1})"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="icon-action" ${data.page >= data.total_pages ? 'disabled' : ''} onclick="gotoPage(${data.page + 1})"><i class="fa-solid fa-chevron-right"></i></button>
        </div>`;
    } else { pager.style.display = 'none'; }
}
function gotoPage(p) { page = p; loadPage(); window.scrollTo({ top: 0, behavior: 'smooth' }); }

let searchDebounce;
document.getElementById('q').addEventListener('input', function () { clearTimeout(searchDebounce); searchDebounce = setTimeout(() => { q = this.value.trim(); page = 1; loadPage(); }, 350); });
document.getElementById('fTA').addEventListener('change', function () { fTA = this.value; page = 1; loadPage(); });

// ===================== MODAL =====================
function setBsMode(v) {
    document.getElementById('f_jenis_wrap').style.display = v === 'single' ? 'block' : 'none';
    document.getElementById('f_grup_wrap').style.display = v === 'bulk' ? 'block' : 'none';
    document.getElementById('f_jenis').required = v === 'single';
    document.getElementById('f_grup').required = v === 'bulk';
}
function setBsTipe() {
    const isNominal = document.getElementById('f_tipe_nominal').checked;
    document.getElementById('f_nilai_label').textContent = isNominal ? 'Nilai (Rp)' : 'Nilai (%)';
    document.getElementById('f_nilai').max = isNominal ? '' : '100';
}

function resetForm() {
    document.getElementById('bsForm').reset();
    document.getElementById('f_editing_id').value = '';
    document.getElementById('f_selected_siswa').innerHTML = '';
    document.getElementById('f_id_siswa').value = '';
    setBsMode('single');
    setBsTipe();
}

function openCreateModal() {
    resetForm();
    document.getElementById('bsModalTitle').textContent = 'Tambah Beasiswa';
    document.getElementById('bsForm').action = BASE_URL + '/admin/beasiswa/store';
    document.getElementById('f_mode_wrap').style.display = 'block';
    document.getElementById('f_status_wrap').style.display = 'none';
    document.getElementById('f_status').removeAttribute('required');
    openPanel('bsPanel');
}

function openEditModal(id) {
    const b = currentRows.find(x => x.id_beasiswa == id);
    if (!b) {
        // Baris ini tidak ada di halaman yang sedang tampil (mis. dari bookmark lama
        // ke ID yang sekarang ada di halaman lain) -- arahkan untuk cari manual.
        alert('Data ini ada di halaman lain. Silakan cari nama siswa/beasiswanya lewat kotak pencarian.');
        return;
    }
    resetForm();
    document.getElementById('f_editing_id').value = id;
    document.getElementById('bsModalTitle').textContent = 'Edit — ' + b.nama_beasiswa;
    document.getElementById('bsForm').action = BASE_URL + '/admin/beasiswa/update/' + id;

    // Edit selalu mode "single" -- satu baris yang sudah ada, sembunyikan toggle mode
    document.getElementById('f_mode_wrap').style.display = 'none';
    setBsMode('single');

    document.getElementById('f_id_siswa').value = b.id_siswa;
    document.getElementById('f_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="display:flex; align-items:center; justify-content:space-between; gap:12px; background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:10px 14px;"><div><strong>${esc(b.nama_siswa)}</strong><br><small>NIS ${esc(b.nis)}</small></div></div>`;
    document.getElementById('f_jenis').value = b.id_jenis_tagihan;
    document.getElementById('f_nama').value = b.nama_beasiswa;
    document.getElementById('f_ta').value = b.id_tahun_ajaran;
    document.getElementById(b.tipe_beasiswa === 'nominal' ? 'f_tipe_nominal' : 'f_tipe_persen').checked = true;
    setBsTipe();
    document.getElementById('f_nilai').value = b.nilai_beasiswa;
    document.getElementById('f_ket').value = b.keterangan || '';
    document.getElementById('f_status_wrap').style.display = 'block';
    document.getElementById('f_status').setAttribute('required', 'required');
    document.getElementById('f_status').value = b.status;

    openPanel('bsPanel');
}

if (typeof openSearchDropdown !== 'function') {
    window.openSearchDropdown = function (inputEl, dropdownEl, html) {
        dropdownEl.innerHTML = html;
        const rect = inputEl.getBoundingClientRect();
        dropdownEl.style.position = 'fixed';
        dropdownEl.style.left = rect.left + 'px';
        dropdownEl.style.right = 'auto';
        dropdownEl.style.top = (rect.bottom + 6) + 'px';
        dropdownEl.style.width = rect.width + 'px';
        dropdownEl.style.margin = '0';
        dropdownEl.style.display = 'block';
    };
}
if (typeof closeSearchDropdown !== 'function') {
    window.closeSearchDropdown = function (dropdownEl) { dropdownEl.style.display = 'none'; };
}
if (typeof scrollIntoModal !== 'function') {
    window.scrollIntoModal = function (el) { if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); };
}

let sSearchTimeout;
document.getElementById('f_siswa_search').addEventListener('input', function () {
    clearTimeout(sSearchTimeout);
    const keyword = this.value;
    const inputEl = this;
    if (keyword.length < 2) { closeSearchDropdown(document.getElementById('f_siswa_results')); return; }
    sSearchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(data => {
                const html = data.length === 0
                    ? '<div class="search-result-item" style="color:var(--faint);">Tidak ada hasil.</div>'
                    : data.map(s => `<div class="search-result-item" onclick='fSelectSiswa(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small style="color:var(--muted);">NIS ${esc(s.nis)} · ${esc(s.nama_kelas || 'Belum dikelas')}</small></div>`).join('');
                openSearchDropdown(inputEl, document.getElementById('f_siswa_results'), html);
            });
    }, 300);
});
function fSelectSiswa(s) {
    document.getElementById('f_id_siswa').value = s.id_siswa;
    document.getElementById('f_siswa_search').value = '';
    closeSearchDropdown(document.getElementById('f_siswa_results'));
    document.getElementById('f_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="display:flex; align-items:center; justify-content:space-between; gap:12px; background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:10px 14px;"><div><strong>${esc(s.nama_lengkap)}</strong><br><small>NIS ${esc(s.nis)}</small></div><button type="button" class="icon-action danger" onclick="document.getElementById('f_id_siswa').value=''; document.getElementById('f_selected_siswa').innerHTML='';"><i class="fa-solid fa-xmark"></i></button></div>`;
    scrollIntoModal(document.getElementById('f_selected_siswa'));
}
document.addEventListener('click', function (e) { if (!e.target.closest('.search-box') && !e.target.closest('#f_siswa_results')) closeSearchDropdown(document.getElementById('f_siswa_results')); });

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

loadPage().then(() => { if (location.hash) handleHash(); });
</script>

<?= $this->include('admin/layouts/footer') ?>