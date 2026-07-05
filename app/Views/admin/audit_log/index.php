<?= $this->include('admin/layouts/header') ?>

<style>
.toolbar-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 14px; }
@media (min-width: 768px) { .toolbar-grid { grid-template-columns: repeat(5, 1fr); } }
.toolbar-grid .search-wrap { position: relative; grid-column: 1 / -1; }
@media (min-width: 768px) { .toolbar-grid .search-wrap { grid-column: span 2; } }
.toolbar-grid .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar-grid .search-wrap input { padding-left: 38px; }

.aksi-badge-create { background: var(--success-bg); color: var(--success); border-color: var(--success-border); }
.aksi-badge-update { background: var(--warning-bg); color: var(--warning); border-color: var(--warning-border); }
.aksi-badge-delete { background: var(--danger-bg); color: var(--danger); border-color: var(--danger-border); }
.aksi-badge-other { background: var(--info-bg); color: var(--info); border-color: var(--info-border); }

.log-card { padding: 14px; }
.log-card .top { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px; }
.log-card .who { font-size: 13px; font-weight: 700; color: var(--ink); }
.log-card .ket { font-size: 12.5px; color: var(--body); margin-top: 4px; }
.log-card .time { font-size: 11px; color: var(--faint); margin-top: 6px; }

.code-block { background: #0f172a; color: #a7f3d0; padding: 14px; border-radius: var(--r-sm); font-family: 'Roboto Mono', monospace; font-size: 11.5px; white-space: pre-wrap; word-break: break-all; overflow-x: auto; max-height: 300px; overflow-y: auto; }
</style>

<div class="page-header" style="margin-bottom:14px;">
    <div class="page-title">Audit Log</div>
    <div class="page-subtitle" id="resultCount">Memuat…</div>
</div>

<div class="toolbar-grid">
    <div class="search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" class="input" id="q" placeholder="Cari user atau keterangan…"></div>
    <select class="input" id="fModul"><option value="">Semua Modul</option><?php foreach ($modules as $m): ?><option value="<?= esc($m['modul']) ?>"><?= esc(ucfirst($m['modul'])) ?></option><?php endforeach; ?></select>
    <select class="input" id="fAksi"><option value="">Semua Aksi</option><?php foreach ($actions as $a): ?><option value="<?= esc($a['aksi']) ?>"><?= esc(ucfirst($a['aksi'])) ?></option><?php endforeach; ?></select>
    <input type="date" class="input" id="fMulai" title="Dari tanggal">
    <input type="date" class="input" id="fSelesai" title="Sampai tanggal">
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Waktu</th><th>User</th><th>Modul</th><th>Aksi</th><th>Keterangan</th><th></th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:10px;"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<!-- ===================== DRAWER: Detail Log ===================== -->
<div class="inline-panel inline-panel-narrow" id="logPanel">
    <div class="inline-panel-header"><button type="button" class="inline-panel-close" onclick="closePanel('logPanel')"><i class="fa-solid fa-xmark"></i></button><h3>Detail Aktivitas</h3></div>
    <div class="inline-panel-body" id="drawerBody"></div>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/audit-log') ?>';
let page = 1;
const PER_PAGE = 30;
let q = '', fModul = '', fAksi = '', fMulai = '', fSelesai = '';
let fetchToken = 0;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmtDateTime(d) { const dt = new Date(d.replace(' ', 'T')); return dt.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' · ' + dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }
function aksiBadge(a) {
    const cls = a === 'create' ? 'aksi-badge-create' : (a === 'delete' || a === 'bulk_delete') ? 'aksi-badge-delete' : (a === 'update' ? 'aksi-badge-update' : 'aksi-badge-other');
    return `<span class="badge ${cls}">${esc(a.toUpperCase())}</span>`;
}

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 6; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:60%;"></div><div class="skeleton-bar" style="width:30%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="padding:0;">' + html + '</td></tr>';
}

async function loadPage() {
    const myToken = ++fetchToken;
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';

    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (q) params.set('keyword', q);
    if (fModul) params.set('modul', fModul);
    if (fAksi) params.set('aksi', fAksi);
    if (fMulai) params.set('tanggal_mulai', fMulai);
    if (fSelesai) params.set('tanggal_selesai', fSelesai);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) { document.getElementById('resultCount').textContent = 'Gagal memuat data.'; return; }
    if (myToken !== fetchToken) return;

    document.getElementById('resultCount').textContent = data.total + ' aktivitas tercatat';

    const rows = data.rows;
    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (rows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><p>Tidak ada aktivitas yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';
        tbody.innerHTML = rows.map(l => `
            <tr>
                <td style="white-space:nowrap; font-size:12.5px;">${fmtDateTime(l.created_at)}</td>
                <td style="font-weight:700; color:var(--ink);">${esc(l.nama_lengkap || 'Sistem')}</td>
                <td><span class="badge badge-neutral">${esc((l.modul || '-').toUpperCase())}</span></td>
                <td>${aksiBadge(l.aksi)}</td>
                <td style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${esc(l.keterangan)}</td>
                <td style="text-align:right;"><button class="icon-action" title="Detail" onclick="openDetailDrawer(${l.id_log})"><i class="fa-solid fa-eye"></i></button></td>
            </tr>`).join('');
        cardList.innerHTML = rows.map(l => `
            <div class="card log-card" onclick="openDetailDrawer(${l.id_log})">
                <div class="top"><span class="who">${esc(l.nama_lengkap || 'Sistem')}</span>${aksiBadge(l.aksi)}</div>
                <div class="ket">${esc(l.keterangan)}</div>
                <div class="time"><span class="badge badge-neutral">${esc((l.modul || '-').toUpperCase())}</span> · ${fmtDateTime(l.created_at)}</div>
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
document.getElementById('fModul').addEventListener('change', function () { fModul = this.value; page = 1; loadPage(); });
document.getElementById('fAksi').addEventListener('change', function () { fAksi = this.value; page = 1; loadPage(); });
document.getElementById('fMulai').addEventListener('change', function () { fMulai = this.value; page = 1; loadPage(); });
document.getElementById('fSelesai').addEventListener('change', function () { fSelesai = this.value; page = 1; loadPage(); });

// ===================== DRAWER: Detail =====================
function openDetailDrawer(id) {
    openPanel('logPanel');
    document.getElementById('drawerBody').innerHTML = '<div class="drawer-skeleton"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i><span>Memuat…</span></div>';

    fetch(BASE_URL + '/admin/audit-log/detail/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.error) { document.getElementById('drawerBody').innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>' + esc(data.error) + '</p></div>'; return; }
            renderLogDrawer(data.log);
        })
        .catch(() => { document.getElementById('drawerBody').innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Gagal memuat detail.</p></div>'; });
}

function prettyJson(raw) {
    if (!raw) return null;
    try { return JSON.stringify(JSON.parse(raw), null, 2); } catch (e) { return raw; }
}

function renderLogDrawer(l) {
    let html = `
        <div class="card" style="padding:4px 16px; margin-bottom:18px;">
            <div class="info-row"><span class="k">Waktu</span><span class="v">${fmtDateTime(l.created_at)}</span></div>
            <div class="info-row"><span class="k">User</span><span class="v">${esc(l.nama_lengkap || 'Sistem')}</span></div>
            <div class="info-row"><span class="k">Modul</span><span class="v">${esc((l.modul || '-').toUpperCase())}</span></div>
            <div class="info-row"><span class="k">Aksi</span><span class="v">${aksiBadge(l.aksi)}</span></div>
            <div class="info-row"><span class="k">IP Address</span><span class="v mono">${esc(l.ip_address || '-')}</span></div>
        </div>
        <div class="eyebrow" style="margin-bottom:8px;">Keterangan</div>
        <div class="card" style="padding:14px; margin-bottom:18px; font-size:13px;">${esc(l.keterangan)}</div>
    `;
    const dataLama = prettyJson(l.data_lama);
    const dataBaru = prettyJson(l.data_baru);
    if (dataLama) html += `<div class="eyebrow" style="margin-bottom:8px;">Data Sebelum</div><div class="code-block" style="margin-bottom:18px;">${esc(dataLama)}</div>`;
    if (dataBaru) html += `<div class="eyebrow" style="margin-bottom:8px;">Data Sesudah</div><div class="code-block" style="margin-bottom:18px;">${esc(dataBaru)}</div>`;
    if (l.user_agent) html += `<div class="eyebrow" style="margin-bottom:8px;">Perangkat</div><div class="card" style="padding:12px 14px; font-size:11.5px; color:var(--muted); word-break:break-all;">${esc(l.user_agent)}</div>`;

    document.getElementById('drawerBody').innerHTML = html;
}

function handleHash() { const h = location.hash; if (h.startsWith('#detail-')) openDetailDrawer(parseInt(h.replace('#detail-', ''), 10)); }

loadPage();
if (location.hash) handleHash();
</script>

<?= $this->include('admin/layouts/footer') ?>
