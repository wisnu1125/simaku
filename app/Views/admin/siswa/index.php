<?= $this->include('admin/layouts/header') ?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
@media (min-width: 640px) { .stat-grid { gap: 16px; } }
.stat-card { padding: 14px 16px; }
@media (min-width: 640px) { .stat-card { padding: 18px 20px; } }
.stat-card .value { font-size: 19px; font-weight: 900; color: var(--ink); }
@media (min-width: 640px) { .stat-card .value { font-size: 24px; } }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

.toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
@media (min-width: 768px) { .toolbar { flex-direction: row; } }
.toolbar .search-wrap { position: relative; flex: 1; }
.toolbar .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); }
.toolbar .search-wrap input { padding-left: 38px; }
.toolbar select { width: 100%; }
@media (min-width: 768px) { .toolbar select { width: 170px; flex-shrink: 0; } }



/* --- Mobile card list --- */
.siswa-card { padding: 14px; display: flex; align-items: center; gap: 12px; }
.siswa-card .avatar { width: 42px; height: 42px; border-radius: 50%; background: var(--brand-bg); color: var(--brand-darker); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
.siswa-card .body { flex: 1; min-width: 0; }
.siswa-card .name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.siswa-card .meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.siswa-card .actions { display: flex; gap: 6px; flex-shrink: 0; }

.pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-soft); font-size: 12.5px; color: var(--muted); }
.pager .btns { display: flex; gap: 6px; }



.detail-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 700px) { .detail-grid { grid-template-columns: 1fr 1fr; align-items: start; } }
.detail-col { display: flex; flex-direction: column; gap: 16px; }
</style>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:10px;">
    <div>
        <div class="page-title">Data Siswa</div>
        <div class="page-subtitle" id="resultCount">Memuat…</div>
    </div>
    <div style="display:flex; gap:8px;">
        <button class="btn btn-secondary" onclick="openImportModal()"><i class="fa-solid fa-file-arrow-up"></i> <span class="hide-xs">Impor Excel</span></button>
        <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah Siswa</span></button>
    </div>
</div>

<!-- ===================== PANEL: Tambah / Edit Siswa (inline) ===================== -->
<div class="inline-panel" id="siswaPanel">
    <div class="inline-panel-header">
        <h3 id="siswaModalTitle">Tambah Siswa</h3>
        <button type="button" class="inline-panel-close" onclick="closePanel('siswaPanel')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="siswaForm" action="<?= base_url('admin/siswa/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="inline-panel-body">

            <div class="eyebrow" style="margin-bottom:10px;">Identitas Siswa</div>
            <div class="field-row">
                <div class="field">
                    <label class="required">NIS</label>
                    <input class="input <?= isset($errors['nis']) ? 'is-invalid' : '' ?>" name="nis" id="f_nis" placeholder="Contoh: 2024001" value="<?= esc(old('nis', '')) ?>" required>
                    <?php if (isset($errors['nis'])): ?><div class="field-error"><?= $errors['nis'] ?></div><?php endif; ?>
                </div>
                <div class="field">
                    <label>NISN</label>
                    <input class="input" name="nisn" id="f_nisn" placeholder="Nomor Induk Siswa Nasional" value="<?= esc(old('nisn', '')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="required">Nama Lengkap</label>
                <input class="input <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" name="nama_lengkap" id="f_nama_lengkap" placeholder="Sesuai ijazah" value="<?= esc(old('nama_lengkap', '')) ?>" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="required">Tanggal Lahir</label>
                    <input type="text" class="input" name="tanggal_lahir" id="f_tanggal_lahir" placeholder="DD-MM-YYYY" pattern="\d{2}-\d{2}-\d{4}" inputmode="numeric" value="<?= esc(old('tanggal_lahir', '')) ?>" required>
                    <div class="field-hint">Contoh: 15-08-2012</div>
                </div>
                <div class="field">
                    <label class="required">Jenis Kelamin</label>
                    <div class="segmented">
                        <label><input type="radio" name="jenis_kelamin" value="L" id="f_jk_l"> Laki-laki</label>
                        <label><input type="radio" name="jenis_kelamin" value="P" id="f_jk_p"> Perempuan</label>
                    </div>
                </div>
            </div>

            <div class="field">
                <label>Alamat</label>
                <textarea class="input" name="alamat" id="f_alamat" rows="2" placeholder="Alamat domisili saat ini"><?= esc(old('alamat', '')) ?></textarea>
            </div>

            <div class="eyebrow" style="margin:18px 0 10px;">Kontak Wali</div>
            <div class="field-row">
                <div class="field">
                    <label>Nama Wali</label>
                    <input class="input" name="nama_wali" id="f_nama_wali" placeholder="Nama Ayah/Ibu/Wali" value="<?= esc(old('nama_wali', '')) ?>">
                </div>
                <div class="field">
                    <label>No. Telepon</label>
                    <input class="input" name="telp_wali" id="f_telp_wali" placeholder="08xxxxxxxxxx" value="<?= esc(old('telp_wali', '')) ?>">
                </div>
            </div>

            <div class="eyebrow" style="margin:18px 0 10px;">Kelas &amp; Pembayaran</div>
            <div class="field-row">
                <div class="field">
                    <label>Kelas</label>
                    <select class="input" name="id_kelas" id="f_id_kelas">
                        <option value="">— Belum ada kelas —</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>"><?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran'] ?? '-') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field" id="f_status_wrap" style="display:none;">
                    <label class="required">Status Akademik</label>
                    <select class="input" name="status_siswa" id="f_status_siswa">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="lulus">Lulus</option>
                    </select>
                </div>
            </div>

        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="closePanel('siswaPanel')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<!-- ===================== MODAL: Impor Excel ===================== -->
<div class="inline-panel" id="importPanel">
    <div class="inline-panel-header"><h3>Impor Data Siswa</h3><button type="button" class="inline-panel-close" onclick="closePanel('importPanel')"><i class="fa-solid fa-xmark"></i></button></div>

    <?php if (!empty($import_result)): ?>
        <div class="inline-panel-body">
            <div class="hint-box" style="background:<?= $import_result['success_count'] > 0 ? 'var(--success-bg)' : 'var(--warning-bg)' ?>; border:1px solid <?= $import_result['success_count'] > 0 ? 'var(--success-border)' : 'var(--warning-border)' ?>; border-radius:var(--r-md); padding:14px 16px; margin-bottom:16px; display:flex; gap:10px; align-items:flex-start;">
                <i class="fa-solid fa-<?= $import_result['success_count'] > 0 ? 'circle-check' : 'triangle-exclamation' ?>" style="margin-top:2px; color:<?= $import_result['success_count'] > 0 ? 'var(--success)' : 'var(--warning)' ?>;"></i>
                <span style="font-size:13px;"><strong><?= (int) $import_result['success_count'] ?> dari <?= (int) $import_result['total_rows'] ?> baris</strong> berhasil diimpor.</span>
            </div>

            <?php if (!empty($import_result['errors'])): ?>
                <div class="eyebrow" style="margin-bottom:8px;">Catatan (<?= count($import_result['errors']) ?>)</div>
                <div class="card" style="max-height:280px; overflow-y:auto; padding:4px 16px;">
                    <?php foreach ($import_result['errors'] as $err): ?>
                        <div style="padding:10px 0; border-bottom:1px solid var(--border-soft); font-size:12.5px; color:var(--body);"><?= esc($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="inline-panel-footer">
            <button type="button" class="btn btn-secondary" onclick="resetImportModal()"><i class="fa-solid fa-rotate-left"></i> Impor Lagi</button>
            <button type="button" class="btn btn-primary" onclick="closePanel('importPanel'); loadPage();">Selesai</button>
        </div>
    <?php else: ?>
        <form id="importForm" action="<?= base_url('admin/siswa/import') ?>" method="POST" enctype="multipart/form-data">
            <div class="inline-panel-body">
                <div class="hint-box" style="background:var(--brand-bg); border:1px solid var(--brand-light); border-radius:var(--r-md); padding:14px 16px; margin-bottom:18px; display:flex; gap:10px; align-items:flex-start;">
                    <i class="fa-solid fa-circle-info" style="margin-top:1px;"></i>
                    <span style="font-size:12.5px; color:var(--brand-darker);">Unduh template dulu, isi datanya, baru unggah lagi ke sini. Kolom bertanda <strong>*</strong> wajib diisi. Baris yang bermasalah akan dilewati dan dilaporkan — baris lain yang benar tetap tersimpan.</span>
                </div>

                <a href="<?= base_url('admin/siswa/import-template') ?>" class="btn btn-secondary btn-block" style="margin-bottom:18px;"><i class="fa-solid fa-file-excel" style="color:var(--success);"></i> Unduh Template Excel</a>

                <div class="field">
                    <label class="required">File Excel</label>
                    <input type="file" class="input" name="file" id="importFile" accept=".xlsx,.xls" required>
                    <div class="field-hint">Format .xlsx atau .xls, sesuai kolom pada template.</div>
                </div>
            </div>
            <div class="inline-panel-footer">
                <button type="button" class="btn btn-secondary" onclick="closePanel('importPanel')">Batal</button>
                <button type="submit" class="btn btn-primary" id="importSubmitBtn"><i class="fa-solid fa-file-arrow-up"></i> Impor Sekarang</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- ===================== PANEL: Detail Siswa (inline, pengganti drawer) ===================== -->
<div class="inline-panel" id="siswaDetailPanel">
    <div class="inline-panel-header">
        <h3>Detail Siswa</h3>
        <button type="button" class="inline-panel-close" onclick="closePanel('siswaDetailPanel')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="inline-panel-body" id="drawerBody">
        <div class="drawer-skeleton"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i><span>Memuat…</span></div>
    </div>
</div>



<div class="stat-grid">
    <div class="card stat-card">
        <div class="value" id="statTotal">0</div>
        <div class="label">Total</div>
    </div>
    <div class="card stat-card">
        <div class="value" style="color:var(--success);" id="statAktif">0</div>
        <div class="label">Aktif</div>
    </div>
    <div class="card stat-card">
        <div class="value" style="color:var(--info);" id="statLulus">0</div>
        <div class="label">Lulus</div>
    </div>
</div>

<div class="toolbar">
    <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" class="input" id="q" placeholder="Cari nama atau NIS…">
    </div>
    <select class="input" id="fTA">
        <option value="">Semua Tahun Ajaran</option>
        <?php foreach ($tahun_ajaran as $ta): ?>
            <option value="<?= $ta['id_tahun_ajaran'] ?>"><?= esc($ta['nama_tahun_ajaran']) ?><?= $ta['status'] === 'aktif' ? ' (Aktif)' : '' ?></option>
        <?php endforeach; ?>
    </select>
    <select class="input" id="fKelas">
        <option value="">Semua Kelas</option>
        <option value="__tanpa_kelas__">— Tanpa Kelas —</option>
        <?php foreach ($kelas_list as $k): ?>
            <option value="<?= esc($k['nama_kelas']) ?>"><?= esc($k['nama_kelas']) ?></option>
        <?php endforeach; ?>
    </select>
    <select class="input" id="fStatus">
        <option value="">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
        <option value="lulus">Lulus</option>
    </select>
</div>

<!-- Desktop table -->
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Tanggal Lahir</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <!-- Mobile card list -->
    <div class="row-list" id="cardList" style="padding:10px;"></div>

    <div id="emptyState" style="display:none;"></div>
    <div class="pager" id="pager"></div>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const LIST_URL = '<?= base_url('admin/siswa') ?>';
const HAS_ERRORS = <?= !empty($errors) ? 'true' : 'false' ?>;
const OLD_EDITING_ID = <?= json_encode(old('editing_id', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const TAHUN_AJARAN = <?= json_encode($tahun_ajaran, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

let page = 1;
const PER_PAGE = 12;
let q = '', fTA = '', fKelas = '', fStatus = '';

// Kalau dibuka lewat link yang bawa parameter filter (mis. dari kartu "Siswa Tanpa Kelas
// dengan Tunggakan" di Dashboard), langsung terapkan filternya begitu halaman dibuka --
// supaya tidak perlu pilih manual lagi dari dropdown.
(function () {
    const urlParams = new URLSearchParams(location.search);
    if (urlParams.has('kelas')) fKelas = urlParams.get('kelas');
    if (urlParams.has('status')) fStatus = urlParams.get('status');
    if (urlParams.has('ta')) fTA = urlParams.get('ta');
})();

let currentRows = [];
let fetchToken = 0;

function initial(name) { return (name || '?').trim().charAt(0).toUpperCase(); }
function statusBadge(s) {
    if (s === 'aktif') return '<span class="badge badge-success">Aktif</span>';
    if (s === 'lulus') return '<span class="badge badge-info">Lulus</span>';
    return '<span class="badge badge-danger">Nonaktif</span>';
}
function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function fmtDate(d) { if (!d) return '—'; const [y, m, day] = d.split('-'); return `${day}-${m}-${y}`; }
// Ubah YYYY-MM-DD (dari server) jadi DD-MM-YYYY (buat isi field teks saat edit)
function toDDMMYYYY(isoDate) { if (!isoDate) return ''; const [y, m, d] = isoDate.split('-'); return `${d}-${m}-${y}`; }

// Auto-sisip tanda strip saat mengetik tanggal (format DD-MM-YYYY), sama seperti di halaman publik
document.getElementById('f_tanggal_lahir').addEventListener('input', function (e) {
    if (e.inputType === 'deleteContentBackward' || e.inputType === 'deleteContentForward') return;
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 8) value = value.slice(0, 8);
    if (value.length >= 2) value = value.slice(0, 2) + '-' + value.slice(2);
    if (value.length >= 5) value = value.slice(0, 5) + '-' + value.slice(5, 9);
    e.target.value = value;
});

function showSkeleton() {
    let html = '';
    for (let i = 0; i < 4; i++) html += '<div class="skeleton-row"><div class="skeleton-bar" style="width:60%;"></div><div class="skeleton-bar" style="width:35%;"></div></div>';
    document.getElementById('cardList').innerHTML = html;
    document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="padding:0;">' + html + '</td></tr>';
}

async function loadPage() {
    const myToken = ++fetchToken;
    showSkeleton();
    document.getElementById('emptyState').style.display = 'none';

    const params = new URLSearchParams({ page, per_page: PER_PAGE });
    if (q) params.set('q', q);
    if (fTA) params.set('ta', fTA);
    if (fKelas) params.set('kelas', fKelas);
    if (fStatus) params.set('status', fStatus);

    let data;
    try {
        const res = await fetch(LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        data = await res.json();
    } catch (e) {
        document.getElementById('resultCount').textContent = 'Gagal memuat data.';
        return;
    }
    if (myToken !== fetchToken) return; // ada request lebih baru menyusul, hasil ini basi -> abaikan

    currentRows = data.rows;
    document.getElementById('statTotal').textContent = data.stats.total;
    document.getElementById('statAktif').textContent = data.stats.aktif;
    document.getElementById('statLulus').textContent = data.stats.lulus;
    document.getElementById('resultCount').textContent = data.total + ' dari ' + data.stats.total + ' siswa';

    const tbody = document.getElementById('tableBody');
    const cardList = document.getElementById('cardList');
    const emptyState = document.getElementById('emptyState');

    if (currentRows.length === 0) {
        tbody.innerHTML = ''; cardList.innerHTML = '';
        emptyState.style.display = 'block';
        emptyState.innerHTML = '<div class="empty-state"><i class="fa-solid fa-user-slash"></i><p>Tidak ada siswa yang cocok dengan pencarian ini.</p></div>';
    } else {
        emptyState.style.display = 'none';

        tbody.innerHTML = currentRows.map(s => `
            <tr>
                <td><span class="mono">${esc(s.nis)}</span></td>
                <td>
                    <div style="font-weight:700; color:var(--ink);">${esc(s.nama_lengkap)}</div>
                    <div style="font-size:11.5px; color:var(--muted);"><i class="fa-solid fa-${s.jenis_kelamin === 'L' ? 'mars' : 'venus'}"></i> ${s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</div>
                </td>
                <td class="mono" style="font-size:12.5px;">${fmtDate(s.tanggal_lahir)}</td>
                <td>${s.nama_kelas ? '<span class="badge badge-neutral">' + esc(s.nama_kelas) + '</span>' : '<span style="color:var(--faint);">—</span>'}</td>
                <td>${statusBadge(s.status_siswa)}</td>
                <td style="text-align:right;">
                    <button class="icon-action" title="Detail" onclick="openDetailDrawer(${s.id_siswa})"><i class="fa-solid fa-eye"></i></button>
                    <button class="icon-action" title="Edit" onclick="openEditModal(${s.id_siswa})"><i class="fa-solid fa-pencil"></i></button>
                    <button class="icon-action danger" title="Hapus" onclick="hapusSiswa(${s.id_siswa}, '${esc(s.nama_lengkap).replace(/'/g, "\\'")}')"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        `).join('');

        cardList.innerHTML = currentRows.map(s => `
            <div class="card siswa-card">
                <div class="avatar">${initial(s.nama_lengkap)}</div>
                <div class="body" onclick="openDetailDrawer(${s.id_siswa})">
                    <div class="name">${esc(s.nama_lengkap)}</div>
                    <div class="meta">
                        <span class="mono">${esc(s.nis)}</span>
                        ${s.nama_kelas ? '<span>· ' + esc(s.nama_kelas) + '</span>' : ''}
                        ${statusBadge(s.status_siswa)}
                    </div>
                </div>
                <div class="actions">
                    <button class="icon-action" title="Edit" onclick="openEditModal(${s.id_siswa})"><i class="fa-solid fa-pencil"></i></button>
                </div>
            </div>
        `).join('');
    }

    const pager = document.getElementById('pager');
    if (data.total_pages > 1) {
        pager.style.display = 'flex';
        pager.innerHTML = `
            <span>Halaman ${data.page} dari ${data.total_pages}</span>
            <div class="btns">
                <button class="icon-action" ${data.page <= 1 ? 'disabled' : ''} onclick="gotoPage(${data.page - 1})"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="icon-action" ${data.page >= data.total_pages ? 'disabled' : ''} onclick="gotoPage(${data.page + 1})"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        `;
    } else {
        pager.style.display = 'none';
    }
}
function gotoPage(p) { page = p; loadPage(); window.scrollTo({ top: 0, behavior: 'smooth' }); }

let searchDebounce;
document.getElementById('q').addEventListener('input', function () {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => { q = this.value.trim(); page = 1; loadPage(); }, 350);
});
document.getElementById('fTA').addEventListener('change', function () { fTA = this.value; page = 1; loadPage(); });
document.getElementById('fKelas').addEventListener('change', function () { fKelas = this.value; page = 1; loadPage(); });
document.getElementById('fStatus').addEventListener('change', function () { fStatus = this.value; page = 1; loadPage(); });

// Tampilkan di dropdown-nya juga (bukan cuma diterapkan diam-diam) kalau ada filter yang
// terbaca dari URL saat halaman dibuka.
if (fKelas) document.getElementById('fKelas').value = fKelas;
if (fStatus) document.getElementById('fStatus').value = fStatus;
if (fTA) document.getElementById('fTA').value = fTA;

// ===================== MODAL: Tambah / Edit =====================
function resetForm() {
    document.getElementById('siswaForm').reset();
    document.getElementById('f_editing_id').value = '';
}

function openCreateModal() {
    resetForm();
    document.getElementById('siswaModalTitle').textContent = 'Tambah Siswa';
    document.getElementById('siswaForm').action = BASE_URL + '/admin/siswa/store';
    document.getElementById('f_status_wrap').style.display = 'none';
    document.getElementById('f_status_siswa').removeAttribute('required');
    openPanel('siswaPanel');
}

function fillEditForm(s) {
    document.getElementById('f_editing_id').value = s.id_siswa;
    document.getElementById('siswaModalTitle').textContent = 'Edit — ' + s.nama_lengkap;
    document.getElementById('siswaForm').action = BASE_URL + '/admin/siswa/update/' + s.id_siswa;

    document.getElementById('f_nis').value = s.nis || '';
    document.getElementById('f_nisn').value = s.nisn || '';
    document.getElementById('f_nama_lengkap').value = s.nama_lengkap || '';
    document.getElementById('f_tanggal_lahir').value = toDDMMYYYY(s.tanggal_lahir);
    document.getElementById('f_jk_l').checked = s.jenis_kelamin === 'L';
    document.getElementById('f_jk_p').checked = s.jenis_kelamin === 'P';
    document.getElementById('f_alamat').value = s.alamat || '';
    document.getElementById('f_nama_wali').value = s.nama_wali || '';
    document.getElementById('f_telp_wali').value = s.telp_wali || '';
    document.getElementById('f_id_kelas').value = s.id_kelas || '';

    document.getElementById('f_status_wrap').style.display = 'block';
    document.getElementById('f_status_siswa').setAttribute('required', 'required');
    document.getElementById('f_status_siswa').value = s.status_siswa || 'aktif';

    openPanel('siswaPanel');
}

function openEditModal(id) {
    // Kalau datanya sudah ada di halaman yang lagi tampil, langsung pakai (instan, tanpa request).
    const cached = currentRows.find(x => x.id_siswa == id);
    if (cached) { resetForm(); fillEditForm(cached); return; }

    // Kalau tidak (mis. dibuka dari drawer setelah pindah halaman), ambil datanya dulu.
    fetch(BASE_URL + '/admin/siswa/detail/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => { if (data.siswa) { resetForm(); fillEditForm(data.siswa); } });
}

function hapusSiswa(id, nama) {
    if (!confirm('Hapus siswa "' + nama + '"? Tindakan ini tidak bisa dibatalkan.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URL + '/admin/siswa/delete/' + id;
    document.body.appendChild(form);
    form.submit();
}

// ===================== DRAWER: Detail =====================
function openDetailDrawer(id) {
    openPanel('siswaDetailPanel');
    document.getElementById('drawerBody').innerHTML = '<div class="drawer-skeleton"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i><span>Memuat…</span></div>';

    fetch(BASE_URL + '/admin/siswa/detail/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                document.getElementById('drawerBody').innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>' + esc(data.error) + '</p></div>';
                return;
            }
            renderDrawer(data);
        })
        .catch(() => {
            document.getElementById('drawerBody').innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Gagal memuat detail siswa.</p></div>';
        });
}

function renderDrawer(data) {
    const s = data.siswa;
    const r = data.ringkasan;
    const waLink = s.telp_wali ? 'https://wa.me/' + s.telp_wali.replace(/[^0-9]/g, '') : null;

    let tagihanHtml = '';
    if (data.tagihan_terbaru && data.tagihan_terbaru.length > 0) {
        tagihanHtml = `<div class="eyebrow" style="margin-bottom:8px;">Tagihan Terbaru</div><div class="card" style="padding:4px 16px;">`;
        data.tagihan_terbaru.forEach(t => {
            tagihanHtml += `<div class="info-row"><span class="k">${esc(t.nama_tagihan)}${t.bulan_tagihan ? ' (Bln ' + t.bulan_tagihan + ')' : ''}</span><span class="v">${t.status_tagihan === 'lunas' ? '<span class="badge badge-success">Lunas</span>' : 'Rp ' + fmt(t.sisa_tagihan)}</span></div>`;
        });
        tagihanHtml += `</div><a href="${BASE_URL}/admin/tagihan/detail/${s.id_siswa}" style="font-size:12.5px; font-weight:700; color:var(--brand); margin-top:8px; display:inline-block;">Lihat semua tagihan <i class="fa-solid fa-arrow-right"></i></a>`;
    }

    document.getElementById('drawerBody').innerHTML = `
        <div style="text-align:center; margin-bottom:18px;">
            <div class="avatar" style="width:64px;height:64px;font-size:22px;margin:0 auto 10px;">${initial(s.nama_lengkap)}</div>
            <div style="font-size:16px; font-weight:800; color:var(--ink);">${esc(s.nama_lengkap)}</div>
            <div style="font-size:12.5px; color:var(--muted);">NIS ${esc(s.nis)}</div>
            <div style="margin-top:8px;">${statusBadge(s.status_siswa)}</div>
        </div>

        <div class="card" style="padding:14px; margin-bottom:18px; display:flex; gap:10px; max-width:420px; margin-left:auto; margin-right:auto;">
            <button class="btn btn-primary btn-block btn-sm" onclick="closePanel('siswaDetailPanel'); openEditModal(${s.id_siswa});"><i class="fa-solid fa-pencil"></i> Edit</button>
            <a class="btn btn-secondary btn-block btn-sm" href="${BASE_URL}/admin/pembayaran#bayar-${s.id_siswa}"><i class="fa-solid fa-wallet"></i> Bayar</a>
        </div>

        <div class="detail-grid">
            <div class="detail-col">
                <div>
                    <div class="eyebrow" style="margin-bottom:8px;">Ringkasan Keuangan</div>
                    <div class="card" style="padding:4px 16px;">
                        <div class="info-row"><span class="k">Total Tagihan</span><span class="v">Rp ${fmt(r.total_tagihan)}</span></div>
                        <div class="info-row"><span class="k">Sudah Dibayar</span><span class="v" style="color:var(--success);">Rp ${fmt(r.total_dibayar)}</span></div>
                        <div class="info-row"><span class="k">Sisa Tagihan</span><span class="v" style="color:${r.total_sisa > 0 ? 'var(--danger)' : 'var(--success)'};">Rp ${fmt(r.total_sisa)}</span></div>
                    </div>
                </div>
                ${tagihanHtml}
            </div>

            <div class="detail-col">
                <div>
                    <div class="eyebrow" style="margin-bottom:8px;">Data Pribadi</div>
                    <div class="card" style="padding:4px 16px;">
                        <div class="info-row"><span class="k">NISN</span><span class="v">${s.nisn ? esc(s.nisn) : '—'}</span></div>
                        <div class="info-row"><span class="k">Jenis Kelamin</span><span class="v">${s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</span></div>
                        <div class="info-row"><span class="k">Tanggal Lahir</span><span class="v">${fmtDate(s.tanggal_lahir)}</span></div>
                        <div class="info-row"><span class="k">Kelas</span><span class="v">${s.nama_kelas ? esc(s.nama_kelas) : 'Belum ada kelas'}</span></div>
                    </div>
                </div>
                <div>
                    <div class="eyebrow" style="margin-bottom:8px;">Wali</div>
                    <div class="card" style="padding:4px 16px;">
                        <div class="info-row"><span class="k">Nama</span><span class="v">${s.nama_wali ? esc(s.nama_wali) : '—'}</span></div>
                        <div class="info-row"><span class="k">Telepon</span><span class="v">${s.telp_wali ? esc(s.telp_wali) : '—'} ${waLink ? '<a href="' + waLink + '" target="_blank" style="color:var(--success); margin-left:6px;"><i class="fa-brands fa-whatsapp"></i></a>' : ''}</span></div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// ===================== MODAL: Impor Excel =====================
const HAS_IMPORT_RESULT = <?= !empty($import_result) ? 'true' : 'false' ?>;

function openImportModal() { openPanel('importPanel'); }
function resetImportModal() {
    // Hasil impor sebelumnya cuma tampil sekali (flashdata session) -- reload halaman
    // supaya modal kebuka lagi (hash #impor masih ada di address bar) tapi kali ini
    // menampilkan form unggah yang bersih karena flashdata sudah kadaluarsa.
    window.location.reload();
}

const importFormEl = document.getElementById('importForm');
if (importFormEl) {
    importFormEl.addEventListener('submit', function () {
        const btn = document.getElementById('importSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses…';
    });
}

// ===================== Buka otomatis dari hash / error validasi =====================
function handleHash() {
    const h = location.hash;
    if (h === '#tambah') { openCreateModal(); }
    else if (h.startsWith('#edit-')) { openEditModal(parseInt(h.replace('#edit-', ''), 10)); }
    else if (h.startsWith('#detail-')) { openDetailDrawer(parseInt(h.replace('#detail-', ''), 10)); }
    else if (h === '#impor') { openImportModal(); }
}

// Default: tampilkan siswa dari tahun ajaran yang sedang aktif dulu (kalau ada).
// Pengguna tetap bisa ganti ke "Semua Tahun Ajaran" atau tahun lain lewat dropdown.
const activeTA = TAHUN_AJARAN.find(t => t.status === 'aktif');
if (activeTA) {
    fTA = String(activeTA.id_tahun_ajaran);
    document.getElementById('fTA').value = fTA;
}

loadPage();

if (HAS_IMPORT_RESULT) {
    openImportModal();
} else if (HAS_ERRORS) {
    // Form baru saja ditolak validasi -> field-field sudah keisi ulang oleh PHP (old()) di HTML,
    // di sini cukup buka modalnya di mode yang sesuai (tidak perlu reset/lookup data lagi).
    if (OLD_EDITING_ID) {
        document.getElementById('siswaModalTitle').textContent = 'Edit Siswa';
        document.getElementById('siswaForm').action = BASE_URL + '/admin/siswa/update/' + OLD_EDITING_ID;
        document.getElementById('f_status_wrap').style.display = 'block';
        document.getElementById('f_status_siswa').setAttribute('required', 'required');
    } else {
        document.getElementById('siswaModalTitle').textContent = 'Tambah Siswa';
        document.getElementById('siswaForm').action = BASE_URL + '/admin/siswa/store';
    }
    openPanel('siswaPanel');
} else if (location.hash) {
    handleHash();
}
</script>

<?= $this->include('admin/layouts/footer') ?>
