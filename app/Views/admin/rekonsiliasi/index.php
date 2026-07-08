<?= $this->include('admin/layouts/header') ?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 16px; }
@media (min-width: 640px) { .stat-grid { grid-template-columns: repeat(4, 1fr); gap: 16px; } }
.stat-card { padding: 14px 16px; }
.stat-card .value { font-size: 19px; font-weight: 900; color: var(--ink); }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 768px) { .toolbar { flex-direction: row; align-items: center; } }
.toolbar select { width: 100%; }
@media (min-width: 768px) { .toolbar select { width: 200px; flex-shrink: 0; } }
.toolbar .spacer { flex: 1; }

.badge-perlu-sync { background: #fff7ed; color: #c2410c; border-color: #fed7aa; font-size: 10px; margin-left: 6px; }

.trx-card { padding: 14px; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.trx-card .info { flex: 1; min-width: 200px; }
.trx-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.trx-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.trx-card .amount { font-size: 14px; font-weight: 800; font-family: 'Roboto Mono', monospace; color: var(--ink); }
.trx-card .actions { display: flex; gap: 6px; }

.progress-modal-track { height: 8px; border-radius: 999px; background: var(--border-soft); overflow: hidden; margin: 14px 0; }
.progress-modal-fill { height: 100%; background: var(--brand); border-radius: 999px; transition: width .2s ease; width: 0%; }
.sync-result-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border-soft); font-size: 12.5px; }
.sync-result-row:last-child { border-bottom: none; }

.log-row { padding: 10px 0; border-bottom: 1px solid var(--border-soft); font-size: 12px; }
.log-row:last-child { border-bottom: none; }
.log-row .src { font-weight: 700; }
.log-row .src.WEBHOOK { color: var(--brand); }
.log-row .src.MANUAL { color: var(--info); }
.log-row .src.CRON { color: #7e22ce; }
</style>

<div class="page-header" style="margin-bottom:16px;">
    <div class="page-title">Rekonsiliasi Pembayaran</div>
    <div class="page-subtitle">Pantau &amp; sinkronkan status transaksi pembayaran online (Xendit) secara manual.</div>
</div>

<div class="stat-grid">
    <div class="card stat-card"><div class="value" style="color:var(--warning);"><?= number_format($ringkasan['pending']) ?></div><div class="label">Pending</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--success);"><?= number_format($ringkasan['paid_hari_ini']) ?></div><div class="label">Lunas Hari Ini</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--muted);"><?= number_format($ringkasan['expired']) ?></div><div class="label">Kedaluwarsa</div></div>
    <div class="card stat-card"><div class="value" style="color:var(--danger);"><?= number_format($ringkasan['failed']) ?></div><div class="label">Gagal</div></div>
</div>

<div class="toolbar">
    <select class="input" id="fStatus">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="paid">Lunas</option>
        <option value="expired">Kedaluwarsa</option>
        <option value="failed">Gagal</option>
    </select>
    <div class="spacer"></div>
    <button class="btn btn-primary" onclick="openSyncAllPanel()"><i class="fa-solid fa-arrows-rotate"></i> Sinkronkan Semua Pending</button>
</div>

<!-- ===================== PANEL: Sinkronkan Semua (inline) ===================== -->
<div class="inline-panel" id="syncAllPanel">
    <div class="inline-panel-header"><h3>Sinkronkan Semua Pending</h3><button type="button" class="inline-panel-close" onclick="closePanel('syncAllPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="inline-panel-body">
        <div id="syncAllIntro">
            <p style="font-size:13px; color:var(--body);">Ini akan mengecek status terbaru ke Xendit untuk <strong id="syncAllCount">...</strong> transaksi yang masih pending, satu per satu. Proses berjalan di halaman ini, jangan ditutup sampai selesai.</p>
        </div>
        <div id="syncAllProgress" style="display:none;">
            <div style="display:flex; justify-content:space-between; font-size:12.5px; color:var(--muted); margin-bottom:4px;">
                <span id="syncAllProgressText">Memproses 0 dari 0</span>
                <span id="syncAllProgressPct">0%</span>
            </div>
            <div class="progress-modal-track"><div class="progress-modal-fill" id="syncAllProgressFill"></div></div>
        </div>
        <div id="syncAllResult" style="display:none;">
            <div class="sync-result-row"><span>Berhasil diperiksa</span><strong id="resOk">0</strong></div>
            <div class="sync-result-row"><span style="color:var(--success);">Berubah jadi lunas/kedaluwarsa</span><strong id="resChanged" style="color:var(--success);">0</strong></div>
            <div class="sync-result-row"><span style="color:var(--danger);">Gagal diperiksa</span><strong id="resFailed" style="color:var(--danger);">0</strong></div>
        </div>
    </div>
    <div class="inline-panel-footer">
        <button type="button" class="btn btn-secondary" onclick="closePanel('syncAllPanel')" id="btnTutupSyncAll">Tutup</button>
        <button type="button" class="btn btn-primary" onclick="mulaiSyncSemua()" id="btnMulaiSyncSemua">Mulai Sinkronkan</button>
    </div>
</div>

<!-- ===================== PANEL: Lihat Log (inline, sempit) ===================== -->
<div class="inline-panel inline-panel-narrow" id="logPanel">
    <div class="inline-panel-header"><h3>Riwayat Log</h3><button type="button" class="inline-panel-close" onclick="closePanel('logPanel')"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="inline-panel-body" id="logPanelBody">
        <div class="drawer-skeleton"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i><span>Memuat…</span></div>
    </div>
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Siswa</th><th>External ID</th><th>Tanggal</th><th style="text-align:right;">Nominal</th><th>Status</th><th style="text-align:right;">Aksi</th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:8px;"></div>
    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
let page = 1;
const PER_PAGE = 15;
let fStatus = '<?= esc($filter_status ?? '') ?>';
if (fStatus) document.getElementById('fStatus').value = fStatus;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function fmtDateTime(d) { if (!d) return '—'; const dt = new Date(d.replace(' ', 'T')); return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ' ' + dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }

function statusBadge(t) {
    const map = {
        pending: '<span class="badge badge-warning">Pending</span>',
        paid: '<span class="badge badge-success">Lunas</span>',
        expired: '<span class="badge badge-neutral">Kedaluwarsa</span>',
        failed: '<span class="badge badge-danger">Gagal</span>',
    };
    let html = map[t.status] || t.status;
    if (t.status === 'pending' && !t.last_synced_at) {
        html += '<span class="badge badge-perlu-sync">Perlu Sync</span>';
    }
    return html;
}

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 5; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:60%;"></div><div class="skeleton-bar" style="width:35%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="padding:0;">' + html + '</td></tr>';
}

async function loadPage() {
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';
    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (fStatus) params.set('status', fStatus);

    let data;
    try {
        const res = await fetch(BASE_URL + '/admin/rekonsiliasi?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) {
            const raw = await res.text();
            throw new Error('Server merespons status ' + res.status + '. Isi respons: ' + raw.substring(0, 300));
        }
        data = await res.json();
    } catch (e) {
        console.error('Gagal memuat daftar rekonsiliasi:', e);
        document.getElementById('cardList').innerHTML = '';
        const emptyState = document.getElementById('emptyState');
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation" style="color:var(--danger);"></i><p>Gagal memuat data. Buka Console browser (F12) untuk detail errornya.</p></div>';
        return;
    }

    const cardList = document.getElementById('cardList');
    const tableBody = document.getElementById('tableBody');
    const emptyState = document.getElementById('emptyState');

    if (data.rows.length === 0) {
        cardList.innerHTML = '';
        tableBody.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-receipt"></i><p>Tidak ada transaksi yang cocok.</p></div>';
    } else {
        emptyState.style.display = 'none';

        const actionBtns = (t) => `
            ${t.status === 'pending' ? `<button class="icon-action" title="Sinkronkan" onclick="syncSatu(${t.id_transaction})" id="syncBtn${t.id_transaction}"><i class="fa-solid fa-arrows-rotate"></i></button>` : ''}
            <button class="icon-action" title="Lihat Log" onclick="lihatLog(${t.id_transaction})"><i class="fa-solid fa-clock-rotate-left"></i></button>
        `;

        tableBody.innerHTML = data.rows.map(t => `
            <tr id="trxRowTable${t.id_transaction}">
                <td><div style="font-weight:700; color:var(--ink);">${esc(t.nama_lengkap || '—')}</div><div style="font-size:11.5px; color:var(--muted);">NIS ${esc(t.nis || '-')}</div></td>
                <td class="mono" style="font-size:12px;">${esc(t.external_id)}</td>
                <td style="font-size:12.5px;">${fmtDateTime(t.created_at)}</td>
                <td style="text-align:right; font-family:'Roboto Mono',monospace; font-weight:700;">Rp ${fmt(t.total_amount)}</td>
                <td>${statusBadge(t)}</td>
                <td style="text-align:right; white-space:nowrap;">${actionBtns(t)}</td>
            </tr>
        `).join('');

        cardList.innerHTML = data.rows.map(t => `
            <div class="card trx-card" id="trxRow${t.id_transaction}">
                <div class="info">
                    <div class="name">${esc(t.nama_lengkap || '—')} <span style="font-weight:400; color:var(--muted); font-size:11.5px;">NIS ${esc(t.nis || '-')}</span></div>
                    <div class="meta">${esc(t.external_id)} · ${fmtDateTime(t.created_at)} ${statusBadge(t)}</div>
                </div>
                <div class="amount">Rp ${fmt(t.total_amount)}</div>
                <div class="actions">${actionBtns(t)}</div>
            </div>
        `).join('');
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
document.getElementById('fStatus').addEventListener('change', function () { fStatus = this.value; page = 1; loadPage(); });

// ===================== Sync 1 transaksi =====================
function syncSatu(id) {
    // Ada 2 elemen dengan id yang sama (versi tabel desktop & versi kartu mobile,
    // cuma salah satunya yang terlihat tergantung lebar layar) -- querySelectorAll
    // dengan attribute selector supaya KEDUANYA ter-update, bukan cuma yang pertama.
    const btns = document.querySelectorAll('[id="syncBtn' + id + '"]');
    const iconOriginal = btns.length ? btns[0].innerHTML : '';
    btns.forEach(b => { b.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; b.disabled = true; });

    fetch(BASE_URL + '/admin/rekonsiliasi/sync/' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            showToast(data.success ? (data.changed ? 'Status diperbarui: ' + data.message : data.message) : data.message, data.success ? (data.changed ? 'success' : 'info') : 'danger');
            loadPage();
        })
        .catch(() => {
            showToast('Gagal menghubungi server.', 'danger');
            btns.forEach(b => { b.innerHTML = iconOriginal; b.disabled = false; });
        });
}

// ===================== Sync semua pending =====================
let daftarIdPending = [];
function openSyncAllPanel() {
    document.getElementById('syncAllIntro').style.display = 'block';
    document.getElementById('syncAllProgress').style.display = 'none';
    document.getElementById('syncAllResult').style.display = 'none';
    document.getElementById('btnMulaiSyncSemua').style.display = 'inline-flex';
    document.getElementById('syncAllCount').textContent = '...';

    openPanel('syncAllPanel');

    fetch(BASE_URL + '/admin/rekonsiliasi/pending-ids', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            daftarIdPending = data.ids || [];
            document.getElementById('syncAllCount').textContent = daftarIdPending.length;
            if (daftarIdPending.length === 0) {
                document.getElementById('btnMulaiSyncSemua').style.display = 'none';
            }
        });
}

async function mulaiSyncSemua() {
    if (daftarIdPending.length === 0) return;

    document.getElementById('syncAllIntro').style.display = 'none';
    document.getElementById('btnMulaiSyncSemua').style.display = 'none';
    document.getElementById('btnTutupSyncAll').disabled = true;
    document.getElementById('syncAllProgress').style.display = 'block';

    let ok = 0, changed = 0, failed = 0;
    const totalItem = daftarIdPending.length;

    for (let i = 0; i < totalItem; i++) {
        const id = daftarIdPending[i];
        try {
            const formData = new FormData();
            formData.append('id_transaction', id);
            const res = await fetch(BASE_URL + '/admin/rekonsiliasi/sync-batch', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (data.success) { ok++; if (data.changed) changed++; } else { failed++; }
        } catch (e) {
            failed++;
        }

        const persen = Math.round(((i + 1) / totalItem) * 100);
        document.getElementById('syncAllProgressText').textContent = `Memproses ${i + 1} dari ${totalItem}`;
        document.getElementById('syncAllProgressPct').textContent = persen + '%';
        document.getElementById('syncAllProgressFill').style.width = persen + '%';
    }

    document.getElementById('syncAllProgress').style.display = 'none';
    document.getElementById('syncAllResult').style.display = 'block';
    document.getElementById('resOk').textContent = ok;
    document.getElementById('resChanged').textContent = changed;
    document.getElementById('resFailed').textContent = failed;
    document.getElementById('btnTutupSyncAll').disabled = false;

    loadPage();
}

// ===================== Lihat Log =====================
function lihatLog(id) {
    openPanel('logPanel');
    const body = document.getElementById('logPanelBody');
    body.innerHTML = '<div class="drawer-skeleton"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i><span>Memuat…</span></div>';

    fetch(BASE_URL + '/admin/rekonsiliasi/logs/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.logs || data.logs.length === 0) {
                body.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Belum ada riwayat log untuk transaksi ini.</p></div>';
                return;
            }
            body.innerHTML = data.logs.map(l => `
                <div class="log-row">
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                        <span class="src ${esc(l.source)}">${esc(l.source)}</span>
                        <span style="color:var(--muted);">${fmtDateTime(l.created_at)}</span>
                    </div>
                    <div>${esc(l.message || '-')}</div>
                    ${l.old_status || l.new_status ? `<div style="color:var(--muted); margin-top:2px;">${esc(l.old_status || '-')} → ${esc(l.new_status || '-')}</div>` : ''}
                </div>
            `).join('');
        })
        .catch(() => { body.innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Gagal memuat log.</p></div>'; });
}

loadPage();
</script>

<?= $this->include('admin/layouts/footer') ?>
