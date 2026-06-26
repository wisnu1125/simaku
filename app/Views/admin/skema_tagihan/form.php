<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== MODERN SKEMA TAGIHAN CSS ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #f8fafc;
}

/* ==================== PAGE HEADER ==================== */
.page-header {
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 3px solid #e2e8f0;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.breadcrumb {
    font-size: 14px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.breadcrumb a {
    color: #14b8a6;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb a:hover {
    color: #0d9488;
}

/* ==================== CARD ==================== */
.card {
    background: #ffffff;
    border-radius: 16px;
    padding: 40px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    max-width: 1200px;
    margin: 0 auto;
}

/* ==================== FORM ==================== */
.form-section {
    margin-bottom: 40px;
}

.form-section-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 3px solid #14b8a6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title::before {
    content: '';
    width: 4px;
    height: 24px;
    background: linear-gradient(180deg, #14b8a6, #0d9488);
    border-radius: 2px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 24px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    color: #334155;
    font-weight: 600;
    font-size: 14px;
}

.form-group label.required::after {
    content: " *";
    color: #ef4444;
    font-weight: 700;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
    background: #f8fafc;
}

.form-control:hover {
    border-color: #cbd5e1;
    background: #ffffff;
}

.form-control:focus {
    outline: none;
    border-color: #14b8a6;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
}

.form-control:disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}

.form-hint {
    font-size: 13px;
    color: #64748b;
    margin-top: 6px;
    font-style: italic;
}

/* ==================== ALERT ==================== */
.alert-info {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 2px solid #60a5fa;
    color: #1e40af;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 32px;
    display: flex;
    align-items: start;
    gap: 12px;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
}

.alert-info i {
    font-size: 20px;
    color: #2563eb;
}

/* ==================== RADIO GROUP ==================== */
.radio-group {
    display: flex;
    gap: 16px;
    margin-top: 12px;
}

.radio-item {
    flex: 1;
    display: flex;
    align-items: start;
    gap: 12px;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f8fafc;
    position: relative;
}

.radio-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #14b8a6, #0d9488);
    border-radius: 12px 0 0 12px;
    transform: scaleX(0);
    transition: transform 0.3s;
}

.radio-item:hover {
    border-color: #14b8a6;
    background: #f0fdfa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.15);
}

.radio-item:has(input[type="radio"]:checked) {
    border-color: #14b8a6;
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2);
}

.radio-item:has(input[type="radio"]:checked)::before {
    transform: scaleX(1);
}

.radio-item input[type="radio"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #14b8a6;
}

.radio-item label {
    margin: 0;
    cursor: pointer;
    flex: 1;
}

.radio-title {
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    color: #0f172a;
}

.radio-title i {
    color: #14b8a6;
    font-size: 18px;
}

.radio-desc {
    font-size: 12px;
    color: #64748b;
}

/* ==================== SEARCH BOX ==================== */
.search-box {
    position: relative;
}

.search-results {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    max-height: 280px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.search-result-item {
    padding: 14px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s;
}

.search-result-item:hover {
    background: linear-gradient(90deg, #f0fdfa, #ffffff);
    border-left: 4px solid #14b8a6;
    padding-left: 12px;
}

/* ==================== CHECKLIST ==================== */
.tagihan-checklist-group {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.grup-header {
    font-size: 16px;
    font-weight: 700;
    color: #14b8a6;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 3px solid #14b8a6;
    display: flex;
    align-items: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.grup-header i {
    font-size: 20px;
}

.select-all-group {
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    border: 2px solid #5eead4;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.select-all-group:hover {
    background: linear-gradient(135deg, #ccfbf1, #99f6e4);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(20, 184, 166, 0.15);
}

.select-all-group input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #14b8a6;
}

.select-all-group label {
    margin: 0;
    cursor: pointer;
    font-weight: 700;
    color: #0d9488;
    font-size: 14px;
}

.tagihan-item {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    display: grid;
    grid-template-columns: auto 1fr 320px;
    gap: 20px;
    align-items: center;
    transition: all 0.3s;
    position: relative;
}

.tagihan-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 5px;
    background: linear-gradient(180deg, #14b8a6, #0d9488);
    border-radius: 12px 0 0 12px;
    transform: scaleX(0);
    transition: transform 0.3s;
}

.tagihan-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateX(5px);
}

.tagihan-item.checked {
    background: linear-gradient(135deg, #f0fdfa, #ffffff);
    border-color: #14b8a6;
    box-shadow: 0 4px 16px rgba(20, 184, 166, 0.15);
}

.tagihan-item.checked::before {
    transform: scaleX(1);
}

.tagihan-checkbox input[type="checkbox"] {
    width: 22px;
    height: 22px;
    cursor: pointer;
    accent-color: #14b8a6;
}

.tagihan-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tagihan-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 15px;
}

.tagihan-meta {
    font-size: 12px;
    color: #64748b;
    display: flex;
    gap: 16px;
}

.tagihan-meta i {
    color: #14b8a6;
}

.tagihan-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-tetap {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

.badge-bulanan {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.badge-insidental {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.tagihan-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: start;
}

.tagihan-input-wrapper > div {
    flex: 1;
}

.tagihan-input-wrapper input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    color: #14b8a6;
    background: #f8fafc;
    font-family: 'SF Mono', 'Monaco', monospace;
    transition: all 0.2s;
}

.tagihan-input-wrapper input:focus {
    border-color: #14b8a6;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
}

.tagihan-input-wrapper input:disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}

.bulan-select {
    width: 130px;
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    transition: all 0.2s;
}

.bulan-select:focus {
    outline: none;
    border-color: #14b8a6;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
}

.terbilang-mini {
    font-size: 11px;
    color: #065f46;
    font-style: italic;
    margin-top: 6px;
    padding: 6px 10px;
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    border-radius: 6px;
    border-left: 3px solid #14b8a6;
    display: none;
    animation: fadeIn 0.3s ease;
}

.terbilang-mini i {
    color: #14b8a6;
    margin-right: 6px;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ==================== SUMMARY ==================== */
.summary-box {
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    border-radius: 16px;
    padding: 24px 32px;
    margin-top: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 30px rgba(20, 184, 166, 0.3);
    position: relative;
    overflow: hidden;
}

.summary-box::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.summary-info {
    display: flex;
    gap: 40px;
    position: relative;
    z-index: 1;
}

.summary-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.summary-label {
    font-size: 12px;
    color: #ccfbf1;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.summary-value {
    font-size: 32px;
    font-weight: 900;
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* ==================== BUTTONS ==================== */
.form-actions {
    display: flex;
    gap: 16px;
    position: relative;
    z-index: 1;
}

.btn {
    padding: 12px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    position: relative;
    overflow: hidden;
    text-decoration: none;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn i,
.btn span {
    position: relative;
    z-index: 1;
}

.btn-primary {
    background: linear-gradient(135deg, #ffffff, #f1f5f9);
    color: #0d9488;
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 255, 255, 0.5);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .card {
        padding: 24px 16px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .radio-group {
        flex-direction: column;
    }
    
    .tagihan-item {
        grid-template-columns: auto 1fr;
    }
    
    .tagihan-input-wrapper {
        grid-column: 1 / -1;
    }
    
    .summary-box {
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }
    
    .summary-info {
        width: 100%;
        justify-content: space-around;
    }
    
    .form-actions {
        width: 100%;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
    }
}
</style>

<div class="page-header">
    <h1 class="page-title">Generate Skema Tagihan (Bulk)</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> / 
        <a href="<?= base_url('admin/skema-tagihan') ?>">Skema Tagihan</a> / 
        Generate Bulk
    </div>
</div>

<div class="card">
    <div class="alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Info:</strong> Centang tagihan yang ingin dibuat skemanya, isi nominal, lalu klik Generate. Tagihan bulanan akan otomatis dibuat 12 skema (Bulan 1-12).
        </div>
    </div>
    
    <form action="<?= base_url('admin/skema-tagihan/store-bulk') ?>" method="POST" id="formBulk">
        
        <!-- Filter -->
        <div class="form-section">
            <div class="form-section-title">Pilih Target & Tahun Ajaran</div>
            
            <!-- TARGET SKEMA -->
            <div class="form-group">
                <label class="required">Target Skema</label>
                <div class="radio-group" style="flex-direction: row; gap: 16px;">
                    <div class="radio-item" style="flex: 1;">
                        <input 
                            type="radio" 
                            id="target_kelas" 
                            name="target_skema" 
                            value="kelas"
                            checked
                            onchange="toggleTarget()"
                        >
                        <label for="target_kelas" style="margin: 0;">
                            <div class="radio-title">
                                <i class="fas fa-school"></i> Per Kelas
                            </div>
                            <div class="radio-desc">Buat skema untuk 1 kelas</div>
                        </label>
                    </div>
                    
                    <div class="radio-item" style="flex: 1;">
                        <input 
                            type="radio" 
                            id="target_semua_siswa" 
                            name="target_skema" 
                            value="semua_siswa"
                            onchange="toggleTarget()"
                        >
                        <label for="target_semua_siswa" style="margin: 0;">
                            <div class="radio-title">
                                <i class="fas fa-users"></i> Semua Siswa
                            </div>
                            <div class="radio-desc">Buat skema untuk semua siswa</div>
                        </label>
                    </div>
                    
                    <div class="radio-item" style="flex: 1;">
                        <input 
                            type="radio" 
                            id="target_siswa" 
                            name="target_skema" 
                            value="siswa"
                            onchange="toggleTarget()"
                        >
                        <label for="target_siswa" style="margin: 0;">
                            <div class="radio-title">
                                <i class="fas fa-user"></i> Per Siswa Tertentu
                            </div>
                            <div class="radio-desc">Buat skema untuk 1 siswa spesifik</div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="id_tahun_ajaran" class="required">Tahun Ajaran</label>
                    <select 
                        class="form-control" 
                        id="id_tahun_ajaran" 
                        name="id_tahun_ajaran"
                        onchange="filterKelas()"
                        required
                    >
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_tahun_ajaran'] ?>">
                                <?= esc($ta['nama_tahun_ajaran']) ?>
                                <?= $ta['status'] === 'aktif' ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" id="kelas_section">
                    <label for="id_kelas" class="required">Kelas</label>
                    <select 
                        class="form-control" 
                        id="id_kelas" 
                        name="id_kelas"
                    >
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" data-tahun="<?= $k['id_tahun_ajaran'] ?>">
                                <?= esc($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- SISWA SEARCH SECTION -->
                <div class="form-group" id="siswa_section" style="display: none;">
                    <label for="siswa_search" class="required">Cari Siswa</label>
                    <div class="search-box">
                        <input 
                            type="text" 
                            class="form-control" 
                            id="siswa_search" 
                            placeholder="Ketik NIS atau Nama Siswa..."
                            autocomplete="off"
                        >
                        <div class="search-results" id="siswa_results"></div>
                    </div>
                    <input type="hidden" name="id_siswa" id="id_siswa">
                    <div id="selected_siswa" style="margin-top: 8px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Checklist Tagihan -->
        <div class="form-section">
            <div class="form-section-title">Pilih Tagihan & Nominal</div>
            
            <?php if (isset($jenis_tagihan_grouped)): ?>
                <?php foreach ($jenis_tagihan_grouped as $grup => $items): ?>
                    <div class="tagihan-checklist-group">
                        <div class="grup-header">
                            <i class="fas fa-folder-open"></i>
                            <?= esc($grup) ?>
                        </div>
                        
                        <!-- Select All -->
                        <div class="select-all-group" onclick="toggleGrup('<?= esc($grup) ?>')">
                            <input 
                                type="checkbox" 
                                id="select_all_<?= md5($grup) ?>"
                                class="select-all-checkbox"
                                data-grup="<?= esc($grup) ?>"
                            >
                            <label for="select_all_<?= md5($grup) ?>">
                                Pilih Semua (<?= count($items) ?> tagihan)
                            </label>
                        </div>
                        
                        <?php foreach ($items as $jt): ?>
                            <div class="tagihan-item" id="item_<?= $jt['id_jenis_tagihan'] ?>">
                                <!-- Checkbox -->
                                <div class="tagihan-checkbox">
                                    <input 
                                        type="checkbox" 
                                        name="tagihan[]" 
                                        value="<?= $jt['id_jenis_tagihan'] ?>"
                                        id="check_<?= $jt['id_jenis_tagihan'] ?>"
                                        data-grup="<?= esc($grup) ?>"
                                        data-tipe="<?= $jt['tipe_tagihan'] ?>"
                                        onchange="toggleNominal(<?= $jt['id_jenis_tagihan'] ?>)"
                                    >
                                </div>
                                
                                <!-- Info Tagihan -->
                                <div class="tagihan-info">
                                    <div class="tagihan-name">
                                        <label for="check_<?= $jt['id_jenis_tagihan'] ?>" style="margin:0;cursor:pointer;">
                                            <?= esc($jt['nama_tagihan']) ?>
                                        </label>
                                    </div>
                                    <div class="tagihan-meta">
                                        <span class="tagihan-badge badge-<?= $jt['tipe_tagihan'] ?>">
                                            <?= ucfirst($jt['tipe_tagihan']) ?>
                                        </span>
                                        <?php if ($jt['tipe_tagihan'] === 'bulanan'): ?>
                                            <span style="color:#14b8a6;">
                                                <i class="fas fa-calendar-check"></i> 
                                                Auto-generate 12 bulan
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Input Nominal -->
                                <div class="tagihan-input-wrapper">
                                    <div style="flex: 1;">
                                        <input 
                                            type="text" 
                                            name="nominal_display_<?= $jt['id_jenis_tagihan'] ?>"
                                            id="nominal_display_<?= $jt['id_jenis_tagihan'] ?>"
                                            class="nominal-input"
                                            placeholder="Rp 0"
                                            disabled
                                            autocomplete="off"
                                        >
                                        <input 
                                            type="hidden" 
                                            name="nominal[<?= $jt['id_jenis_tagihan'] ?>]"
                                            id="nominal_<?= $jt['id_jenis_tagihan'] ?>"
                                        >
                                        <div class="terbilang-mini" id="terbilang_<?= $jt['id_jenis_tagihan'] ?>">
                                            <i class="fas fa-spell-check"></i> 
                                            <span id="terbilang_text_<?= $jt['id_jenis_tagihan'] ?>"></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($jt['tipe_tagihan'] === 'bulanan'): ?>
                                        <select 
                                            class="bulan-select" 
                                            name="bulan[<?= $jt['id_jenis_tagihan'] ?>]"
                                            id="bulan_<?= $jt['id_jenis_tagihan'] ?>"
                                            disabled
                                        >
                                            <option value="">Semua</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>">Bulan <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-info">
                <div class="summary-item">
                    <div class="summary-label">Total Dipilih</div>
                    <div class="summary-value" id="total_selected">0</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Skema</div>
                    <div class="summary-value" id="total_skema">0</div>
                </div>
            </div>
            <div class="form-actions" style="margin:0;padding:0;border:0;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-magic"></i>
                    <span>Generate</span>
                </button>
                <a href="<?= base_url('admin/skema-tagihan') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </a>
            </div>
        </div>
        
    </form>
</div>

<script>
// ==================== FUNGSI TERBILANG ====================
function terbilang(angka) {
    const bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    
    if (angka < 12) return bilangan[angka];
    else if (angka < 20) return terbilang(angka - 10) + ' Belas';
    else if (angka < 100) return terbilang(Math.floor(angka / 10)) + ' Puluh ' + terbilang(angka % 10);
    else if (angka < 200) return 'Seratus ' + terbilang(angka - 100);
    else if (angka < 1000) return terbilang(Math.floor(angka / 100)) + ' Ratus ' + terbilang(angka % 100);
    else if (angka < 2000) return 'Seribu ' + terbilang(angka - 1000);
    else if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + ' Ribu ' + terbilang(angka % 1000);
    else if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + ' Juta ' + terbilang(angka % 1000000);
    else if (angka < 1000000000000) return terbilang(Math.floor(angka / 1000000000)) + ' Miliar ' + terbilang(angka % 1000000000);
    else return 'Angka terlalu besar';
}

// ==================== FORMAT RUPIAH ====================
function formatRupiah(angka) {
    const numberString = angka.toString().replace(/[^,\d]/g, '');
    const split = numberString.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    return rupiah;
}

// ==================== TOGGLE NOMINAL INPUT ====================
function toggleNominal(id) {
    const checkbox = document.getElementById('check_' + id);
    const nominalDisplay = document.getElementById('nominal_display_' + id);
    const nominalHidden = document.getElementById('nominal_' + id);
    const item = document.getElementById('item_' + id);
    const bulanSelect = document.getElementById('bulan_' + id);
    
    if (checkbox.checked) {
        nominalDisplay.disabled = false;
        if (bulanSelect) bulanSelect.disabled = false;
        item.classList.add('checked');
        nominalDisplay.focus();
    } else {
        nominalDisplay.disabled = true;
        nominalDisplay.value = '';
        nominalHidden.value = '';
        if (bulanSelect) {
            bulanSelect.disabled = true;
            bulanSelect.value = '';
        }
        item.classList.remove('checked');
    }
    
    updateSummary();
}

// ==================== TOGGLE GRUP ====================
function toggleGrup(grup) {
    const selectAllCheckbox = document.querySelector('.select-all-checkbox[data-grup="' + grup + '"]');
    const checked = selectAllCheckbox.checked;
    
    const checkboxes = document.querySelectorAll('input[type="checkbox"][data-grup="' + grup + '"][name="tagihan[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
        toggleNominal(checkbox.value);
    });
}

// ==================== UPDATE SUMMARY ====================
function updateSummary() {
    const checkedBoxes = document.querySelectorAll('input[name="tagihan[]"]:checked');
    let totalSelected = checkedBoxes.length;
    let totalSkema = 0;
    
    checkedBoxes.forEach(checkbox => {
        const tipe = checkbox.getAttribute('data-tipe');
        if (tipe === 'bulanan') {
            totalSkema += 12;
        } else {
            totalSkema += 1;
        }
    });
    
    document.getElementById('total_selected').textContent = totalSelected;
    document.getElementById('total_skema').textContent = totalSkema;
}

// ==================== SETUP NOMINAL INPUTS ====================
document.addEventListener('DOMContentLoaded', function() {
    const nominalInputs = document.querySelectorAll('.nominal-input');
    
    nominalInputs.forEach(input => {
        const id = input.id.replace('nominal_display_', '');
        const hiddenInput = document.getElementById('nominal_' + id);
        const terbilangBox = document.getElementById('terbilang_' + id);
        const terbilangText = document.getElementById('terbilang_text_' + id);
        
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            
            if (value === '' || value === '0') {
                input.value = '';
                hiddenInput.value = '';
                if (terbilangBox) terbilangBox.style.display = 'none';
                return;
            }
            
            const angka = parseInt(value);
            const formatted = formatRupiah(value);
            input.value = formatted;
            hiddenInput.value = angka;
            
            if (terbilangBox && terbilangText) {
                const terbilangStr = terbilang(angka);
                if (terbilangStr) {
                    terbilangText.textContent = terbilangStr.trim() + ' Rupiah';
                    terbilangBox.style.display = 'block';
                }
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(String.fromCharCode(e.keyCode))) {
                e.preventDefault();
            }
        });
    });
});

// ==================== TOGGLE TARGET ====================
function toggleTarget() {
    const targetKelas = document.getElementById('target_kelas').checked;
    const targetSemuaSiswa = document.getElementById('target_semua_siswa').checked;
    const targetSiswa = document.getElementById('target_siswa').checked;
    
    const kelasSection = document.getElementById('kelas_section');
    const siswaSection = document.getElementById('siswa_section');
    const kelasSelect = document.getElementById('id_kelas');
    const siswaInput = document.getElementById('id_siswa');
    
    kelasSection.style.display = 'none';
    siswaSection.style.display = 'none';
    kelasSelect.required = false;
    siswaInput.required = false;
    
    if (targetKelas) {
        kelasSection.style.display = 'block';
        kelasSelect.required = true;
    } else if (targetSemuaSiswa) {
        // Tidak perlu pilih apa-apa
    } else if (targetSiswa) {
        siswaSection.style.display = 'block';
        siswaInput.required = true;
    }
}

// ==================== SISWA SEARCH ====================
let searchTimeout;
document.getElementById('siswa_search')?.addEventListener('input', function() {
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
                    results.innerHTML = '<div class="search-result-item" style="color: #9ca3af;">Tidak ada hasil</div>';
                } else {
                    data.forEach(siswa => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `<strong>${siswa.nama_lengkap}</strong><br><small style="color: #6b7280;">NIS: ${siswa.nis} - ${siswa.nama_kelas || 'Belum dikelas'}</small>`;
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
        <div style="background: #f0fdfa; border: 1px solid #14b8a6; border-radius: 6px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>${siswa.nama_lengkap}</strong><br>
                <small style="color: #6b7280;">NIS: ${siswa.nis}</small>
            </div>
            <button type="button" onclick="clearSiswa()" style="background: #ef4444; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer;">
                <i class="fas fa-times"></i>
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
        const results = document.getElementById('siswa_results');
        if (results) results.style.display = 'none';
    }
});

// ==================== FILTER KELAS BY TAHUN ====================
function filterKelas() {
    const tahunId = document.getElementById('id_tahun_ajaran').value;
    const kelasSelect = document.getElementById('id_kelas');
    const options = kelasSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const tahun = option.getAttribute('data-tahun');
        if (tahun === tahunId || !tahunId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    kelasSelect.value = '';
}

// ==================== FORM VALIDATION ====================
document.getElementById('formBulk').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('input[name="tagihan[]"]:checked');
    const targetSkema = document.querySelector('input[name="target_skema"]:checked').value;
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('⚠️ Pilih minimal 1 tagihan!');
        return false;
    }
    
    if (targetSkema === 'kelas') {
        const kelas = document.getElementById('id_kelas').value;
        if (!kelas) {
            e.preventDefault();
            alert('⚠️ Pilih kelas terlebih dahulu!');
            return false;
        }
    } else if (targetSkema === 'siswa') {
        const siswa = document.getElementById('id_siswa').value;
        if (!siswa) {
            e.preventDefault();
            alert('⚠️ Pilih siswa terlebih dahulu!');
            return false;
        }
    }
    
    let hasError = false;
    checkedBoxes.forEach(checkbox => {
        const id = checkbox.value;
        const nominal = document.getElementById('nominal_' + id).value;
        
        if (!nominal || nominal == 0) {
            hasError = true;
            const display = document.getElementById('nominal_display_' + id);
            display.style.borderColor = '#ef4444';
            display.focus();
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('⚠️ Isi nominal untuk semua tagihan yang dipilih!');
        return false;
    }
    
    let confirmMsg = `Yakin generate ${document.getElementById('total_skema').textContent} skema tagihan`;
    if (targetSkema === 'kelas') {
        const kelasText = document.getElementById('id_kelas').options[document.getElementById('id_kelas').selectedIndex].text;
        confirmMsg += ` untuk kelas ${kelasText}?`;
    } else if (targetSkema === 'semua_siswa') {
        confirmMsg += ` untuk SEMUA SISWA?`;
    } else if (targetSkema === 'siswa') {
        confirmMsg += ` untuk siswa terpilih?`;
    }
    
    return confirm(confirmMsg);
});
</script>

<?= $this->include('admin/layouts/footer') ?>