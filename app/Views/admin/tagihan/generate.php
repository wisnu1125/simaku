<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL PROCESS THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-light: #cffafe;
    --primary-bg: #ecfeff;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
    --radius: 16px;
}

body { background: #f8fafc; font-family: 'Inter', sans-serif; }

/* Header & Breadcrumb */
.page-header { margin-bottom: 28px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 8px; }
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Card Main */
.card-process {
    background: #ffffff;
    border-radius: var(--radius);
    padding: 40px;
    border: 1px solid var(--border);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    max-width: 750px;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Alerts Modern */
.alert-custom {
    padding: 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    gap: 15px;
    align-items: flex-start;
    line-height: 1.5;
}
.alert-warn { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; }
.alert-info { background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; }

/* Sections */
.form-section { margin-bottom: 35px; }
.form-section-title {
    font-size: 15px; font-weight: 800; color: var(--primary);
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
    display: flex; align-items: center; gap: 10px;
}

.form-group label { display: block; margin-bottom: 10px; font-size: 13px; font-weight: 700; color: #334155; }
.form-group label.required::after { content: " *"; color: #ef4444; }

.form-control {
    width: 100%; padding: 12px 16px; border: 1.5px solid var(--border);
    border-radius: 10px; font-size: 14px; background: #f8fafc;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.form-control:focus { outline: none; border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1); }

/* Radio Grid Modern */
.radio-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 10px; }
.radio-card {
    border: 2px solid var(--border); border-radius: 12px; padding: 20px 15px;
    cursor: pointer; transition: all 0.3s ease; text-align: center; background: white;
}
.radio-card:hover { border-color: var(--primary-light); background: var(--primary-bg); transform: translateY(-3px); }
.radio-card.active { border-color: var(--primary); background: var(--primary-bg); box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.1); }
.radio-card input[type="radio"] { display: none; }
.radio-icon { font-size: 30px; margin-bottom: 12px; display: block; }
.radio-label { font-size: 14px; font-weight: 800; color: #1e293b; display: block; margin-bottom: 4px; }
.radio-desc { font-size: 11px; color: #64748b; font-weight: 500; line-height: 1.3; }
.radio-card.active .radio-label { color: var(--primary); }

/* Search & Results */
.search-box { position: relative; }
.search-results {
    position: absolute; top: 105%; left: 0; right: 0; background: white;
    border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 100; display: none; max-height: 250px; overflow-y: auto;
}
.search-result-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
.search-result-item:hover { background: var(--primary-bg); color: var(--primary); }

/* Actions */
.form-actions { display: flex; gap: 15px; margin-top: 40px; padding-top: 25px; border-top: 1px solid var(--border); }
.btn { 
    padding: 14px 30px; border-radius: 10px; font-size: 14px; font-weight: 800; 
    display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.4); }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }

/* Hidden state */
#kelas_section, #siswa_section { display: none; animation: slideDown 0.3s ease-out; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="page-header">
    <h1 class="page-title">Generate Tagihan Otomatis</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/tagihan') ?>">Manajemen Tagihan</a> &nbsp;•&nbsp; 
        <span>Siklus Generate</span>
    </div>
</div>

<div class="card-process">
    <div class="alert-custom alert-warn">
        <span style="font-size: 20px;">⚠️</span>
        <div>
            <strong>Peringatan Penting:</strong> Proses ini akan membuat record tagihan baru berdasarkan skema aktif. Sistem secara otomatis melakukan validasi agar <strong>tidak terjadi duplikasi</strong> pada tagihan yang sudah ada.
        </div>
    </div>
    
    <div class="alert-custom alert-info">
        <span style="font-size: 20px;">ℹ️</span>
        <div>Pastikan Anda telah melakukan konfigurasi <strong>Skema Tagihan</strong> di menu master data sebelum memulai proses ini.</div>
    </div>
    
    <form action="<?= base_url('admin/tagihan/generate') ?>" method="POST" onsubmit="return confirm('Mulai proses generate tagihan sekarang?')">
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-calendar-alt"></i> &nbsp; Batasan Waktu
            </div>
            
            <div class="form-group">
                <label for="id_tahun_ajaran" class="required">Tahun Ajaran Target</label>
                <select 
                    class="form-control" 
                    id="id_tahun_ajaran" 
                    name="id_tahun_ajaran"
                    onchange="loadKelasByTahunAjaran()"
                    required
                >
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= (old('id_tahun_ajaran') == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?> 
                            <?= $ta['status'] === 'aktif' ? '• AKTIF' : '• CLOSED' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-hint" style="margin-top: 10px; font-style: italic;">Tagihan hanya akan diproses untuk periode akademik yang dipilih di atas.</div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-users-cog"></i> &nbsp; Ruang Lingkup Generasi
            </div>
            
            <div class="form-group">
                <label class="required">Pilih Target Siswa</label>
                <div class="radio-grid">
                    <label class="radio-card active" onclick="selectTipe(this, 'semua')">
                        <input type="radio" name="tipe_generate" value="semua" checked required>
                        <div class="radio-content">
                            <span class="radio-icon">🌐</span>
                            <span class="radio-label">Global</span>
                            <span class="radio-desc">Seluruh siswa aktif</span>
                        </div>
                    </label>
                    
                    <label class="radio-card" onclick="selectTipe(this, 'kelas')">
                        <input type="radio" name="tipe_generate" value="kelas" required>
                        <div class="radio-content">
                            <span class="radio-icon">🏫</span>
                            <span class="radio-label">Kelas</span>
                            <span class="radio-desc">Satu kelas spesifik</span>
                        </div>
                    </label>
                    
                    <label class="radio-card" onclick="selectTipe(this, 'siswa')">
                        <input type="radio" name="tipe_generate" value="siswa" required>
                        <div class="radio-content">
                            <span class="radio-icon">👤</span>
                            <span class="radio-label">Personal</span>
                            <span class="radio-desc">Satu siswa pilihan</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div id="kelas_section" class="form-group">
                <label for="id_kelas" class="required">Pilih Kelas</label>
                <select class="form-control" id="id_kelas" name="id_kelas">
                    <option value="">-- Pilih Unit Kelas --</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" data-tahun="<?= $k['id_tahun_ajaran'] ?>">
                            <?= esc($k['nama_kelas']) ?> (<?= esc($k['nama_tahun_ajaran']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="siswa_section" class="form-group">
                <label for="siswa_search" class="required">Cari Data Siswa</label>
                <div class="search-box">
                    <input 
                        type="text" 
                        class="form-control" 
                        id="siswa_search" 
                        placeholder="Ketik NIS atau Nama Lengkap..."
                        autocomplete="off"
                    >
                    <div class="search-results" id="siswa_results"></div>
                </div>
                <input type="hidden" name="id_siswa" id="id_siswa">
                <div id="selected_siswa" style="margin-top: 12px;"></div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-bolt"></i> Jalankan Proses Generate
            </button>
            <a href="<?= base_url('admin/tagihan') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>



<script>
function selectTipe(card, value) {
    document.querySelectorAll('.radio-card').forEach(el => el.classList.remove('active'));
    card.classList.add('active');
    
    const kelasSection = document.getElementById('kelas_section');
    const siswaSection = document.getElementById('siswa_section');
    const kelasSelect = document.getElementById('id_kelas');
    const siswaInput = document.getElementById('id_siswa');
    
    if (value === 'kelas') {
        kelasSection.style.display = 'block';
        siswaSection.style.display = 'none';
        kelasSelect.required = true;
        siswaInput.required = false;
    } else if (value === 'siswa') {
        kelasSection.style.display = 'none';
        siswaSection.style.display = 'block';
        kelasSelect.required = false;
        siswaInput.required = true;
    } else {
        kelasSection.style.display = 'none';
        siswaSection.style.display = 'none';
        kelasSelect.required = false;
        siswaInput.required = false;
    }
}

function loadKelasByTahunAjaran() {
    const tahunAjaranId = document.getElementById('id_tahun_ajaran').value;
    const kelasSelect = document.getElementById('id_kelas');
    const options = kelasSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        const tahun = option.getAttribute('data-tahun');
        if (tahun === tahunAjaranId || !tahunAjaranId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    kelasSelect.value = '';
}

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
                    results.innerHTML = '<div class="search-result-item" style="color: #9ca3af; font-size:13px;">Pencarian tidak ditemukan...</div>';
                } else {
                    data.forEach(siswa => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `<strong>${siswa.nama_lengkap}</strong><br><small style="color: #64748b;">NIS: ${siswa.nis} • ${siswa.nama_kelas || 'Tanpa Kelas'}</small>`;
                        item.onclick = () => selectSiswa(siswa);
                        results.appendChild(item);
                    });
                }
                results.style.display = 'block';
            });
    }, 300);
});

function selectSiswa(siswa) {
    document.getElementById('id_siswa').value = siswa.id_siswa;
    document.getElementById('siswa_search').value = '';
    document.getElementById('siswa_results').style.display = 'none';
    
    document.getElementById('selected_siswa').innerHTML = `
        <div style="background: #ecfeff; border: 1.5px solid #0891b2; border-radius: 10px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; animation: fadeIn 0.3s;">
            <div>
                <div style="font-weight: 800; color: #0e7490; font-size: 14px;">${siswa.nama_lengkap}</div>
                <div style="color: #64748b; font-size: 12px;">NIS: ${siswa.nis}</div>
            </div>
            <button type="button" onclick="clearSiswa()" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 700;">
                <i class="fas fa-times"></i> &nbsp; Batalkan
            </button>
        </div>
    `;
}

function clearSiswa() {
    document.getElementById('id_siswa').value = '';
    document.getElementById('selected_siswa').innerHTML = '';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-box')) {
        document.getElementById('siswa_results').style.display = 'none';
    }
});
</script>

<?= $this->include('admin/layouts/footer') ?>