<?= $this->include('admin/layouts/header') ?>

<style>
.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 900px) { .toolbar { flex-direction: row; } }
.toolbar .search-wrap { position: relative; flex: 1; }
.toolbar .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar .search-wrap input { padding-left: 38px; }
.toolbar select { width: 100%; }
@media (min-width: 900px) { .toolbar select { width: 180px; flex-shrink: 0; } }
.pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-soft); font-size: 12.5px; color: var(--muted); }
.pager .btns { display: flex; gap: 6px; }
.st-card { padding: 14px; }
.st-card .top { display: flex; justify-content: space-between; gap: 8px; align-items: flex-start; }
.st-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.st-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 4px; }
.st-card .amount { font-weight: 800; color: var(--ink); font-family: 'Roboto Mono', monospace; margin-top: 8px; }

/* Checklist massal (modal Tambah) */
.checklist-group { border: 1px solid var(--border-soft); border-radius: var(--r-md); margin-bottom: 12px; overflow: hidden; }
.checklist-group-header { background: var(--border-soft); padding: 10px 14px; display: flex; align-items: center; gap: 10px; font-size: 12.5px; font-weight: 700; color: var(--ink); }
.checklist-group-header input { width: 16px; height: 16px; accent-color: var(--brand); }
.st-item { display: grid; grid-template-columns: 24px 1fr; row-gap: 8px; column-gap: 12px; align-items: center; padding: 10px 14px; border-top: 1px solid var(--border-soft); }
.st-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--brand); }
.st-item .nom-cell { grid-column: 1 / -1; display: flex; gap: 8px; }
@media (min-width: 640px) { .st-item { grid-template-columns: 24px 1.6fr 1fr; } .st-item .nom-cell { grid-column: auto; } }
.st-item .info strong { font-size: 12.5px; color: var(--ink); }
.st-item .info .badge { margin-left: 4px; }
.st-item input.nominal-input, .st-item select.bulan-select { padding: 7px 10px; border: 1.5px solid var(--border); border-radius: var(--r-sm); font-size: 12.5px; }
.st-item input.nominal-input:disabled, .st-item select.bulan-select:disabled { background: var(--border-soft); color: var(--faint); }
.st-item input.nominal-input { flex: 1; font-family: 'Roboto Mono', monospace; }
.st-item select.bulan-select { width: 110px; flex-shrink: 0; }
.st-summary { display: flex; gap: 20px; padding: 12px 16px; background: var(--brand-bg); border-radius: var(--r-md); font-size: 12.5px; font-weight: 700; color: var(--brand-darker); margin-top: 8px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:10px;">
    <div>
        <div class="page-title">Skema Tagihan</div>
        <div class="page-subtitle" id="resultCount">Memuat…</div>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah</span></button>
</div>

<div class="toolbar">
    <div class="search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari siswa, kelas, atau tagihan…"></div>
    <select class="input" id="fTA"><option value="">Semua Tahun Ajaran</option><?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>" <?= $filter_tahun_ajaran == $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?></option><?php endforeach; ?></select>
    <select class="input" id="fGrup"><option value="">Semua Grup</option><?php foreach ($grup_list as $g): ?><option value="<?= esc($g['grup_tagihan']) ?>" <?= $filter_grup === $g['grup_tagihan'] ? 'selected' : '' ?>><?= esc($g['grup_tagihan'] ?: 'Lainnya') ?></option><?php endforeach; ?></select>
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Grup</th><th>Tagihan</th><th>Tahun Ajaran</th><th>Berlaku Untuk</th><th style="text-align:right;">Nominal</th><th>Bulan</th><th></th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:10px;"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<!-- ===================== MODAL: Tambah (checklist massal) ===================== -->
<div class="overlay" id="addModal_overlay" onclick="closeModal('addModal')"></div>
<div class="modal modal-wide" id="addModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3>Tambah Skema Tagihan</h3><button type="button" class="modal-close" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="addForm" action="<?= base_url('admin/skema-tagihan/store-bulk') ?>" method="POST">
        <div class="modal-body">
            <div class="hint-box">
                <i class="fa-solid fa-circle-info" style="margin-top:1px;"></i>
                <span>Centang tagihan yang ingin dibuat skemanya, isi nominal per item, lalu Simpan. Tagihan bertipe <strong>bulanan</strong> otomatis dibuat 12 skema (Bulan 1–12) kecuali Anda pilih bulan tertentu.</span>
            </div>

            <div class="field">
                <label class="required">Target</label>
                <div class="segmented">
                    <label><input type="radio" name="target_skema" value="kelas" id="a_target_kelas" checked onchange="setAddTarget('kelas')"> Per Kelas</label>
                    <label><input type="radio" name="target_skema" value="semua_siswa" id="a_target_semua" onchange="setAddTarget('semua_siswa')"> Semua Siswa Aktif</label>
                    <label><input type="radio" name="target_skema" value="siswa" id="a_target_siswa" onchange="setAddTarget('siswa')"> Satu Siswa</label>
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="required">Tahun Ajaran</label>
                    <select class="input" name="id_tahun_ajaran" id="a_ta" required>
                        <option value="">— Pilih —</option>
                        <?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?> <?= $ta['status'] === 'aktif' ? '• Aktif' : '' ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field" id="a_kelas_wrap">
                    <label class="required">Kelas</label>
                    <select class="input" name="id_kelas" id="a_kelas">
                        <option value="">— Pilih —</option>
                        <?php foreach ($kelas as $k): ?><option value="<?= $k['id_kelas'] ?>"><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran'] ?? '-') ?>)</option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field" id="a_siswa_wrap" style="display:none;">
                    <label class="required">Cari Siswa</label>
                    <div class="search-box" style="position:relative;">
                        <input type="text" class="input" id="a_siswa_search" placeholder="Ketik NIS atau nama…" autocomplete="off">
                        <div class="search-results" id="a_siswa_results" style="display:none; position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:30; background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-md); max-height:200px; overflow-y:auto;"></div>
                    </div>
                    <input type="hidden" name="id_siswa" id="a_id_siswa">
                    <div id="a_selected_siswa" style="margin-top:8px;"></div>
                </div>
            </div>

            <div class="eyebrow" style="margin:18px 0 10px;">Pilih Tagihan &amp; Nominal</div>
            <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
            <div class="checklist-group">
                <div class="checklist-group-header">
                    <input type="checkbox" id="grup_<?= md5($grup) ?>" onchange="toggleGrup('<?= esc($grup) ?>', this.checked)">
                    <label for="grup_<?= md5($grup) ?>" style="cursor:pointer;"><?= esc($grup ?: 'LAINNYA') ?> (<?= count($items) ?> tagihan)</label>
                </div>
                <?php foreach ($items as $jt): ?>
                <div class="st-item" id="item_<?= $jt['id_jenis_tagihan'] ?>">
                    <input type="checkbox" name="tagihan[]" value="<?= $jt['id_jenis_tagihan'] ?>" id="check_<?= $jt['id_jenis_tagihan'] ?>" data-grup="<?= esc($grup) ?>" data-tipe="<?= $jt['tipe_tagihan'] ?>" onchange="toggleItem(<?= $jt['id_jenis_tagihan'] ?>)">
                    <div class="info">
                        <strong><?= esc($jt['nama_tagihan']) ?></strong>
                        <?php if ($jt['tipe_tagihan'] === 'bulanan'): ?><span class="badge badge-info">12x/thn</span><?php elseif ($jt['tipe_tagihan'] === 'tahunan'): ?><span class="badge badge-neutral">Tahunan</span><?php else: ?><span class="badge badge-neutral">Sekali</span><?php endif; ?>
                    </div>
                    <div class="nom-cell">
                        <input type="text" class="nominal-input" placeholder="Rp 0" id="nominal_display_<?= $jt['id_jenis_tagihan'] ?>" disabled autocomplete="off">
                        <input type="hidden" name="nominal[<?= $jt['id_jenis_tagihan'] ?>]" id="nominal_<?= $jt['id_jenis_tagihan'] ?>">
                        <?php if ($jt['tipe_tagihan'] === 'bulanan'): ?>
                        <select class="bulan-select" name="bulan[<?= $jt['id_jenis_tagihan'] ?>]" id="bulan_<?= $jt['id_jenis_tagihan'] ?>" disabled>
                            <option value="">Semua bulan</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>">Bulan <?= $i ?></option><?php endfor; ?>
                        </select>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>

            <div class="st-summary">
                <span><i class="fa-solid fa-list-check"></i> <span id="a_total_selected">0</span> tagihan dipilih</span>
                <span><i class="fa-solid fa-layer-group"></i> ≈<span id="a_total_skema">0</span> skema akan dibuat</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-wand-magic-sparkles"></i> Simpan</button>
        </div>
    </form>
</div>

<!-- ===================== MODAL: Edit (satu baris) ===================== -->
<div class="overlay" id="editModal_overlay" onclick="closeModal('editModal')"></div>
<div class="modal" id="editModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3>Edit Skema Tagihan</h3><button type="button" class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="editForm" method="POST">
        <div class="modal-body">
            <div class="field">
                <label class="required">Berlaku Untuk</label>
                <div class="segmented">
                    <label><input type="radio" name="tipe_skema" value="kelas" id="e_tipe_kelas" onchange="setEditTipe('kelas')"> Kelas</label>
                    <label><input type="radio" name="tipe_skema" value="siswa" id="e_tipe_siswa" onchange="setEditTipe('siswa')"> Siswa Tertentu</label>
                </div>
            </div>
            <div class="field" id="e_kelas_wrap">
                <label class="required">Kelas</label>
                <select class="input" name="id_kelas" id="e_kelas">
                    <option value="">— Pilih —</option>
                    <?php foreach ($kelas as $k): ?><option value="<?= $k['id_kelas'] ?>"><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran'] ?? '-') ?>)</option><?php endforeach; ?>
                </select>
            </div>
            <div class="field" id="e_siswa_wrap" style="display:none;">
                <label class="required">Cari Siswa</label>
                <div class="search-box" style="position:relative;">
                    <input type="text" class="input" id="e_siswa_search" placeholder="Ketik NIS atau nama…" autocomplete="off">
                    <div class="search-results" id="e_siswa_results" style="display:none; position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:30; background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-md); max-height:200px; overflow-y:auto;"></div>
                </div>
                <input type="hidden" name="id_siswa" id="e_id_siswa">
                <div id="e_selected_siswa" style="margin-top:8px;"></div>
            </div>

            <div class="field">
                <label class="required">Jenis Tagihan</label>
                <select class="input" name="id_jenis_tagihan" id="e_jenis" required>
                    <option value="">— Pilih —</option>
                    <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
                        <optgroup label="<?= esc($grup ?: 'LAINNYA') ?>">
                        <?php foreach ($items as $jt): ?><option value="<?= $jt['id_jenis_tagihan'] ?>" data-tipe="<?= $jt['tipe_tagihan'] ?>"><?= esc($jt['nama_tagihan']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="required">Tahun Ajaran</label>
                    <select class="input" name="id_tahun_ajaran" id="e_ta" required>
                        <option value="">— Pilih —</option>
                        <?php foreach ($tahun_ajaran as $ta): ?><option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label class="required">Nominal (Rp)</label>
                    <input type="number" class="input" name="nominal" id="e_nominal" min="0" required>
                </div>
            </div>

            <div class="field" id="e_bulan_wrap" style="display:none;">
                <label>Bulan (khusus tagihan bulanan)</label>
                <select class="input" name="bulan_tagihan" id="e_bulan">
                    <option value="">Semua bulan</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>">Bulan <?= $i ?></option><?php endfor; ?>
                </select>
            </div>

            <div class="field">
                <label>Keterangan</label>
                <textarea class="input" name="keterangan" id="e_ket" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/skema-tagihan') ?>';
let page = 1;
const PER_PAGE = 20;
let q = '', fTA = '<?= esc($filter_tahun_ajaran ?? '') ?>', fGrup = '<?= esc($filter_grup ?? '') ?>';
let currentRows = [];
let fetchToken = 0;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 6; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:60%;"></div><div class="skeleton-bar" style="width:35%;"></div></div>';
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
    if (fGrup) params.set('filter_grup', fGrup);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) { document.getElementById('resultCount').textContent = 'Gagal memuat data.'; return; }
    if (myToken !== fetchToken) return;

    currentRows = data.rows;
    document.getElementById('resultCount').textContent = data.total + ' skema tagihan';

    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (currentRows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-sitemap"></i><p>Tidak ada skema tagihan yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';
        tbody.innerHTML = currentRows.map(sk => {
            const berlaku = sk.nama_kelas ? `<span class="badge badge-info">Kelas ${esc(sk.nama_kelas)}</span>` : (sk.nama_siswa ? `<span class="badge badge-success">${esc(sk.nama_siswa)}</span>` : '<span class="badge badge-neutral">Semua Siswa</span>');
            return `<tr>
                <td><span class="badge badge-neutral">${esc((sk.grup_tagihan || 'LAINNYA').toUpperCase())}</span></td>
                <td style="font-weight:700; color:var(--ink);">${esc(sk.nama_tagihan)}</td>
                <td>${esc(sk.nama_tahun_ajaran)}</td>
                <td>${berlaku}</td>
                <td style="text-align:right; font-weight:700; font-family:'Roboto Mono',monospace;">Rp ${fmt(sk.nominal)}</td>
                <td>${sk.bulan_tagihan ? '<span class="badge badge-neutral">Bln ' + sk.bulan_tagihan + '</span>' : '<span style="color:var(--faint);">—</span>'}</td>
                <td style="text-align:right;">
                    <button class="icon-action" title="Edit" onclick="openEditModal(${sk.id_skema_tagihan})"><i class="fa-solid fa-pencil"></i></button>
                    <form method="POST" action="${BASE_URL}/admin/skema-tagihan/delete/${sk.id_skema_tagihan}" style="display:inline;" onsubmit="return confirm('Hapus skema tagihan ini?')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
                </td>
            </tr>`;
        }).join('');
        cardList.innerHTML = currentRows.map(sk => {
            const berlaku = sk.nama_kelas ? `Kelas ${esc(sk.nama_kelas)}` : (sk.nama_siswa ? esc(sk.nama_siswa) : 'Semua Siswa');
            return `<div class="card st-card" onclick="openEditModal(${sk.id_skema_tagihan})">
                <div class="top"><span class="name">${esc(sk.nama_tagihan)}</span><span class="badge badge-neutral">${esc((sk.grup_tagihan || 'LAINNYA').toUpperCase())}</span></div>
                <div class="meta">${esc(sk.nama_tahun_ajaran)} · ${berlaku}${sk.bulan_tagihan ? ' · Bln ' + sk.bulan_tagihan : ''}</div>
                <div class="amount">Rp ${fmt(sk.nominal)}</div>
            </div>`;
        }).join('');
    }

    const pager = document.getElementById('pager');
    if (data.total_pages > 1) {
        pager.style.display = 'flex';
        pager.innerHTML = `<span>Halaman ${data.page} dari ${data.total_pages}</span><div class="btns">
            <button class="icon-action" ${data.page <= 1 ? 'disabled' : ''} onclick="gotoPage(${data.page - 1})"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="icon-action" ${data.page >= data.total_pages ? 'disabled' : ''} onclick="gotoPage(${data.page + 1})"><i class="fa-solid fa-chevron-right"></i></button>
        </div>`;
    } else { pager.style.display = 'none'; }
    return data;
}
function gotoPage(p) { page = p; loadPage(); window.scrollTo({ top: 0, behavior: 'smooth' }); }

let searchDebounce;
document.getElementById('q').addEventListener('input', function () { clearTimeout(searchDebounce); searchDebounce = setTimeout(() => { q = this.value.trim(); page = 1; loadPage(); }, 350); });
document.getElementById('fTA').addEventListener('change', function () { fTA = this.value; page = 1; loadPage(); });
document.getElementById('fGrup').addEventListener('change', function () { fGrup = this.value; page = 1; loadPage(); });

// ===================== MODAL: Tambah (checklist massal) =====================
function setAddTarget(v) {
    document.getElementById('a_kelas_wrap').style.display = v === 'kelas' ? 'block' : 'none';
    document.getElementById('a_siswa_wrap').style.display = v === 'siswa' ? 'block' : 'none';
    document.getElementById('a_kelas').required = v === 'kelas';
    document.getElementById('a_id_siswa').required = v === 'siswa';
}
function openCreateModal() {
    document.getElementById('addForm').reset();
    document.getElementById('a_selected_siswa').innerHTML = '';
    document.getElementById('a_id_siswa').value = '';
    setAddTarget('kelas');
    document.querySelectorAll('.st-item').forEach(el => el.classList.remove('checked'));
    document.querySelectorAll('.nominal-input, .bulan-select').forEach(el => el.disabled = true);
    updateAddSummary();
    openModal('addModal');
}
function toggleGrup(grup, checked) {
    document.querySelectorAll(`input[data-grup="${grup}"][name="tagihan[]"]`).forEach(cb => { cb.checked = checked; toggleItem(cb.value); });
}
function toggleItem(id) {
    const checkbox = document.getElementById('check_' + id);
    const nominalDisplay = document.getElementById('nominal_display_' + id);
    const nominalHidden = document.getElementById('nominal_' + id);
    const bulanSelect = document.getElementById('bulan_' + id);
    const item = document.getElementById('item_' + id);
    if (checkbox.checked) {
        nominalDisplay.disabled = false;
        if (bulanSelect) bulanSelect.disabled = false;
        item.classList.add('checked');
    } else {
        nominalDisplay.disabled = true; nominalDisplay.value = ''; nominalHidden.value = '';
        if (bulanSelect) { bulanSelect.disabled = true; bulanSelect.value = ''; }
        item.classList.remove('checked');
    }
    updateAddSummary();
}
document.querySelectorAll('.nominal-input').forEach(input => {
    const id = input.id.replace('nominal_display_', '');
    const hidden = document.getElementById('nominal_' + id);
    input.addEventListener('input', function () {
        const digits = this.value.replace(/[^\d]/g, '');
        hidden.value = digits;
        this.value = digits ? fmt(digits) : '';
    });
});
function updateAddSummary() {
    const checked = document.querySelectorAll('input[name="tagihan[]"]:checked');
    let totalSkema = 0;
    checked.forEach(cb => { totalSkema += (cb.dataset.tipe === 'bulanan' && !document.getElementById('bulan_' + cb.value)?.value) ? 12 : 1; });
    document.getElementById('a_total_selected').textContent = checked.length;
    document.getElementById('a_total_skema').textContent = totalSkema;
}

let aSearchTimeout;
document.getElementById('a_siswa_search').addEventListener('input', function () {
    clearTimeout(aSearchTimeout);
    const keyword = this.value;
    if (keyword.length < 2) { document.getElementById('a_siswa_results').style.display = 'none'; return; }
    aSearchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword)).then(r => r.json()).then(data => {
            const results = document.getElementById('a_siswa_results');
            results.innerHTML = data.length === 0 ? '<div class="search-result-item" style="padding:12px 16px; color:var(--faint);">Tidak ada hasil.</div>'
                : data.map(s => `<div class="search-result-item" style="padding:12px 16px; cursor:pointer; border-bottom:1px solid var(--border-soft);" onclick='aSelectSiswa(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small style="color:var(--muted);">NIS ${esc(s.nis)}</small></div>`).join('');
            results.style.display = 'block';
        });
    }, 300);
});
function aSelectSiswa(s) {
    document.getElementById('a_id_siswa').value = s.id_siswa;
    document.getElementById('a_siswa_search').value = '';
    document.getElementById('a_siswa_results').style.display = 'none';
    document.getElementById('a_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:10px 14px;"><strong>${esc(s.nama_lengkap)}</strong> <small>(NIS ${esc(s.nis)})</small></div>`;
}
document.addEventListener('click', e => { if (!e.target.closest('#a_siswa_wrap')) document.getElementById('a_siswa_results').style.display = 'none'; if (!e.target.closest('#e_siswa_wrap')) document.getElementById('e_siswa_results').style.display = 'none'; });

// ===================== MODAL: Edit (satu baris) =====================
function setEditTipe(v) {
    document.getElementById('e_kelas_wrap').style.display = v === 'kelas' ? 'block' : 'none';
    document.getElementById('e_siswa_wrap').style.display = v === 'siswa' ? 'block' : 'none';
    document.getElementById('e_kelas').required = v === 'kelas';
    document.getElementById('e_id_siswa').required = v === 'siswa';
}
document.getElementById('e_jenis').addEventListener('change', function () {
    const tipe = this.selectedOptions[0]?.dataset.tipe;
    document.getElementById('e_bulan_wrap').style.display = tipe === 'bulanan' ? 'block' : 'none';
});

function fillEditForm(sk) {
    document.getElementById('editForm').action = BASE_URL + '/admin/skema-tagihan/update/' + sk.id_skema_tagihan;
    const tipe = sk.id_siswa ? 'siswa' : 'kelas';
    document.getElementById(tipe === 'siswa' ? 'e_tipe_siswa' : 'e_tipe_kelas').checked = true;
    setEditTipe(tipe);
    if (tipe === 'kelas') {
        document.getElementById('e_kelas').value = sk.id_kelas;
    } else {
        document.getElementById('e_id_siswa').value = sk.id_siswa;
        document.getElementById('e_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:10px 14px;"><strong>${esc(sk.nama_siswa)}</strong> <small>(NIS ${esc(sk.nis)})</small></div>`;
    }
    document.getElementById('e_jenis').value = sk.id_jenis_tagihan;
    document.getElementById('e_bulan_wrap').style.display = sk.tipe_tagihan === 'bulanan' ? 'block' : 'none';
    document.getElementById('e_ta').value = sk.id_tahun_ajaran;
    document.getElementById('e_nominal').value = Math.round(sk.nominal);
    document.getElementById('e_bulan').value = sk.bulan_tagihan || '';
    document.getElementById('e_ket').value = sk.keterangan || '';
    openModal('editModal');
}

function openEditModal(id) {
    document.getElementById('editForm').reset();
    document.getElementById('e_selected_siswa').innerHTML = '';
    document.getElementById('e_id_siswa').value = '';
    const sk = currentRows.find(x => x.id_skema_tagihan == id);
    if (sk) { fillEditForm(sk); return; }
    alert('Data ini ada di halaman lain. Silakan cari dulu lewat kotak pencarian di atas.');
}

let eSearchTimeout;
document.getElementById('e_siswa_search').addEventListener('input', function () {
    clearTimeout(eSearchTimeout);
    const keyword = this.value;
    if (keyword.length < 2) { document.getElementById('e_siswa_results').style.display = 'none'; return; }
    eSearchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword)).then(r => r.json()).then(data => {
            const results = document.getElementById('e_siswa_results');
            results.innerHTML = data.length === 0 ? '<div class="search-result-item" style="padding:12px 16px; color:var(--faint);">Tidak ada hasil.</div>'
                : data.map(s => `<div class="search-result-item" style="padding:12px 16px; cursor:pointer; border-bottom:1px solid var(--border-soft);" onclick='eSelectSiswa(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small style="color:var(--muted);">NIS ${esc(s.nis)}</small></div>`).join('');
            results.style.display = 'block';
        });
    }, 300);
});
function eSelectSiswa(s) {
    document.getElementById('e_id_siswa').value = s.id_siswa;
    document.getElementById('e_siswa_search').value = '';
    document.getElementById('e_siswa_results').style.display = 'none';
    document.getElementById('e_selected_siswa').innerHTML = `<div class="selected-siswa-box" style="background:var(--brand-bg); border:1.5px solid var(--brand-light); border-radius:var(--r-md); padding:10px 14px;"><strong>${esc(s.nama_lengkap)}</strong> <small>(NIS ${esc(s.nis)})</small></div>`;
}

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

loadPage().then(() => { if (location.hash) handleHash(); });
</script>

<?= $this->include('admin/layouts/footer') ?>
