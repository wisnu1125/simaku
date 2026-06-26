<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL SCHOLARSHIP THEME ==================== */
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

/* Page Header */
.page-header { margin-bottom: 28px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 8px; }
.breadcrumb { font-size: 13px; color: var(--secondary); font-weight: 500; }
.breadcrumb a { color: var(--primary); text-decoration: none; }

/* Main Card */
.card-scholarship {
    background: #ffffff;
    border-radius: var(--radius);
    padding: 35px;
    border: 1px solid var(--border);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    max-width: 750px;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Alert Box Modern */
.alert-custom {
    padding: 18px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 14px;
    display: flex;
    gap: 15px;
    align-items: center;
    background: var(--primary-bg);
    border: 1px solid var(--primary-light);
    color: #0e7490;
}

/* Form Sections */
.form-section { margin-bottom: 35px; }
.form-section-title {
    font-size: 15px; font-weight: 800; color: var(--primary);
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
    display: flex; align-items: center; gap: 10px;
}

.form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #334155; }
.form-group label.required::after { content: " *"; color: #ef4444; }

.form-control {
    width: 100%; padding: 12px 16px; border: 1.5px solid var(--border);
    border-radius: 10px; font-size: 14px; background: #f8fafc;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.form-control:focus { outline: none; border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1); }

/* Mode Selector (Radio Cards) */
.radio-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 10px; }
.radio-card {
    border: 2px solid var(--border); border-radius: 12px; padding: 20px 15px;
    cursor: pointer; transition: all 0.3s ease; background: white;
}
.radio-card:hover { border-color: var(--primary-light); background: var(--primary-bg); }
.radio-card.active { border-color: var(--primary); background: var(--primary-bg); box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.1); }
.radio-card input[type="radio"] { display: none; }
.radio-icon { font-size: 28px; margin-bottom: 10px; display: block; }
.radio-label { font-size: 14px; font-weight: 800; color: #1e293b; display: block; margin-bottom: 4px; }
.radio-desc { font-size: 11px; color: #64748b; font-weight: 500; line-height: 1.3; }

/* Group Preview Box */
.grup-info-box {
    background: #f8fafc; border: 1.5px dashed var(--primary);
    padding: 16px; border-radius: 10px; margin-top: 15px;
}
.grup-preview-list { padding-left: 20px; font-size: 13px; color: var(--secondary); margin-top: 10px; }

/* Search Results */
.search-box { position: relative; }
.search-results {
    position: absolute; top: 105%; left: 0; right: 0; background: white;
    border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 100; display: none; max-height: 200px; overflow-y: auto;
}
.search-result-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f1f5f9; }
.search-result-item:hover { background: var(--primary-bg); color: var(--primary); }

/* Actions */
.form-actions { display: flex; gap: 15px; margin-top: 35px; padding-top: 25px; border-top: 1px solid var(--border); }
.btn { 
    padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 800; 
    display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-primary { background: var(--primary); color: white; }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); }

/* Utility */
#grup_section, #jenis_tagihan_field { display: none; }
.input-with-symbol { position: relative; }
.input-symbol { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-weight: 800; color: var(--primary); }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($beasiswa) ? 'Sunting' : 'Registrasi' ?> Beasiswa</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> &nbsp;•&nbsp; 
        <a href="<?= base_url('admin/beasiswa') ?>">Master Beasiswa</a> &nbsp;•&nbsp; 
        <span>Form Entry</span>
    </div>
</div>

<div class="card-scholarship">
    <div class="alert-custom">
        <i class="fas fa-magic fa-lg"></i>
        <div>
            <strong>Automatisasi Potongan:</strong> Nilai beasiswa akan langsung memotong nominal kewajiban pada tagihan siswa. Sistem akan melakukan <em>re-calculation</em> otomatis setelah data disimpan.
        </div>
    </div>
    
    <form action="<?= isset($beasiswa) ? base_url('admin/beasiswa/update/' . $beasiswa['id_beasiswa']) : base_url('admin/beasiswa/store') ?>" method="POST">
        
        <div class="form-section">
            <div class="form-section-title"><i class="fas fa-user-graduate"></i> Identitas Penerima</div>
            <div class="form-group">
                <label for="siswa_search" class="required">Cari Siswa</label>
                <div class="search-box">
                    <input type="text" class="form-control" id="siswa_search" placeholder="Masukkan NIS atau Nama Siswa..." autocomplete="off">
                    <div class="search-results" id="siswa_results"></div>
                </div>
                <input type="hidden" name="id_siswa" id="id_siswa" value="<?= old('id_siswa', $beasiswa['id_siswa'] ?? '') ?>" required>
                <div id="selected_siswa" style="margin-top: 10px;"></div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title"><i class="fas fa-award"></i> Konfigurasi Beasiswa</div>
            
            <div class="form-group">
                <label for="nama_beasiswa" class="required">Nama / Program Beasiswa</label>
                <input type="text" class="form-control" id="nama_beasiswa" name="nama_beasiswa" 
                       placeholder="Contoh: Beasiswa Prestasi Akademik 2024" 
                       value="<?= old('nama_beasiswa', $beasiswa['nama_beasiswa'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="required">Lingkup Pemberian (Mode)</label>
                <div class="radio-grid">
                    <label class="radio-card active" onclick="selectMode(this, 'single')">
                        <input type="radio" name="mode_beasiswa" id="mode_single" value="single" 
                               <?= (old('mode_beasiswa', $beasiswa['mode_beasiswa'] ?? 'single') === 'single') ? 'checked' : '' ?> required>
                        <span class="radio-icon">📄</span>
                        <span class="radio-label">Single Item</span>
                        <span class="radio-desc">Potongan hanya pada satu jenis tagihan spesifik.</span>
                    </label>
                    
                    <label class="radio-card" onclick="selectMode(this, 'bulk')">
                        <input type="radio" name="mode_beasiswa" id="mode_bulk" value="bulk" 
                               <?= (old('mode_beasiswa', $beasiswa['mode_beasiswa'] ?? '') === 'bulk') ? 'checked' : '' ?> required>
                        <span class="radio-icon">🗂️</span>
                        <span class="radio-label">Bulk Group</span>
                        <span class="radio-desc">Potongan berlaku untuk seluruh tagihan dalam grup.</span>
                    </label>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div id="jenis_tagihan_field">
                    <div class="form-group">
                        <label for="id_jenis_tagihan" class="required">Jenis Tagihan</label>
                        <select class="form-control" id="id_jenis_tagihan" name="id_jenis_tagihan">
                            <option value="">-- Pilih Tagihan --</option>
                            <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
                                <optgroup label="<?= esc($grup) ?>">
                                    <?php foreach ($items as $jt): ?>
                                        <option value="<?= $jt['id_jenis_tagihan'] ?>" 
                                            <?= (old('id_jenis_tagihan', $beasiswa['id_jenis_tagihan'] ?? '') == $jt['id_jenis_tagihan']) ? 'selected' : '' ?>>
                                            <?= esc($jt['nama_tagihan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="grup_section" style="grid-column: span 2;">
                    <div class="form-group">
                        <label for="selected_grup" class="required">Pilih Grup Tagihan</label>
                        <select class="form-control" id="selected_grup" name="selected_grup">
                            <option value="">-- Pilih Grup --</option>
                            <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
                                <option value="<?= esc($grup) ?>"><?= esc($grup) ?> (<?= count($items) ?> Item)</option>
                            <?php endforeach; ?>
                        </select>
                        <div id="grup_preview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_tahun_ajaran" class="required">Tahun Ajaran</label>
                    <select class="form-control" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                        <option value="">-- Pilih Tahun --</option>
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_tahun_ajaran'] ?>" 
                                <?= (old('id_tahun_ajaran', $beasiswa['id_tahun_ajaran'] ?? '') == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                                <?= esc($ta['nama_tahun_ajaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="required">Metode Potongan</label>
                    <div style="display: flex; gap: 15px; margin-top: 10px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="tipe_beasiswa" value="nominal" onchange="toggleTipe()" <?= (old('tipe_beasiswa', $beasiswa['tipe_beasiswa'] ?? 'nominal') === 'nominal') ? 'checked' : '' ?>> Rp (Nominal)
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="tipe_beasiswa" value="persentase" onchange="toggleTipe()" <?= (old('tipe_beasiswa', $beasiswa['tipe_beasiswa'] ?? '') === 'persentase') ? 'checked' : '' ?>> % (Persen)
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nilai_beasiswa" class="required">Nilai Beasiswa</label>
                    <div class="input-with-symbol">
                        <input type="number" class="form-control" id="nilai_beasiswa" name="nilai_beasiswa" 
                               value="<?= old('nilai_beasiswa', $beasiswa['nilai_beasiswa'] ?? '') ?>" required>
                        <span id="tipe_symbol" class="input-symbol">Rp</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Catatan / Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?= old('keterangan', $beasiswa['keterangan'] ?? '') ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> <?= isset($beasiswa) ? 'Perbarui Data' : 'Simpan Beasiswa' ?>
            </button>
            <a href="<?= base_url('admin/beasiswa') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>



<script>
function selectMode(card, value) {
    document.querySelectorAll('.radio-card').forEach(el => el.classList.remove('active'));
    card.classList.add('active');
    card.querySelector('input').checked = true;
    
    const isSingle = (value === 'single');
    document.getElementById('jenis_tagihan_field').style.display = isSingle ? 'block' : 'none';
    document.getElementById('grup_section').style.display = isSingle ? 'none' : 'block';
    
    document.getElementById('id_jenis_tagihan').required = isSingle;
    document.getElementById('selected_grup').required = !isSingle;
}

function toggleTipe() {
    const isNominal = document.querySelector('input[name="tipe_beasiswa"]:checked').value === 'nominal';
    document.getElementById('tipe_symbol').textContent = isNominal ? 'Rp' : '%';
    document.getElementById('nilai_beasiswa').max = isNominal ? '' : '100';
}

// Grup Preview
document.getElementById('selected_grup')?.addEventListener('change', function() {
    const selected = this.value;
    const grupData = <?= json_encode($jenis_tagihan_grouped ?? []) ?>;
    const items = grupData[selected] || [];
    const preview = document.getElementById('grup_preview');
    
    if (items.length > 0) {
        let html = '<div class="grup-info-box"><strong>Item dalam grup ini:</strong><ul class="grup-preview-list">';
        items.forEach(i => html += `<li>${i.nama_tagihan}</li>`);
        html += '</ul></div>';
        preview.innerHTML = html;
    } else {
        preview.innerHTML = '';
    }
});

// Student Search
let timeout;
document.getElementById('siswa_search').addEventListener('input', function() {
    clearTimeout(timeout);
    const k = this.value;
    if (k.length < 2) { document.getElementById('siswa_results').style.display = 'none'; return; }
    
    timeout = setTimeout(() => {
        fetch(`<?= base_url('admin/siswa/search') ?>?keyword=${k}`)
            .then(r => r.json())
            .then(data => {
                const res = document.getElementById('siswa_results');
                res.innerHTML = data.map(s => `
                    <div class="search-result-item" onclick="selectSiswa(${JSON.stringify(s).replace(/"/g, '&quot;')})">
                        <strong>${s.nama_lengkap}</strong><br><small>NIS: ${s.nis} • ${s.nama_kelas || 'Belum dikelas'}</small>
                    </div>
                `).join('');
                res.style.display = 'block';
            });
    }, 300);
});

function selectSiswa(s) {
    document.getElementById('id_siswa').value = s.id_siswa;
    document.getElementById('siswa_search').value = '';
    document.getElementById('siswa_results').style.display = 'none';
    document.getElementById('selected_siswa').innerHTML = `
        <div style="background:var(--primary-bg); border:1px solid var(--primary); border-radius:10px; padding:12px; display:flex; justify-content:space-between; align-items:center;">
            <div><strong>${s.nama_lengkap}</strong><br><small>NIS: ${s.nis}</small></div>
            <button type="button" onclick="clearSiswa()" style="background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">Hapus</button>
        </div>`;
}

function clearSiswa() {
    document.getElementById('id_siswa').value = '';
    document.getElementById('selected_siswa').innerHTML = '';
}

// Initialize on Load
document.addEventListener('DOMContentLoaded', function() {
    const currentMode = "<?= old('mode_beasiswa', $beasiswa['mode_beasiswa'] ?? 'single') ?>";
    const modeCard = currentMode === 'single' ? document.querySelectorAll('.radio-card')[0] : document.querySelectorAll('.radio-card')[1];
    selectMode(modeCard, currentMode);
    toggleTipe();
});
</script>

<?= $this->include('admin/layouts/footer') ?>