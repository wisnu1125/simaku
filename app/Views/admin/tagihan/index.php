<?= $this->include('admin/layouts/header') ?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
@media (min-width: 640px) { .stat-grid { gap: 16px; } }
.stat-card { padding: 14px 16px; }
@media (min-width: 640px) { .stat-card { padding: 18px 20px; } }
.stat-card .value { font-size: 14px; font-weight: 900; }
@media (min-width: 640px) { .stat-card .value { font-size: 18px; } }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 900px) { .toolbar { flex-direction: row; } }
.toolbar .search-wrap { position: relative; flex: 1; }
.toolbar .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar .search-wrap input { padding-left: 38px; }
.toolbar select { width: 100%; }
@media (min-width: 900px) { .toolbar select { width: 160px; flex-shrink: 0; } }

.bulk-bar { display: none; align-items: center; justify-content: space-between; gap: 10px; background: var(--brand-darker); color: #fff; padding: 12px 18px; border-radius: var(--r-md); margin-bottom: 12px; font-size: 13px; font-weight: 600; }
.bulk-bar.show { display: flex; }
.bulk-bar button { background: rgba(255,255,255,.15); color: #fff; border: none; padding: 7px 14px; border-radius: var(--r-sm); font-size: 12.5px; font-weight: 700; cursor: pointer; }
.bulk-bar button:hover { background: rgba(255,255,255,.25); }

.kwitansi-code, .mono-cell { font-family: 'Roboto Mono', monospace; }
.pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-soft); font-size: 12.5px; color: var(--muted); }
.pager .btns { display: flex; gap: 6px; }

.tgh-card { padding: 14px; display: flex; align-items: flex-start; gap: 10px; }
.tgh-card .body { flex: 1; min-width: 0; }
.tgh-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.tgh-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.tgh-card .amounts { display: flex; justify-content: space-between; margin-top: 8px; font-size: 12.5px; }

/* Modal generate */
.radio-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
@media (max-width: 560px) { .radio-cards { grid-template-columns: 1fr; } }
.radio-card { border: 1.5px solid var(--border); border-radius: var(--r-md); padding: 14px 10px; text-align: center; cursor: pointer; transition: .15s; }
.radio-card:has(input:checked) { border-color: var(--brand); background: var(--brand-bg); }
.radio-card input { display: none; }
.radio-card .icon { font-size: 22px; margin-bottom: 6px; }
.radio-card .label { font-size: 12.5px; font-weight: 700; color: var(--ink); }
.radio-card .desc { font-size: 10.5px; color: var(--muted); margin-top: 2px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:10px;">
    <div>
        <div class="page-title">Tagihan</div>
        <div class="page-subtitle" id="resultCount">Memuat…</div>
    </div>
    <button class="btn btn-primary" onclick="openGenerateModal()"><i class="fa-solid fa-bolt"></i> <span class="hide-xs">Generate</span></button>
</div>

<div class="stat-grid">
    <div class="card stat-card"><div class="value" id="statJumlah">0</div><div class="label">Jumlah Tagihan</div></div>
    <div class="card stat-card"><div class="value" id="statNominal">Rp 0</div><div class="label">Total Nominal</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--danger);" id="statSisa">Rp 0</div><div class="label">Total Tunggakan</div></div>
</div>

<div class="toolbar">
    <div class="search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari nama atau NIS…"></div>
    <select class="input" id="fTahunAjaran"><option value="">Semua Tahun Ajaran</option><?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>" <?= $filter_tahun_ajaran == $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option><?php endforeach; ?></select>
    <select class="input" id="fKelas"><option value="">Semua Kelas</option><?php foreach ($kelas as $k): ?><option value="<?= $k['id_kelas'] ?>" <?= $filter_kelas == $k['id_kelas'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option><?php endforeach; ?></select>
    <select class="input" id="fStatus"><option value="">Semua Status</option><option value="belum_bayar">Belum Bayar</option><option value="cicil">Cicil</option><option value="lunas">Lunas</option></select>
</div>

<div class="bulk-bar" id="bulkBar">
    <span><span id="bulkCount">0</span> tagihan terpilih</span>
    <button type="button" onclick="bulkDelete()"><i class="fa-solid fa-trash"></i> Hapus Terpilih</button>
</div>
<form id="formBulkDelete" method="POST" action="<?= base_url('admin/tagihan/bulk-delete') ?>" style="display:none;"></form>
<form id="formDeleteSingle" method="POST" style="display:none;"></form>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th style="width:32px;"><input type="checkbox" id="selectAll"></th><th>Siswa</th><th>Tagihan</th><th style="text-align:right;">Nominal</th><th style="text-align:right;">Sisa</th><th>Status</th><th></th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<!-- ===================== MODAL: Generate Tagihan ===================== -->
<div class="overlay" id="genModal_overlay" onclick="closeModal('genModal')"></div>
<div class="modal" id="genModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3>Generate Tagihan</h3><button type="button" class="modal-close" onclick="closeModal('genModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="genForm" action="<?= base_url('admin/tagihan/generate') ?>" method="POST">
        <div class="modal-body">
            <div class="hint-box" style="background:var(--warning-bg); border:1px solid var(--warning-border); border-radius:var(--r-md); padding:12px 14px; font-size:12.5px; color:#78350f; margin-bottom:18px; display:flex; gap:10px;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-top:1px;"></i>
                <span>Tagihan yang <strong>belum ada pembayaran</strong> pada ruang lingkup terpilih akan dihapus &amp; dibuat ulang. Tagihan yang sudah ada pembayarannya tidak akan tersentuh.</span>
            </div>

            <div class="field">
                <label class="required">Tahun Ajaran</label>
                <select class="input" name="id_tahun_ajaran" id="g_ta" required>
                    <option value="">— Pilih —</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?> <?= $ta['status'] === 'aktif' ? '• Aktif' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label class="required">Ruang Lingkup</label>
                <div class="radio-cards">
                    <label class="radio-card"><input type="radio" name="tipe_generate" value="semua" checked onchange="setGenScope('semua')"><div class="icon">🌐</div><div class="label">Global</div><div class="desc">Semua siswa aktif</div></label>
                    <label class="radio-card"><input type="radio" name="tipe_generate" value="kelas" onchange="setGenScope('kelas')"><div class="icon">🏫</div><div class="label">Kelas</div><div class="desc">Satu kelas</div></label>
                    <label class="radio-card"><input type="radio" name="tipe_generate" value="siswa" onchange="setGenScope('siswa')"><div class="icon">👤</div><div class="label">Personal</div><div class="desc">Satu siswa</div></label>
                </div>
            </div>

            <div class="field" id="g_kelas_wrap" style="display:none;">
                <label class="required">Pilih Kelas</label>
                <select class="input" name="id_kelas" id="g_kelas">
                    <option value="">— Pilih —</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" data-tahun="<?= $k['id_tahun_ajaran'] ?>"><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field" id="g_siswa_wrap" style="display:none;">
                <label class="required">Cari Siswa</label>
                <div class="search-box" style="position:relative;">
                    <input type="text" class="input" id="g_siswa_search" placeholder="Ketik NIS atau nama…" autocomplete="off">
                    <div class="search-results" id="g_siswa_results" style="display:none; position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:30; background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-md); max-height:200px; overflow-y:auto;"></div>
                </div>
                <input type="hidden" name="id_siswa" id="g_id_siswa">
                <div id="g_selected_siswa" style="margin-top:10px;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('genModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-bolt"></i> Jalankan Generate</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/tagihan') ?>';
let page = 1;
const PER_PAGE = 20;
let q = '', fTA = '<?= esc($filter_tahun_ajaran ?? '') ?>', fKelas = '<?= esc($filter_kelas ?? '') ?>', fStatus = '';
let currentRows = [];
let selectedIds = new Set();
let fetchToken = 0;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function statusBadge(s) {
    if (s === 'lunas') return '<span class="badge badge-success">Lunas</span>';
    if (s === 'cicil') return '<span class="badge badge-warning">Cicil</span>';
    return '<span class="badge badge-danger">Belum Bayar</span>';
}

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 6; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:65%;"></div><div class="skeleton-bar" style="width:35%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" style="padding:0;">' + html + '</td></tr>';
}

async function loadPage() {
    const myToken = ++fetchToken;
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';

    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (q) params.set('keyword', q);
    if (fTA) params.set('filter_tahun_ajaran', fTA);
    if (fKelas) params.set('filter_kelas', fKelas);
    if (fStatus) params.set('filter_status', fStatus);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) { document.getElementById('resultCount').textContent = 'Gagal memuat data.'; return; }
    if (myToken !== fetchToken) return;

    currentRows = data.rows;
    document.getElementById('statJumlah').textContent = data.stats.jumlah;
    document.getElementById('statNominal').textContent = 'Rp ' + fmt(data.stats.total_nominal);
    document.getElementById('statSisa').textContent = 'Rp ' + fmt(data.stats.total_sisa);
    document.getElementById('resultCount').textContent = data.total + ' tagihan';

    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (currentRows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-file-invoice"></i><p>Tidak ada tagihan yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';
        tbody.innerHTML = currentRows.map(t => `
            <tr>
                <td>${t.status_tagihan === 'belum_bayar' ? `<input type="checkbox" class="row-check" data-id="${t.id_tagihan}" ${selectedIds.has(String(t.id_tagihan)) ? 'checked' : ''} onchange="toggleRow(${t.id_tagihan}, this.checked)">` : ''}</td>
                <td><div style="font-weight:700; color:var(--ink);">${esc(t.nama_siswa)}</div><div style="font-size:11.5px; color:var(--muted);">${esc(t.nis)} · ${esc(t.nama_kelas || '-')}</div></td>
                <td><div style="font-weight:600; font-size:13px;">${esc(t.nama_tagihan)}</div>${t.bulan_tagihan ? '<span style="font-size:10.5px; color:var(--muted);">Bulan ' + t.bulan_tagihan + '</span>' : ''}</td>
                <td class="mono-cell" style="text-align:right; font-weight:700;">Rp ${fmt(t.nominal_akhir)}</td>
                <td class="mono-cell" style="text-align:right; font-weight:700; color:var(--danger);">Rp ${fmt(t.sisa_tagihan)}</td>
                <td>${statusBadge(t.status_tagihan)}</td>
                <td style="text-align:right; white-space:nowrap;">
                    <a class="icon-action" href="${BASE_URL}/admin/tagihan/detail/${t.id_siswa}" title="Lihat"><i class="fa-solid fa-eye"></i></a>
                    ${t.status_tagihan === 'belum_bayar' ? `<button class="icon-action danger" title="Hapus" onclick="deleteSingle(${t.id_tagihan}, '${esc(t.nama_tagihan).replace(/'/g, "\\'")}')"><i class="fa-solid fa-trash"></i></button>` : ''}
                </td>
            </tr>`).join('');

        cardList.innerHTML = currentRows.map(t => `
            <div class="card tgh-card">
                ${t.status_tagihan === 'belum_bayar' ? `<input type="checkbox" class="row-check" data-id="${t.id_tagihan}" ${selectedIds.has(String(t.id_tagihan)) ? 'checked' : ''} onchange="toggleRow(${t.id_tagihan}, this.checked)" style="margin-top:3px; width:18px; height:18px; accent-color:var(--brand);">` : '<div style="width:18px;"></div>'}
                <div class="body">
                    <div class="name">${esc(t.nama_siswa)}</div>
                    <div class="meta">${esc(t.nama_tagihan)}${t.bulan_tagihan ? ' · Bln ' + t.bulan_tagihan : ''} · ${esc(t.nis)}</div>
                    <div class="amounts"><span>${statusBadge(t.status_tagihan)}</span><span class="mono-cell" style="font-weight:700; color:var(--danger);">Rp ${fmt(t.sisa_tagihan)}</span></div>
                </div>
                <a class="icon-action" href="${BASE_URL}/admin/tagihan/detail/${t.id_siswa}"><i class="fa-solid fa-eye"></i></a>
            </div>`).join('');
    }

    document.getElementById('selectAll').checked = false;
    updateBulkBar();

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
document.getElementById('fTahunAjaran').addEventListener('change', function () { fTA = this.value; page = 1; loadPage(); });
document.getElementById('fKelas').addEventListener('change', function () { fKelas = this.value; page = 1; loadPage(); });
document.getElementById('fStatus').addEventListener('change', function () { fStatus = this.value; page = 1; loadPage(); });

// ===================== Bulk select & delete =====================
function toggleRow(id, checked) {
    if (checked) selectedIds.add(String(id)); else selectedIds.delete(String(id));
    updateBulkBar();
}
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => { cb.checked = this.checked; toggleRow(cb.dataset.id, this.checked); });
});
function updateBulkBar() {
    document.getElementById('bulkCount').textContent = selectedIds.size;
    document.getElementById('bulkBar').classList.toggle('show', selectedIds.size > 0);
}
function bulkDelete() {
    if (selectedIds.size === 0) return;
    if (!confirm('Hapus ' + selectedIds.size + ' tagihan terpilih? Tindakan ini tidak bisa dibatalkan.')) return;
    const form = document.getElementById('formBulkDelete');
    form.innerHTML = '';
    selectedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'id_tagihan[]'; inp.value = id;
        form.appendChild(inp);
    });
    form.submit();
}
function deleteSingle(id, nama) {
    if (!confirm('Hapus tagihan "' + nama + '"?')) return;
    const form = document.getElementById('formDeleteSingle');
    form.action = BASE_URL + '/admin/tagihan/delete/' + id;
    form.submit();
}

// ===================== Modal: Generate =====================
function setGenScope(v) {
    document.getElementById('g_kelas_wrap').style.display = v === 'kelas' ? 'block' : 'none';
    document.getElementById('g_siswa_wrap').style.display = v === 'siswa' ? 'block' : 'none';
    document.getElementById('g_kelas').required = v === 'kelas';
    document.getElementById('g_id_siswa').required = v === 'siswa';
}
function openGenerateModal() {
    document.getElementById('genForm').reset();
    document.getElementById('g_selected_siswa').innerHTML = '';
    document.getElementById('g_id_siswa').value = '';
    setGenScope('semua');
    openModal('genModal');
}

let gSearchTimeout;
document.getElementById('g_siswa_search').addEventListener('input', function () {
    clearTimeout(gSearchTimeout);
    const keyword = this.value;
    if (keyword.length < 2) { document.getElementById('g_siswa_results').style.display = 'none'; return; }
    gSearchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(data => {
                const results = document.getElementById('g_siswa_results');
                results.innerHTML = data.length === 0
                    ? '<div class="search-result-item" style="padding:12px 16px; color:var(--faint);">Tidak ada hasil.</div>'
                    : data.map(s => `<div class="search-result-item" style="padding:12px 16px; cursor:pointer; border-bottom:1px solid var(--border-soft);" onclick='gSelectSiswa(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small style="color:var(--muted);">NIS ${esc(s.nis)} · ${esc(s.nama_kelas || 'Belum dikelas')}</small></div>`).join('');
                results.style.display = 'block';
            });
    }, 300);
});
function gSelectSiswa(s) {
    document.getElementById('g_id_siswa').value = s.id_siswa;
    document.getElementById('g_siswa_search').value = '';
    document.getElementById('g_siswa_results').style.display = 'none';
    document.getElementById('g_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="display:flex; align-items:center; justify-content:space-between; gap:12px; background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:12px 14px;"><div><strong>${esc(s.nama_lengkap)}</strong><br><small>NIS ${esc(s.nis)}</small></div><button type="button" class="icon-action danger" onclick="document.getElementById('g_id_siswa').value=''; document.getElementById('g_selected_siswa').innerHTML='';"><i class="fa-solid fa-xmark"></i></button></div>`;
}
document.addEventListener('click', function (e) { if (!e.target.closest('#g_siswa_wrap')) document.getElementById('g_siswa_results').style.display = 'none'; });

function handleHash() { if (location.hash === '#generate') openGenerateModal(); }

document.getElementById('fTahunAjaran').value = fTA;
document.getElementById('fKelas').value = fKelas;
loadPage();
if (location.hash) handleHash();
</script>

<?= $this->include('admin/layouts/footer') ?>
