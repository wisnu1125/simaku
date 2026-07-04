<?= $this->include('admin/layouts/header') ?>

<style>
.search-box { position: relative; }
.search-results {
    display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 30;
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md);
    box-shadow: var(--shadow-md); max-height: 280px; overflow-y: auto;
}
.search-result-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--border-soft); font-size: 13.5px; }
.search-result-item:last-child { border-bottom: none; }
.search-result-item:hover { background: var(--brand-bg); }
.search-result-item strong { color: var(--ink); }
.search-result-item small { color: var(--muted); }

.selected-siswa-box {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    background: var(--brand-bg); border: 1.5px solid var(--brand-light); border-radius: var(--r-md);
    padding: 14px 16px; margin-top: 10px;
}
.selected-siswa-box strong { color: var(--ink); font-size: 14px; }
.selected-siswa-box small { color: var(--muted); }

.section-step { display: flex; align-items: center; gap: 10px; margin: 24px 0 14px; }
.section-step:first-child { margin-top: 0; }
.section-step .num { width: 24px; height: 24px; border-radius: 50%; background: var(--brand); color: #fff; font-size: 12px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.section-step .txt { font-size: 14px; font-weight: 800; color: var(--ink); }

.checklist { border: 1px solid var(--border-soft); border-radius: var(--r-md); overflow: hidden; }
.checklist-header {
    display: none; padding: 10px 16px; background: var(--border-soft); font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px; color: var(--muted);
    grid-template-columns: 28px 1.6fr 1fr 1fr; gap: 14px;
}
@media (min-width: 769px) { .checklist-header { display: grid; } }

.checklist-item { display: grid; grid-template-columns: 28px 1fr; row-gap: 10px; column-gap: 14px; align-items: center; padding: 14px 16px; border-bottom: 1px solid var(--border-soft); transition: background .15s; }
.checklist-item:last-child { border-bottom: none; }
.checklist-item.checked { background: var(--brand-bg); }
.checklist-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--brand); cursor: pointer; }
.checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: 1 / -1; }
@media (min-width: 769px) {
    .checklist-item { grid-template-columns: 28px 1.6fr 1fr 1fr; row-gap: 0; }
    .checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: auto; }
}

.tagihan-info strong { font-size: 13.5px; color: var(--ink); display: block; margin-bottom: 4px; }
.tagihan-info .sisa { font-size: 12px; color: var(--danger); font-weight: 700; margin-top: 4px; }
.nominal-input, .date-input { width: 100%; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--r-sm); font-size: 13.5px; font-family: 'Roboto Mono', monospace; }
.nominal-input:disabled, .date-input:disabled { background: var(--border-soft); color: var(--faint); }
.terbilang-text { font-size: 10.5px; color: var(--muted); font-style: italic; margin-top: 3px; font-family: 'Roboto', sans-serif; }

.summary-box { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); border-radius: var(--r-lg); padding: 20px; margin-top: 18px; color: #fff; }
.summary-row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,.2); font-size: 13.5px; }
.summary-row:last-child { border-bottom: none; padding-top: 12px; margin-top: 4px; border-top: 2px solid rgba(255,255,255,.3); }
.summary-row .v.total { font-size: 21px; font-weight: 900; }

.sticky-actions { position: sticky; bottom: 0; background: var(--surface); border-top: 1px solid var(--border-soft); padding: 14px 0 4px; margin-top: 20px; display: flex; gap: 10px; }
@media (max-width: 768px) { .sticky-actions { bottom: calc(var(--bottomnav-h)); padding: 12px 14px; margin: 20px -14px -16px; box-shadow: 0 -6px 16px rgba(15,23,42,.06); } }
</style>

<div class="page-title" style="margin-bottom:2px;">Input Pembayaran</div>
<div class="page-subtitle" style="margin-bottom:18px;">Cari siswa, centang tagihan yang dibayar — bisa lebih dari satu sekaligus.</div>

<div class="card card-pad">
    <form action="<?= base_url('admin/pembayaran/store-bulk') ?>" method="POST" id="formPembayaran">

        <div class="section-step"><span class="num">1</span><span class="txt">Pilih Siswa</span></div>
        <div class="field" style="margin-bottom:0;">
            <div class="search-box">
                <input type="text" class="input" id="siswa_search" placeholder="Ketik NIS atau nama siswa…" autocomplete="off">
                <div class="search-results" id="siswa_results"></div>
            </div>
            <input type="hidden" name="id_siswa" id="id_siswa" required>
            <div id="selected_siswa"></div>
        </div>

        <div id="tagihan_section" style="display:none;">
            <div class="section-step"><span class="num">2</span><span class="txt">Tagihan Belum Lunas</span></div>
            <div class="checklist">
                <div class="checklist-header">
                    <div></div><div>Tagihan</div><div>Nominal Bayar</div><div>Tanggal Bayar</div>
                </div>
                <div id="tagihan_list"></div>
            </div>
        </div>

        <div id="pembayaran_section" style="display:none;">
            <div class="section-step"><span class="num">3</span><span class="txt">Metode &amp; Catatan</span></div>

            <div class="field">
                <label class="required">Metode Pembayaran</label>
                <div class="segmented">
                    <label><input type="radio" name="metode_pembayaran" value="tunai" checked required> 💵 Tunai</label>
                    <label><input type="radio" name="metode_pembayaran" value="transfer" required> 🏦 Transfer</label>
                </div>
            </div>

            <div class="field">
                <label>Catatan (opsional)</label>
                <textarea class="input" name="keterangan" rows="2" placeholder="Contoh: dibayar oleh wali murid langsung"></textarea>
            </div>

            <div class="summary-box" id="summary_box" style="display:none;">
                <div class="summary-row"><span>Tagihan dipilih</span><span id="summary_count">0</span></div>
                <div class="summary-row"><span>Total dibayar</span><span class="v total" id="summary_total">Rp 0</span></div>
            </div>
        </div>

        <div class="sticky-actions" id="form_actions" style="display:none;">
            <button type="submit" class="btn btn-primary btn-block" id="btn_submit" disabled><i class="fa-solid fa-check-double"></i> Konfirmasi Pembayaran</button>
            <a href="<?= base_url('admin/pembayaran') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
let selectedSiswaId = null;
let selectedTagihan = [];

// ---------- Cari siswa ----------
let searchTimeout;
document.getElementById('siswa_search').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const keyword = this.value;
    if (keyword.length < 2) { document.getElementById('siswa_results').style.display = 'none'; return; }

    searchTimeout = setTimeout(() => {
        fetch('<?= base_url('admin/siswa/search') ?>?keyword=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(data => {
                const results = document.getElementById('siswa_results');
                results.innerHTML = '';
                if (data.length === 0) {
                    results.innerHTML = '<div class="search-result-item" style="color:var(--faint);">Tidak ada hasil.</div>';
                } else {
                    data.forEach(siswa => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `<strong>${siswa.nama_lengkap}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${siswa.nis} · ${siswa.nama_kelas || 'Belum dikelas'}</small>`;
                        item.onclick = () => selectSiswa(siswa);
                        results.appendChild(item);
                    });
                }
                results.style.display = 'block';
            });
    }, 300);
});

function selectSiswa(siswa) {
    selectedSiswaId = siswa.id_siswa;
    selectedTagihan = [];

    document.getElementById('id_siswa').value = siswa.id_siswa;
    document.getElementById('siswa_search').value = '';
    document.getElementById('siswa_results').style.display = 'none';

    document.getElementById('selected_siswa').innerHTML = `
        <div class="selected-siswa-box">
            <div><strong>${siswa.nama_lengkap}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${siswa.nis}</small></div>
            <button type="button" class="icon-action danger" onclick="clearSiswa()" title="Ganti siswa"><i class="fa-solid fa-xmark"></i></button>
        </div>`;

    loadTagihan(siswa.id_siswa);
}

function clearSiswa() {
    selectedSiswaId = null;
    selectedTagihan = [];
    document.getElementById('id_siswa').value = '';
    document.getElementById('selected_siswa').innerHTML = '';
    document.getElementById('tagihan_section').style.display = 'none';
    document.getElementById('pembayaran_section').style.display = 'none';
    document.getElementById('form_actions').style.display = 'none';
    updateSummary();
}

function loadTagihan(idSiswa) {
    fetch('<?= base_url('admin/pembayaran/get-tagihan-by-siswa') ?>?id_siswa=' + idSiswa)
        .then(r => r.json())
        .then(data => {
            const tagihanList = document.getElementById('tagihan_list');

            // ================= LOGIKA SORTING (dipertahankan sama persis) =================
            data.sort((a, b) => {
                const nameA = a.nama_tagihan.toLowerCase();
                const nameB = b.nama_tagihan.toLowerCase();
                const isSppA = nameA.includes('spp');
                const isSppB = nameB.includes('spp');
                if (!isSppA && isSppB) return -1;
                if (isSppA && !isSppB) return 1;
                if (isSppA && isSppB) {
                    const academicOrder = { juli: 1, jul: 1, agustus: 2, agu: 2, september: 3, sep: 3, oktober: 4, okt: 4, november: 5, nov: 5, desember: 6, des: 6, januari: 7, jan: 7, februari: 8, feb: 8, maret: 9, mar: 9, april: 10, apr: 10, mei: 11, may: 11, juni: 12, jun: 12 };
                    const getOrderValue = (text) => { for (const [key, value] of Object.entries(academicOrder)) { if (text.includes(key)) return value; } return 99; };
                    return getOrderValue(nameA) - getOrderValue(nameB);
                }
                return 0;
            });
            // ================= END SORTING =================

            if (data.length === 0) {
                tagihanList.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-check"></i><p>Tidak ada tagihan yang belum lunas.</p></div>';
                document.getElementById('tagihan_section').style.display = 'block';
                return;
            }

            let html = '';
            data.forEach((tagihan, index) => {
                const today = new Date().toISOString().split('T')[0];
                html += `
                    <div class="checklist-item" id="item_${index}">
                        <input type="checkbox" id="check_${index}" onchange='toggleTagihan(${index}, ${JSON.stringify(tagihan)})'>
                        <div class="tagihan-info">
                            <strong>${tagihan.nama_tagihan}</strong>
                            ${tagihan.bulan_tagihan ? '<span class="badge badge-info">Bulan ' + tagihan.bulan_tagihan + '</span> ' : ''}
                            <span class="badge badge-neutral">${tagihan.nama_tahun_ajaran}</span>
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
            });
            tagihanList.innerHTML = html;

            document.getElementById('tagihan_section').style.display = 'block';
            document.getElementById('pembayaran_section').style.display = 'block';
            document.getElementById('form_actions').style.display = 'flex';
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
        item.classList.add('checked');
        nominalInput.disabled = false;
        tanggalInput.disabled = false;
        updateTerbilangDisplay(index);
        selectedTagihan.push({ id_tagihan: tagihan.id_tagihan, nominal: parseInt(nominalInput.value), tanggal: tanggalInput.value });
    } else {
        item.classList.remove('checked');
        nominalInput.disabled = true;
        tanggalInput.disabled = true;
        document.getElementById('terbilang_' + index).textContent = '';
        selectedTagihan = selectedTagihan.filter(t => t.id_tagihan !== tagihan.id_tagihan);
    }
    updateSummary();
}

function updateNominal(index) {
    const nominalInput = document.getElementById('nominal_' + index);
    const checkbox = document.getElementById('check_' + index);
    if (checkbox.checked) {
        const found = selectedTagihan.find(t => document.querySelector(`input[name="nominal[${t.id_tagihan}]"]`) === nominalInput);
        if (found) found.nominal = parseInt(nominalInput.value) || 0;
        updateSummary();
    }
}

function updateSummary() {
    const count = selectedTagihan.length;
    const total = selectedTagihan.reduce((sum, t) => sum + t.nominal, 0);
    document.getElementById('summary_count').textContent = count;
    document.getElementById('summary_total').textContent = 'Rp ' + formatRupiah(total, '');
    document.getElementById('summary_box').style.display = count > 0 ? 'block' : 'none';
    document.getElementById('btn_submit').disabled = count === 0;
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-box')) document.getElementById('siswa_results').style.display = 'none';
});

document.getElementById('formPembayaran').addEventListener('submit', function (e) {
    if (selectedTagihan.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 tagihan untuk dibayar.');
    }
});

// ---------- Quick-link: ?id_siswa=123 langsung pilih siswa itu (mis. dari tombol "Bayar" di drawer Siswa) ----------
(function () {
    const params = new URLSearchParams(location.search);
    const idSiswa = params.get('id_siswa');
    if (!idSiswa) return;
    fetch('<?= base_url('admin/siswa/detail') ?>/' + idSiswa, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => { if (data.siswa) selectSiswa(data.siswa); });
})();
</script>

<?= $this->include('admin/layouts/footer') ?>
