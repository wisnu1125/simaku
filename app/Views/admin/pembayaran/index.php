<?= $this->include('admin/layouts/header') ?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
@media (min-width: 640px) { .stat-grid { gap: 16px; } }
.stat-card { padding: 14px 16px; }
@media (min-width: 640px) { .stat-card { padding: 18px 20px; } }
.stat-card .value { font-size: 15px; font-weight: 900; }
@media (min-width: 640px) { .stat-card .value { font-size: 19px; } }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 768px) { .toolbar { flex-direction: row; } }
.toolbar .search-wrap { position: relative; flex: 1; }
.toolbar .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar .search-wrap input { padding-left: 38px; }
.toolbar select { width: 100%; }
@media (min-width: 768px) { .toolbar select { width: 160px; flex-shrink: 0; } }

.kwitansi-code { font-family: 'Roboto Mono', monospace; font-size: 12px; color: var(--brand-darker); background: var(--border-soft); padding: 3px 8px; border-radius: 6px; }
.bayar-card { padding: 14px; display: flex; align-items: center; gap: 12px; }
.bayar-card .body { flex: 1; min-width: 0; }
.bayar-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.bayar-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.bayar-card .amount { font-size: 14px; font-weight: 900; font-family: 'Roboto Mono', monospace; flex-shrink: 0; text-align: right; }
.pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-soft); font-size: 12.5px; color: var(--muted); }
.pager .btns { display: flex; gap: 6px; }

/* Modal form pembayaran */
.selected-siswa-box { display: flex; align-items: center; justify-content: space-between; gap: 12px; background: var(--brand-bg); border: 1.5px solid var(--brand-light); border-radius: var(--r-md); padding: 14px 16px; margin-top: 10px; }
.section-step { display: flex; align-items: center; gap: 10px; margin: 22px 0 12px; }
.section-step:first-child { margin-top: 0; }
.section-step .num { width: 22px; height: 22px; border-radius: 50%; background: var(--brand); color: #fff; font-size: 11.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.section-step .txt { font-size: 13.5px; font-weight: 800; color: var(--ink); }
.checklist { border: 1px solid var(--border-soft); border-radius: var(--r-md); overflow: hidden; }
.checklist-header { display: none; padding: 10px 16px; background: var(--border-soft); font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--muted); grid-template-columns: 28px 1.6fr 1fr 1fr; gap: 14px; }
@media (min-width: 640px) { .checklist-header { display: grid; } }
.checklist-item { display: grid; grid-template-columns: 28px 1fr; row-gap: 10px; column-gap: 14px; align-items: center; padding: 14px 16px; border-bottom: 1px solid var(--border-soft); }
.checklist-item:last-child { border-bottom: none; }
.checklist-item.checked { background: var(--brand-bg); }
.checklist-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--brand); cursor: pointer; }
.checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: 1 / -1; }
@media (min-width: 640px) { .checklist-item { grid-template-columns: 28px 1.6fr 1fr 1fr; row-gap: 0; } .checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: auto; } }
.tagihan-info strong { font-size: 13.5px; color: var(--ink); display: block; margin-bottom: 4px; }
.tagihan-info .sisa { font-size: 12px; color: var(--danger); font-weight: 700; margin-top: 4px; }
.nominal-input, .date-input { width: 100%; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--r-sm); font-size: 13.5px; font-family: 'Roboto Mono', monospace; }
.nominal-input:disabled, .date-input:disabled { background: var(--border-soft); color: var(--faint); }
.terbilang-text { font-size: 10.5px; color: var(--muted); font-style: italic; margin-top: 3px; font-family: 'Roboto', sans-serif; }
.summary-box { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); border-radius: var(--r-lg); padding: 18px; margin-top: 16px; color: #fff; }
.summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.2); font-size: 13px; }
.summary-row:last-child { border-bottom: none; padding-top: 10px; margin-top: 2px; border-top: 2px solid rgba(255,255,255,.3); }
.summary-row .v.total { font-size: 19px; font-weight: 900; }

.pay-card { border: 1.5px solid var(--brand-light); margin-bottom: 20px; }
.pay-card-header { display: flex; align-items: center; gap: 10px; padding: 18px 20px; border-bottom: 1px solid var(--brand-light); background: var(--brand-bg); border-radius: var(--r-lg) var(--r-lg) 0 0; }
.pay-card-header i { font-size: 17px; color: var(--brand); }
.pay-card-header h3 { font-size: 15.5px; font-weight: 800; color: var(--brand-darker); }
.pay-card-body { padding: 20px; }

.history-title { font-size: 14px; font-weight: 800; color: var(--ink); margin: 4px 0 14px; display: flex; align-items: center; justify-content: space-between; }
.history-title span.count { font-size: 12px; font-weight: 600; color: var(--muted); }
</style>

<div class="page-header" style="margin-bottom:16px;">
    <div class="page-title">Pembayaran</div>
    <div class="page-subtitle">Cari siswa untuk langsung input pembayaran.</div>
</div>

<!-- ===================== INPUT PEMBAYARAN (selalu tampil, bukan panel yang perlu dibuka) ===================== -->
<div class="card pay-card">
    <div class="pay-card-header"><i class="fa-solid fa-wallet"></i><h3>Input Pembayaran</h3></div>
    <form id="formPembayaran" action="<?= base_url('admin/pembayaran/store-bulk') ?>" method="POST">
        <div class="pay-card-body">
            <div class="search-box">
                <input type="text" class="input" id="siswa_search" placeholder="Ketik NIS atau nama siswa…" autocomplete="off">
                <div class="search-results" id="siswa_results"></div>
            </div>
            <input type="hidden" name="id_siswa" id="id_siswa" required>
            <div id="selected_siswa"></div>

            <div id="tagihan_section" style="display:none;">
                <div class="section-step"><span class="num">1</span><span class="txt">Tagihan Belum Lunas</span></div>
                <div class="checklist">
                    <div class="checklist-header"><div></div><div>Tagihan</div><div>Nominal Bayar</div><div>Tanggal Bayar</div></div>
                    <div id="tagihan_list"></div>
                </div>
            </div>

            <div id="pembayaran_section" style="display:none;">
                <div class="section-step"><span class="num">2</span><span class="txt">Metode &amp; Catatan</span></div>
                <div class="field">
                    <label class="required">Metode Pembayaran</label>
                    <div class="segmented">
                        <label><input type="radio" name="metode_pembayaran" value="tunai" checked required> 💵 Tunai</label>
                        <label><input type="radio" name="metode_pembayaran" value="transfer" required> 🏦 Transfer</label>
                    </div>
                </div>
                <div class="field" style="margin-bottom:0;">
                    <label>Catatan (opsional)</label>
                    <textarea class="input" name="keterangan" rows="2" placeholder="Contoh: dibayar oleh wali murid langsung"></textarea>
                </div>
                <div class="summary-box" id="summary_box" style="display:none;">
                    <div class="summary-row"><span>Tagihan dipilih</span><span id="summary_count">0</span></div>
                    <div class="summary-row"><span>Total dibayar</span><span class="v total" id="summary_total">Rp 0</span></div>
                </div>
                <div style="display:flex; gap:10px; margin-top:16px;">
                    <button type="button" class="btn btn-secondary" onclick="resetBayarForm()">Reset</button>
                    <button type="submit" class="btn btn-primary" id="btn_submit" disabled style="flex:1;"><i class="fa-solid fa-check-double"></i> Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="history-title">Riwayat Pembayaran <span class="count" id="resultCount">Memuat…</span></div>

<div class="stat-grid">
    <div class="card stat-card"><div class="value" style="color:var(--success);" id="statValid">Rp 0</div><div class="label">Total Valid</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--info);" id="statCount">0</div><div class="label">Transaksi</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--danger);" id="statBatal">Rp 0</div><div class="label">Dibatalkan</div></div>
</div>

<div class="toolbar">
    <div class="search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari NIS, nama, atau nomor kwitansi…"></div>
    <select class="input" id="fTA">
        <option value="">Semua Tahun Ajaran</option>
        <?php foreach ($tahun_ajaran as $ta): ?>
            <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= (string) $filter_tahun_ajaran === (string) $ta['id_tahun_ajaran'] ? 'selected' : '' ?>><?= esc($ta['nama_tahun_ajaran']) ?><?= $ta['status'] === 'aktif' ? ' (Aktif)' : '' ?></option>
        <?php endforeach; ?>
    </select>
    <select class="input" id="fStatus"><option value="">Semua Status</option><option value="valid">Valid</option><option value="dibatalkan">Dibatalkan</option></select>
    <select class="input" id="fMetode"><option value="">Semua Metode</option><option value="tunai">Tunai</option><option value="transfer">Transfer</option></select>
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Tanggal</th><th>Kwitansi</th><th>Siswa</th><th>Tagihan</th><th style="text-align:right;">Nominal</th><th>Metode</th><th>Status</th><th></th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/pembayaran') ?>';
let page = 1;
const PER_PAGE = 15;
let q = '', fTA = '<?= esc($filter_tahun_ajaran ?? '') ?>', fStatus = '', fMetode = '';
let fetchToken = 0;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function fmtDateTime(d) { const dt = new Date(d.replace(' ', 'T')); return dt.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' · ' + dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }
function fmtDateShort(d) { const dt = new Date(d.replace(' ', 'T')); return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }); }
function metodeBadge(m) { return m === 'tunai' ? '<span class="badge badge-success">Tunai</span>' : '<span class="badge badge-info">Transfer</span>'; }
function statusBadge(s) { return s === 'valid' ? '<span class="badge badge-success">Valid</span>' : '<span class="badge badge-danger">Batal</span>'; }

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 5; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:70%;"></div><div class="skeleton-bar" style="width:40%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="8" style="padding:0;">' + html + '</td></tr>';
}
async function loadPage() {
    const myToken = ++fetchToken;
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';

    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (q) params.set('keyword', q);
    if (fTA) params.set('filter_tahun_ajaran', fTA);
    if (fStatus) params.set('filter_status', fStatus);
    if (fMetode) params.set('filter_metode', fMetode);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) {
        document.getElementById('resultCount').textContent = 'Gagal memuat data.';
        return;
    }
    if (myToken !== fetchToken) return; // respons lama menyusul terlambat, abaikan

    document.getElementById('statValid').textContent = 'Rp ' + fmt(data.stats.total_valid);
    document.getElementById('statCount').textContent = data.stats.count_valid;
    document.getElementById('statBatal').textContent = 'Rp ' + fmt(data.stats.total_batal);
    document.getElementById('resultCount').textContent = data.total + ' transaksi' + (q || fStatus || fMetode ? ' (difilter)' : '');

    const rows = data.rows;
    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (rows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-receipt"></i><p>Tidak ada transaksi yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';
        tbody.innerHTML = rows.map(p => `
            <tr>
                <td>${fmtDateTime(p.tanggal_bayar)}</td>
                <td><span class="kwitansi-code">${esc(p.nomor_kwitansi)}</span></td>
                <td><div style="font-weight:700;color:var(--ink);">${esc(p.nama_siswa)}</div><div style="font-size:11.5px;color:var(--muted);">NIS ${esc(p.nis)}</div></td>
                <td>${esc(p.nama_tagihan)}</td>
                <td style="text-align:right; font-family:'Roboto Mono',monospace; font-weight:700;">Rp ${fmt(p.nominal_bayar)}</td>
                <td>${metodeBadge(p.metode_pembayaran)}</td>
                <td>${statusBadge(p.status_pembayaran)}</td>
                <td style="text-align:right;"><a class="icon-action" href="${BASE_URL}/admin/pembayaran/detail/${p.id_pembayaran}" title="Detail"><i class="fa-solid fa-eye"></i></a></td>
            </tr>`).join('');
        cardList.innerHTML = rows.map(p => `
            <a class="card bayar-card" href="${BASE_URL}/admin/pembayaran/detail/${p.id_pembayaran}" style="text-decoration:none;">
                <div class="body">
                    <div class="name">${esc(p.nama_siswa)}</div>
                    <div class="meta">${esc(p.nama_tagihan)} · ${fmtDateShort(p.tanggal_bayar)}</div>
                    <div style="margin-top:5px;">${statusBadge(p.status_pembayaran)} ${metodeBadge(p.metode_pembayaran)}</div>
                </div>
                <div class="amount" style="color:${p.status_pembayaran === 'valid' ? 'var(--success)' : 'var(--danger)'};">Rp ${fmt(p.nominal_bayar)}</div>
            </a>`).join('');
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
document.getElementById('q').addEventListener('input', function () {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => { q = this.value.trim(); page = 1; loadPage(); }, 350);
});
document.getElementById('fTA').addEventListener('change', function () { fTA = this.value; page = 1; loadPage(); });
document.getElementById('fStatus').addEventListener('change', function () { fStatus = this.value; page = 1; loadPage(); });
document.getElementById('fMetode').addEventListener('change', function () { fMetode = this.value; page = 1; loadPage(); });

// ===================== MODAL: Input Pembayaran (logic dipertahankan dari versi sebelumnya) =====================
let selectedTagihan = [];

function resetBayarForm() {
    document.getElementById('formPembayaran').reset();
    document.getElementById('id_siswa').value = '';
    document.getElementById('selected_siswa').innerHTML = '';
    document.getElementById('tagihan_section').style.display = 'none';
    document.getElementById('pembayaran_section').style.display = 'none';
    document.getElementById('btn_submit').disabled = true;
    selectedTagihan = [];
}
function openCreateModal() { resetBayarForm(); document.getElementById('siswa_search')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); document.getElementById('siswa_search')?.focus(); }

// Jaga-jaga: kalau footer.php di server belum sempat diperbarui (openSearchDropdown
// dkk. jadi belum ada), definisikan versi cadangannya di sini supaya pencarian tetap
// berfungsi tanpa harus bergantung file lain.
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

let searchTimeout;
document.getElementById('siswa_search').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const keyword = this.value;
    const inputEl = this;
    if (keyword.length < 2) { closeSearchDropdown(document.getElementById('siswa_results')); return; }
    searchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(data => {
                const html = data.length === 0
                    ? '<div class="search-result-item" style="color:var(--faint);">Tidak ada hasil.</div>'
                    : data.map(s => `<div class="search-result-item" onclick='selectSiswaObj(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${esc(s.nis)} · ${esc(s.nama_kelas || 'Belum dikelas')}</small></div>`).join('');
                openSearchDropdown(inputEl, document.getElementById('siswa_results'), html);
            });
    }, 300);
});
document.addEventListener('click', function (e) { if (!e.target.closest('.search-box') && !e.target.closest('#siswa_results')) closeSearchDropdown(document.getElementById('siswa_results')); });

function selectSiswaObj(siswa) {
    selectedTagihan = [];
    document.getElementById('id_siswa').value = siswa.id_siswa;
    document.getElementById('siswa_search').value = '';
    closeSearchDropdown(document.getElementById('siswa_results'));
    document.getElementById('selected_siswa').innerHTML = `
        <div class="selected-siswa-box">
            <div><strong>${esc(siswa.nama_lengkap)}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${esc(siswa.nis)}</small></div>
            <button type="button" class="icon-action danger" onclick="resetBayarForm()" title="Ganti siswa"><i class="fa-solid fa-xmark"></i></button>
        </div>`;
    loadTagihanUntukBayar(siswa.id_siswa);
}

function loadTagihanUntukBayar(idSiswa) {
    fetch(BASE_URL + '/admin/pembayaran/get-tagihan-by-siswa?id_siswa=' + idSiswa)
        .then(r => r.json())
        .then(data => {
            const tagihanList = document.getElementById('tagihan_list');
            data.sort((a, b) => {
                const nameA = a.nama_tagihan.toLowerCase(), nameB = b.nama_tagihan.toLowerCase();
                const isSppA = nameA.includes('spp'), isSppB = nameB.includes('spp');
                if (!isSppA && isSppB) return -1;
                if (isSppA && !isSppB) return 1;
                if (isSppA && isSppB) {
                    const order = { juli: 1, jul: 1, agustus: 2, agu: 2, september: 3, sep: 3, oktober: 4, okt: 4, november: 5, nov: 5, desember: 6, des: 6, januari: 7, jan: 7, februari: 8, feb: 8, maret: 9, mar: 9, april: 10, apr: 10, mei: 11, may: 11, juni: 12, jun: 12 };
                    const val = t => { for (const [k, v] of Object.entries(order)) if (t.includes(k)) return v; return 99; };
                    return val(nameA) - val(nameB);
                }
                return 0;
            });

            if (data.length === 0) {
                tagihanList.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-check"></i><p>Tidak ada tagihan yang belum lunas.</p></div>';
                document.getElementById('tagihan_section').style.display = 'block';
                setTimeout(() => scrollIntoModal(document.getElementById('tagihan_section')), 50);
                return;
            }

            tagihanList.innerHTML = data.map((tagihan, index) => {
                const today = new Date().toISOString().split('T')[0];
                return `
                    <div class="checklist-item" id="item_${index}">
                        <input type="checkbox" id="check_${index}" onchange='toggleTagihan(${index}, ${JSON.stringify(tagihan)})'>
                        <div class="tagihan-info">
                            <strong>${esc(tagihan.nama_tagihan)}</strong>
                            ${tagihan.bulan_tagihan ? '<span class="badge badge-info">Bulan ' + tagihan.bulan_tagihan + '</span> ' : ''}
                            <span class="badge badge-neutral">${esc(tagihan.nama_tahun_ajaran)}</span>
                            <div class="sisa">Sisa: ${formatRupiah(tagihan.sisa_tagihan)}</div>
                        </div>
                        <div class="nominal-cell">
                            <input type="number" class="nominal-input" id="nominal_${index}" name="nominal[${tagihan.id_tagihan}]" value="${tagihan.sisa_tagihan}" min="0" max="${tagihan.sisa_tagihan}" oninput="updateNominalInput(${index})" disabled>
                            <div id="terbilang_${index}" class="terbilang-text"></div>
                        </div>
                        <div class="date-cell">
                            <input type="date" class="date-input" id="tanggal_${index}" name="tanggal[${tagihan.id_tagihan}]" value="${today}" max="${today}" disabled>
                        </div>
                    </div>`;
            }).join('');

            document.getElementById('tagihan_section').style.display = 'block';
            document.getElementById('pembayaran_section').style.display = 'block';
            setTimeout(() => scrollIntoModal(document.getElementById('tagihan_section')), 50);
        });
}

function updateTerbilangDisplay(index) {
    const val = parseInt(document.getElementById('nominal_' + index).value) || 0;
    document.getElementById('terbilang_' + index).textContent = val > 0 ? (terbilang(val).trim() + ' Rupiah') : '';
}
function updateNominalInput(index) { updateTerbilangDisplay(index); updateNominal(index); }

function toggleTagihan(index, tagihan) {
    const checkbox = document.getElementById('check_' + index);
    const item = document.getElementById('item_' + index);
    const nominalInput = document.getElementById('nominal_' + index);
    const tanggalInput = document.getElementById('tanggal_' + index);
    if (checkbox.checked) {
        item.classList.add('checked'); nominalInput.disabled = false; tanggalInput.disabled = false;
        updateTerbilangDisplay(index);
        selectedTagihan.push({ id_tagihan: tagihan.id_tagihan, nominal: parseInt(nominalInput.value), tanggal: tanggalInput.value });
    } else {
        item.classList.remove('checked'); nominalInput.disabled = true; tanggalInput.disabled = true;
        document.getElementById('terbilang_' + index).textContent = '';
        selectedTagihan = selectedTagihan.filter(t => t.id_tagihan !== tagihan.id_tagihan);
    }
    updateBayarSummary();
}
function updateNominal(index) {
    const nominalInput = document.getElementById('nominal_' + index);
    const checkbox = document.getElementById('check_' + index);
    if (checkbox.checked) {
        const found = selectedTagihan.find(t => document.querySelector(`input[name="nominal[${t.id_tagihan}]"]`) === nominalInput);
        if (found) found.nominal = parseInt(nominalInput.value) || 0;
        updateBayarSummary();
    }
}
function updateBayarSummary() {
    const count = selectedTagihan.length;
    const total = selectedTagihan.reduce((sum, t) => sum + t.nominal, 0);
    document.getElementById('summary_count').textContent = count;
    document.getElementById('summary_total').textContent = 'Rp ' + formatRupiah(total, '');
    document.getElementById('summary_box').style.display = count > 0 ? 'block' : 'none';
    document.getElementById('btn_submit').disabled = count === 0;
}
document.getElementById('formPembayaran').addEventListener('submit', function (e) {
    if (selectedTagihan.length === 0) { e.preventDefault(); alert('Pilih minimal 1 tagihan untuk dibayar.'); }
});

// ===================== Auto-isi dari hash (link lama / tombol "Bayar" di drawer Siswa) =====================
function handleHash() {
    const h = location.hash;
    if (h === '#bayar') { openCreateModal(); }
    else if (h.startsWith('#bayar-')) {
        const idSiswa = h.replace('#bayar-', '');
        resetBayarForm();
        fetch(BASE_URL + '/admin/siswa/detail/' + idSiswa, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => { if (data.siswa) selectSiswaObj(data.siswa); });
    }
}
if (location.hash) handleHash();

loadPage();
</script>

<?= $this->include('admin/layouts/footer') ?>