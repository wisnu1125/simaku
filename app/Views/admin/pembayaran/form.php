<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== MODERN PEMBAYARAN FORM CSS (BLUISH TEAL) ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #f8fafc;
    color: #1e293b;
}

/* ==================== PAGE HEADER ==================== */
.page-header {
    margin-bottom: 32px;
    padding: 24px;
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.page-title {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
    margin-bottom: 8px;
}

.breadcrumb {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.breadcrumb a {
    color: #0891b2; /* Primary Teal */
    text-decoration: none;
    transition: all 0.2s;
}

.breadcrumb a:hover {
    color: #0e7490;
}

/* ==================== CARD ==================== */
.card {
    background: #ffffff;
    border-radius: 16px;
    padding: 32px;
    border: 1px solid #e2e8f0;
    max-width: 1000px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

/* ==================== ALERT ==================== */
.alert-info {
    background: #ecfeff; /* Light Teal Bg */
    border: 1px solid #cffafe;
    color: #0e7490;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 32px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* ==================== FORM SECTION ==================== */
.form-section {
    margin-bottom: 32px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 800;
    color: #0891b2; /* Primary Teal */
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #ecfeff;
    display: flex;
    align-items: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ==================== FORM GROUP ==================== */
.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-weight: 700;
    font-size: 14px;
}

.form-group label.required::after {
    content: " *";
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
    background: #f8fafc;
}

.form-control:focus {
    outline: none;
    border-color: #0891b2;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
}

/* ==================== SEARCH BOX ==================== */
.search-box {
    position: relative;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0 0 10px 10px;
    max-height: 280px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.search-result-item {
    padding: 14px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
}

.search-result-item:hover {
    background: #ecfeff;
}

/* ==================== SELECTED SISWA ==================== */
.selected-siswa-box {
    background: #ecfeff;
    border: 1px solid #0891b2;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
}

.selected-siswa-info strong {
    color: #0e7490;
}

/* ==================== TAGIHAN CHECKLIST ==================== */
.tagihan-checklist {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.checklist-header {
    background: #f8fafc;
    border-bottom: 2px solid #0891b2;
    padding: 16px;
    display: grid;
    grid-template-columns: 50px 1fr 180px 180px;
    gap: 16px;
    font-weight: 800;
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.checklist-item {
    display: grid;
    grid-template-columns: 50px 1fr 180px 180px;
    gap: 16px;
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    align-items: start; /* Changed center to start for text alignment */
    transition: all 0.2s;
}

.checklist-item.checked {
    background: #f0fdfa;
    border-left: 4px solid #0891b2;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    height: 100%;
}

.checkbox-wrapper input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #0891b2;
}

.badge-info {
    background: #fef3c7; /* Amber for period */
    color: #92400e;
    border: 1px solid #fde68a;
}

.badge-warning {
    background: #dcfce7; /* Green for TA */
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.nominal-input {
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: #0891b2;
    width: 100%;
}

.date-input {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
}

/* ==================== TERBILANG TEXT ==================== */
.terbilang-text {
    font-size: 11px;
    color: #0e7490;
    font-style: italic;
    margin-top: 6px;
    line-height: 1.3;
    display: block;
    font-weight: 500;
}

/* ==================== SUMMARY BOX ==================== */
.summary-box {
    background: linear-gradient(135deg, #0891b2, #0e7490);
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
    color: white;
    box-shadow: 0 10px 25px -5px rgba(8, 145, 178, 0.4);
}

.summary-title {
    font-size: 15px;
    font-weight: 800;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 2px solid rgba(255,255,255,0.3);
}

.summary-label {
    font-weight: 600;
    opacity: 0.9;
}

.summary-value.total {
    font-size: 24px;
    font-weight: 900;
    color: #ffffff;
}

/* ==================== FORM ACTIONS ==================== */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.btn {
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    cursor: pointer;
}

.btn-primary {
    background: #0891b2;
    color: #ffffff;
    border: none;
}

.btn-primary:hover:not(:disabled) {
    background: #0e7490;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
}

.btn-secondary {
    background: #ffffff;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
}

/* ==================== RADIO GROUP ==================== */
.radio-item {
    border: 1.5px solid #e2e8f0;
    padding: 12px 20px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.radio-item.selected {
    border-color: #0891b2;
    background: #ecfeff;
}

.radio-item input[type="radio"] {
    accent-color: #0891b2;
}

@media (max-width: 768px) {
    .checklist-header, .checklist-item {
        grid-template-columns: 40px 1fr;
    }
}
</style>

<div class="page-header">
    <h1 class="page-title">Input Pembayaran</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/pembayaran') ?>">Pembayaran</a> &nbsp;•&nbsp; 
        <span>Input</span>
    </div>
</div>

<div class="card">
    <div class="alert-info">
        <i class="fas fa-info-circle fa-lg"></i>
        <span><strong>Info:</strong> Pilih siswa, lalu centang tagihan yang ingin dibayar. Sistem mendukung pembayaran ganda sekaligus!</span>
    </div>
    
    <form action="<?= base_url('admin/pembayaran/store-bulk') ?>" method="POST" id="formPembayaran">
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-user-circle"></i> 1. Penentuan Siswa
            </div>
            
            <div class="form-group">
                <label for="siswa_search" class="required">Cari Siswa</label>
                <div class="search-box">
                    <input 
                        type="text" 
                        class="form-control" 
                        id="siswa_search" 
                        placeholder="Masukkan NIS atau Nama Lengkap..."
                        autocomplete="off"
                    >
                    <div class="search-results" id="siswa_results"></div>
                </div>
                <input type="hidden" name="id_siswa" id="id_siswa" required>
                <div id="selected_siswa"></div>
            </div>
        </div>
        
        <div class="form-section" id="tagihan_section" style="display: none;">
            <div class="form-section-title">
                <i class="fas fa-list-ul"></i> 2. Daftar Tagihan Aktif
            </div>
            
            <div id="tagihan_list"></div>
        </div>
        
        <div class="form-section" id="pembayaran_section" style="display: none;">
            <div class="form-section-title">
                <i class="fas fa-wallet"></i> 3. Konfigurasi Pembayaran
            </div>
            
            <div class="form-group">
                <label class="required">Metode Pembayaran</label>
                <div class="radio-group">
                    <div class="radio-item selected">
                        <input 
                            type="radio" 
                            id="metode_tunai" 
                            name="metode_pembayaran" 
                            value="tunai"
                            checked
                            required
                        >
                        <label for="metode_tunai">💵 Tunai (Cash)</label>
                    </div>
                    <div class="radio-item">
                        <input 
                            type="radio" 
                            id="metode_transfer" 
                            name="metode_pembayaran" 
                            value="transfer"
                            required
                        >
                        <label for="metode_transfer">🏦 Transfer Bank</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Memo / Keterangan</label>
                <textarea 
                    class="form-control" 
                    id="keterangan" 
                    name="keterangan" 
                    placeholder="Contoh: Pembayaran melalui wali murid..."
                    rows="3"
                ></textarea>
            </div>
            
            <div class="summary-box" id="summary_box" style="display: none;">
                <div class="summary-title">
                    <i class="fas fa-file-invoice-dollar"></i> Kalkulasi Pembayaran
                </div>
                <div class="summary-row">
                    <span class="summary-label">Tagihan yang dipilih</span>
                    <span class="summary-value" id="summary_count">0</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total yang harus dibayar</span>
                    <span class="summary-value total" id="summary_total">Rp 0</span>
                </div>
            </div>
        </div>
        
        <div class="form-actions" id="form_actions" style="display: none;">
            <button type="submit" class="btn btn-primary" id="btn_submit" disabled>
                <i class="fas fa-check-double"></i> Konfirmasi Pembayaran
            </button>
            <a href="<?= base_url('admin/pembayaran') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>

<script>

let selectedSiswaId = null;
let selectedTagihan = [];

// Siswa search
let searchTimeout;
document.getElementById('siswa_search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const keyword = this.value;
    
    if (keyword.length < 2) {
        document.getElementById('siswa_results').style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch('<?= base_url('admin/siswa/search') ?>?keyword=' + encodeURIComponent(keyword))
            .then(response => response.json())
            .then(data => {
                const results = document.getElementById('siswa_results');
                results.innerHTML = '';
                
                if (data.length === 0) {
                    results.innerHTML = '<div class="search-result-item" style="color: #9ca3af;">Tidak ada hasil ditemukan</div>';
                } else {
                    data.forEach(siswa => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <strong>${siswa.nama_lengkap}</strong><br>
                            <small style="color: #64748b;">
                                <i class="fas fa-id-card"></i> NIS: ${siswa.nis} • ${siswa.nama_kelas || 'Belum dikelas'}
                            </small>
                        `;
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
            <div class="selected-siswa-info">
                <strong>${siswa.nama_lengkap}</strong><br>
                <small><i class="fas fa-id-card"></i> NIS: ${siswa.nis}</small>
            </div>
            <button type="button" class="btn-clear" onclick="clearSiswa()">
                <i class="fas fa-times"></i> Hapus
            </button>
        </div>
    `;
    
    // Load tagihan
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
        .then(response => response.json())
        .then(data => {
            const tagihanList = document.getElementById('tagihan_list');
            
            // ================= LOGIKA SORTING (TETAP SAMA) =================
            data.sort((a, b) => {
                const nameA = a.nama_tagihan.toLowerCase();
                const nameB = b.nama_tagihan.toLowerCase();
                const isSppA = nameA.includes('spp');
                const isSppB = nameB.includes('spp');
                
                if (!isSppA && isSppB) return -1; 
                if (isSppA && !isSppB) return 1;  
                
                if (isSppA && isSppB) {
                    const academicOrder = {
                        'juli': 1, 'jul': 1,
                        'agustus': 2, 'agu': 2,
                        'september': 3, 'sep': 3,
                        'oktober': 4, 'okt': 4,
                        'november': 5, 'nov': 5,
                        'desember': 6, 'des': 6,
                        'januari': 7, 'jan': 7,
                        'februari': 8, 'feb': 8,
                        'maret': 9, 'mar': 9,
                        'april': 10, 'apr': 10,
                        'mei': 11, 'may': 11, 'mei': 11,
                        'juni': 12, 'jun': 12
                    };
                    
                    const getOrderValue = (text) => {
                        for (const [key, value] of Object.entries(academicOrder)) {
                            if (text.includes(key)) {
                                return value;
                            }
                        }
                        return 99;
                    };
                    
                    const orderA = getOrderValue(nameA);
                    const orderB = getOrderValue(nameB);
                    
                    return orderA - orderB;
                }
                return 0;
            });
            // ================= END LOGIKA SORTING =================
            
            if (data.length === 0) {
                tagihanList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">✓</div>
                        <p>Tidak ada tagihan yang belum lunas</p>
                    </div>
                `;
                document.getElementById('tagihan_section').style.display = 'block';
                return;
            }
            
            let html = `
                <div class="tagihan-checklist">
                    <div class="checklist-header">
                        <div>✓</div>
                        <div>TAGIHAN</div>
                        <div>NOMINAL BAYAR</div>
                        <div>TANGGAL BAYAR</div>
                    </div>
            `;
            
            data.forEach((tagihan, index) => {
                const today = new Date().toISOString().split('T')[0];
                html += `
                    <div class="checklist-item" id="item_${index}">
                        <div class="checkbox-wrapper">
                            <input 
                                type="checkbox" 
                                id="check_${index}" 
                                onchange="toggleTagihan(${index}, ${JSON.stringify(tagihan).replace(/"/g, '&quot;')})"
                            >
                        </div>
                        <div class="tagihan-info">
                            <strong>${tagihan.nama_tagihan}</strong>
                            ${tagihan.bulan_tagihan ? '<span class="badge badge-info">Bulan ' + tagihan.bulan_tagihan + '</span>' : ''}
                            <span class="badge badge-warning">${tagihan.nama_tahun_ajaran}</span>
                            <div class="sisa-tagihan">
                                Sisa: <strong style="color: #ef4444;">Rp ${formatRupiah(tagihan.sisa_tagihan)}</strong>
                            </div>
                        </div>
                        <div>
                            <input 
                                type="number" 
                                class="nominal-input" 
                                id="nominal_${index}" 
                                name="nominal[${tagihan.id_tagihan}]"
                                value="${tagihan.sisa_tagihan}" 
                                min="0" 
                                max="${tagihan.sisa_tagihan}"
                                oninput="updateNominalInput(${index})"
                                disabled
                            >
                            <div id="terbilang_${index}" class="terbilang-text"></div>
                        </div>
                        <div>
                            <input 
                                type="date" 
                                class="date-input" 
                                id="tanggal_${index}" 
                                name="tanggal[${tagihan.id_tagihan}]"
                                value="${today}" 
                                max="${today}"
                                disabled
                            >
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            tagihanList.innerHTML = html;
            
            document.getElementById('tagihan_section').style.display = 'block';
            document.getElementById('pembayaran_section').style.display = 'block';
            document.getElementById('form_actions').style.display = 'flex';
        });
}

// Fungsi Terbilang JS
function terbilang(angka) {
    angka = Math.floor(Math.abs(angka));
    var baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    var terbilang = '';

    if (angka < 12) {
        terbilang = ' ' + baca[angka];
    } else if (angka < 20) {
        terbilang = terbilangJS(angka - 10) + ' Belas';
    } else if (angka < 100) {
        terbilang = terbilangJS(Math.floor(angka / 10)) + ' Puluh' + terbilangJS(angka % 10);
    } else if (angka < 200) {
        terbilang = ' Seratus' + terbilangJS(angka - 100);
    } else if (angka < 1000) {
        terbilang = terbilangJS(Math.floor(angka / 100)) + ' Ratus' + terbilangJS(angka % 100);
    } else if (angka < 2000) {
        terbilang = ' Seribu' + terbilangJS(angka - 1000);
    } else if (angka < 1000000) {
        terbilang = terbilangJS(Math.floor(angka / 1000)) + ' Ribu' + terbilangJS(angka % 1000);
    } else if (angka < 1000000000) {
        terbilang = terbilangJS(Math.floor(angka / 1000000)) + ' Juta' + terbilangJS(angka % 1000000);
    }
    
    return terbilang;
}

// Helper rekursif untuk terbilang agar nama fungsi tidak bentrok
function terbilangJS(angka) {
    return terbilang(angka);
}

function updateTerbilangDisplay(index) {
    const nominalInput = document.getElementById('nominal_' + index);
    const terbilangDiv = document.getElementById('terbilang_' + index);
    const val = parseInt(nominalInput.value) || 0;
    
    if (val > 0) {
        terbilangDiv.textContent = terbilang(val).trim() + " Rupiah";
    } else {
        terbilangDiv.textContent = "";
    }
}

function updateNominalInput(index) {
    updateTerbilangDisplay(index);
    updateNominal(index);
}

function toggleTagihan(index, tagihan) {
    const checkbox = document.getElementById('check_' + index);
    const item = document.getElementById('item_' + index);
    const nominalInput = document.getElementById('nominal_' + index);
    const tanggalInput = document.getElementById('tanggal_' + index);
    
    if (checkbox.checked) {
        item.classList.add('checked');
        nominalInput.disabled = false;
        tanggalInput.disabled = false;
        
        // Trigger terbilang saat dicentang
        updateTerbilangDisplay(index);
        
        selectedTagihan.push({
            id_tagihan: tagihan.id_tagihan,
            nominal: parseInt(nominalInput.value),
            tanggal: tanggalInput.value
        });
    } else {
        item.classList.remove('checked');
        nominalInput.disabled = true;
        tanggalInput.disabled = true;
        
        // Hapus text terbilang saat di-uncheck
        document.getElementById('terbilang_' + index).textContent = "";
        
        selectedTagihan = selectedTagihan.filter(t => t.id_tagihan !== tagihan.id_tagihan);
    }
    
    updateSummary();
}

function updateNominal(index) {
    const nominalInput = document.getElementById('nominal_' + index);
    const checkbox = document.getElementById('check_' + index);
    
    if (checkbox.checked) {
        const idTagihan = selectedTagihan.find(t => {
            return document.querySelector(`input[name="nominal[${t.id_tagihan}]"]`) === nominalInput;
        });
        
        if (idTagihan) {
            idTagihan.nominal = parseInt(nominalInput.value) || 0;
        }
        
        updateSummary();
    }
}

function updateSummary() {
    const count = selectedTagihan.length;
    const total = selectedTagihan.reduce((sum, t) => sum + t.nominal, 0);
    
    document.getElementById('summary_count').textContent = count;
    document.getElementById('summary_total').textContent = 'Rp ' + formatRupiah(total);
    
    if (count > 0) {
        document.getElementById('summary_box').style.display = 'block';
        document.getElementById('btn_submit').disabled = false;
    } else {
        document.getElementById('summary_box').style.display = 'none';
        document.getElementById('btn_submit').disabled = true;
    }
}

function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

document.querySelectorAll('.radio-item input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.radio-item').forEach(item => {
            item.classList.remove('selected');
        });
        if (this.checked) {
            this.closest('.radio-item').classList.add('selected');
        }
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-box')) {
        document.getElementById('siswa_results').style.display = 'none';
    }
});

document.getElementById('formPembayaran').addEventListener('submit', function(e) {
    if (selectedTagihan.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 tagihan untuk dibayar!');
        return false;
    }
});
</script>

<?= $this->include('admin/layouts/footer') ?>