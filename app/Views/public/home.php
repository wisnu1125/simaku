<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/favicon.png') ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Palette Warna */
            --primary: #0ea5e9;       /* Sky Blue Utama */
            --primary-dark: #0284c7;  
            --primary-soft: #f0f9ff;
            --accent: #38bdf8;
            
            --text-dark: #1e293b;     /* Slate 800 */
            --text-grey: #64748b;     /* Slate 500 */
            --bg-body: #f8fafc;
            
            --danger: #ef4444;
            --white: #ffffff;
            
            /* Spacing & Radius */
            --radius-std: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Container Tengah */
        .main-wrapper {
            width: 100%;
            max-width: 480px;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        /* ================= 1. HEADER (LOGO KIRI & JAM KANAN) ================= */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            margin-top: 10px;
        }

        /* Logo dalam Card Khusus */
        .logo-card {
            background: #ffffff;
            padding: 10px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border: 1px solid rgba(255,255,255,0.8);
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-fallback {
            font-size: 24px;
            color: var(--primary);
            display: none;
        }

        /* Jam Realtime */
        .time-badge {
            text-align: right;
            font-size: 12px;
            color: var(--text-grey);
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(4px);
            padding: 8px 12px;
            border-radius: 12px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .time-badge strong {
            display: block;
            color: var(--primary-dark);
            font-size: 14px;
        }

        /* ================= 2. HERO TEXT (ATAS FORM) ================= */
        .hero-info {
            margin-bottom: 30px;
            animation: fadeIn 0.8s ease;
        }

        .app-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .headline {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        /* Box Penjelasan "Double Check" */
        .info-card-highlight {
            background: linear-gradient(to right bottom, #ffffff, #f0f9ff);
            border-left: 4px solid var(--primary);
            padding: 15px;
            border-radius: 0 16px 16px 0;
            box-shadow: 0 10px 30px -10px rgba(14, 165, 233, 0.1);
            margin-bottom: 10px;
        }

        .info-card-highlight p {
            font-size: 12px;
            line-height: 1.6;
            color: var(--text-grey);
            margin-bottom: 8px;
        }

        .info-card-highlight p:last-child {
            margin-bottom: 0;
        }

        .highlight-text {
            color: var(--primary-dark);
            font-weight: 600;
        }

        /* ================= 3. FORM SECTION ================= */
        .form-section {
            width: 100%;
            position: relative;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-left: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .clean-input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            color: var(--text-dark);
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-std);
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .clean-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.15);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .clean-input:focus + .input-icon {
            color: var(--primary);
        }

        .hint {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 5px;
            margin-left: 4px;
            text-align: right;
        }

        /* --- TAMBAHAN STYLE UNTUK AUTOCOMPLETE AJAX --- */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 16px 16px;
            margin-top: -2px; /* Supaya nempel sama input */
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            z-index: 100;
            display: none;
        }

        .search-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .search-item:last-child {
            border-bottom: none;
        }

        .search-item:hover {
            background: var(--primary-soft);
        }

        .search-item strong {
            display: block;
            font-size: 14px;
            color: var(--text-dark);
        }

        .search-item small {
            font-size: 11px;
            color: var(--text-grey);
        }

        .loading-spinner {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            display: none;
        }
        /* --- END TAMBAHAN AJAX --- */

        /* Button */
        .btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--text-dark);
            color: white;
            border: none;
            border-radius: var(--radius-std);
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.2s, background 0.2s;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-top: 10px;
        }

        .btn-primary:hover:not(:disabled) {
            background: black;
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-primary:disabled, .btn-primary.loading {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Alerts */
        .alert-error {
            background: #fef2f2;
            color: var(--danger);
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #fee2e2;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cooldown-badge {
            margin-top: 15px;
            background: #fff1f2;
            color: var(--danger);
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            font-size: 13px;
            font-weight: 500;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* ================= 4. INFO SECTION (BAWAH FORM - RESTORED) ================= */
        .info-container {
            width: 100%;
            animation: fadeIn 1s ease;
        }

        .info-header {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-grey);
            font-weight: 700;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid var(--primary);
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.7); /* Transparan dikit */
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .info-icon-box {
            min-width: 36px;
            height: 36px;
            background: var(--primary-soft);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 16px;
        }

        .info-content h4 {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .info-content p {
            font-size: 11px;
            color: var(--text-grey);
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            padding-bottom: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

    </style>
</head>
<body>

    <div class="main-wrapper">

        <div class="top-bar">
            <div class="logo-card" id="logoIcon">
                <img src="<?= base_url('assets/img/logoavatar.png') ?>" 
                     alt="Logo" 
                     class="logo-img"
                     id="logoImg"
                     onerror="handleLogoError()">
                <div class="logo-fallback" id="logoFallback">
                    <i class="fa-solid fa-school"></i>
                </div>
            </div>

            <div class="time-badge">
                <span id="liveDate">Memuat...</span>
                <strong id="liveTime">--:--</strong>
            </div>
        </div>

        <div class="hero-info">
            <span class="app-name">Sistem Informasi Manajemen Keuangan</span>
            <h1 class="headline">Cek Tagihan<br>dan Riwayat Pembayaran.</h1>
            
            <div class="info-card-highlight">
                <p>
                    <strong>Mengapa perlu cek disini?</strong><br>
                    Untuk memastikan akurasi data antara catatan Anda dan sekolah (<em>Double Check</em>).
                </p>
                <div style="margin-top: 8px; height: 1px; background: rgba(0,0,0,0.05); width: 100%;"></div>
                <p style="margin-top: 8px;">
                    Hal ini meminimalisir <span class="highlight-text">Human Error</span> admin/bendahara. Kecil kemungkinan tagihan muncul jika sudah lunas, namun pengecekan mandiri sangat dianjurkan.
                </p>
            </div>
        </div>

        <div class="form-section">
            
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('cek-tagihan') ?>" method="POST" id="formCekTagihan" autocomplete="on">
                
                <div class="form-group">
                    <label class="form-label" for="search_input">Cari Nama Siswa</label>
                    <div class="input-wrapper">
                        <input type="text" 
                               class="clean-input" 
                               id="search_input" 
                               placeholder="Ketik Nama/NIS Siswa..."
                               autocomplete="off"
                               required>
                        <i class="fa-solid fa-magnifying-glass input-icon"></i>
                        
                        <i class="fa-solid fa-spinner fa-spin loading-spinner" id="loading_icon"></i>

                        <input type="hidden" name="nis" id="nis_real">
                        
                        <div id="search_results" class="search-results"></div>
                    </div>
                    <div style="margin-top: 6px; font-size: 11px; color: var(--text-grey);" id="selected_info"></div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <div class="input-wrapper">
                        <input type="text" 
                               class="clean-input" 
                               id="tanggal_lahir" 
                               name="tanggal_lahir" 
                               placeholder="DD-MM-YYYY"
                               value="<?= old('tanggal_lahir') ?>"
                               inputmode="numeric"
                               pattern="\d{2}-\d{2}-\d{4}"
                               autocomplete="off"
                               required>
                        <i class="fa-solid fa-calendar-days input-icon"></i>
                    </div>
                    <div class="hint">Contoh: 15-08-2012</div>
                </div>

                <button type="submit" class="btn-primary" id="btnSubmit">
                    <span>Lihat Rincian Tagihan</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="cooldown-badge" id="cooldownTimer">
                    <i class="fa-solid fa-clock"></i>
                    <span>Mohon tunggu <strong id="countdown">10</strong> detik</span>
                </div>

            </form>
        </div>

        <div class="info-container">
            <div class="info-header">Pusat Bantuan & Informasi</div>
            
            <div class="info-list">
                
                <div class="info-item">
                    <div class="info-icon-box">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="info-content">
                        <h4>Lupa NIS / Tanggal Lahir?</h4>
                        <p>Silakan hubungi bagian <strong>Tata Usaha (TU)</strong> sekolah atau Bendahara untuk meminta data login siswa.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon-box">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="info-content">
                        <h4>Data & Privasi Aman</h4>
                        <p>Data yang ditampilkan hanya berupa riwayat pembayaran SPP/Tagihan. Data pribadi rinci lainnya tidak dipublikasikan.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon-box">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h4>Jam Layanan TU</h4>
                        <p>Senin - Jumat: 07.30 - 14.00 WIB<br>Sabtu: 08.00 - 12.00 WIB</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer">
            &copy; <?= date('Y') ?> SMPIT Wahdatul Ummah. All Rights Reserved.
        </div>

    </div>

    <script>
        // --- JAM REALTIME ---
        function updateClock() {
            const now = new Date();
            const optionsDate = { weekday: 'short', day: 'numeric', month: 'short' };
            const dateString = now.toLocaleDateString('id-ID', optionsDate);
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            document.getElementById('liveDate').textContent = dateString;
            document.getElementById('liveTime').textContent = `${hours}:${minutes} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- PROTEKSI & VALIDASI ---
        const COOLDOWN_DURATION = 10; 
        const MAX_ATTEMPTS = 3; 
        const LONG_COOLDOWN = 60; 

        let attemptCount = 0;
        let lastAttemptTime = 0;
        let cooldownActive = false;

        function startCooldown(duration) {
            cooldownActive = true;
            const btn = document.getElementById('btnSubmit');
            const timer = document.getElementById('cooldownTimer');
            const countdown = document.getElementById('countdown');
            
            btn.disabled = true;
            timer.style.display = 'flex';
            
            let timeLeft = duration;
            countdown.textContent = timeLeft;
            
            const interval = setInterval(() => {
                timeLeft--;
                countdown.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    cooldownActive = false;
                    btn.disabled = false;
                    timer.style.display = 'none';
                    if (duration === LONG_COOLDOWN) {
                        attemptCount = 0;
                    }
                }
            }, 1000);
        }

        function checkSpam() {
            const now = Date.now();
            const timeSinceLastAttempt = (now - lastAttemptTime) / 1000;
            
            if (cooldownActive) return false;
            
            if (timeSinceLastAttempt < 30) {
                attemptCount++;
            } else {
                attemptCount = 1;
            }
            
            lastAttemptTime = now;
            
            if (attemptCount >= MAX_ATTEMPTS) {
                alert('⚠️ Terlalu banyak percobaan! Mohon tunggu sebentar.');
                startCooldown(LONG_COOLDOWN);
                return false;
            }
            
            return true;
        }

        function handleLogoError() {
            document.getElementById('logoImg').style.display = 'none';
            document.getElementById('logoFallback').style.display = 'flex';
        }

        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        
        // PERBAIKAN SCRIPT TANGGAL
        tanggalLahirInput.addEventListener('input', function(e) {
            // Jika user menekan backspace (hapus), jangan jalankan auto-format
            if (e.inputType === 'deleteContentBackward' || e.inputType === 'deleteContentForward') {
                return;
            }

            let value = e.target.value.replace(/\D/g, ''); // Hapus semua kecuali angka
            
            // Batasi maksimal 8 angka (DDMMYYYY)
            if (value.length > 8) value = value.slice(0, 8);
            
            // Logika penambahan tanda strip
            if (value.length >= 2) value = value.slice(0, 2) + '-' + value.slice(2);
            if (value.length >= 5) value = value.slice(0, 5) + '-' + value.slice(5, 9);
            
            e.target.value = value;
        });

        // ================= AJAX SEARCH SISWA =================
        const searchInput = document.getElementById('search_input');
        const searchResults = document.getElementById('search_results');
        const nisInput = document.getElementById('nis_real'); // Hidden input untuk NIS
        const selectedInfo = document.getElementById('selected_info');
        const loadingIcon = document.getElementById('loading_icon');
        let searchTimeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const keyword = this.value;

            // Reset hidden NIS jika user mengetik ulang (agar data konsisten)
            nisInput.value = '';
            
            if (keyword.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            loadingIcon.style.display = 'block';

            // Debounce: Tunggu 500ms setelah user berhenti mengetik
            searchTimeout = setTimeout(() => {
                fetch('<?= base_url('home/searchSiswa') ?>?keyword=' + encodeURIComponent(keyword))
                    .then(response => response.json())
                    .then(data => {
                        loadingIcon.style.display = 'none';
                        searchResults.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(siswa => {
                                const item = document.createElement('div');
                                item.className = 'search-item';
                                // PRIVASI: Hanya menampilkan Nama dan Kelas di dropdown
                                // NIS tidak ditampilkan di sini
                                item.innerHTML = `
                                    <strong>${siswa.nama}</strong>
                                    <small>${siswa.kelas}</small>
                                `;
                                
                                // Event saat user memilih salah satu siswa
                                item.onclick = () => {
                                    // 1. Tampilkan Nama di input visual
                                    searchInput.value = siswa.nama;
                                    
                                    // 2. Simpan NIS di input hidden (ini yang dikirim ke server)
                                    nisInput.value = siswa.nis;
                                    
                                    // 3. Tampilkan konfirmasi di bawah input (Nama & Kelas saja)
                                    selectedInfo.innerHTML = `<span style="color:var(--primary);"><i class="fa-solid fa-check-circle"></i> Terpilih:</span> ${siswa.nama} (${siswa.kelas})`;
                                    
                                    // 4. Sembunyikan dropdown
                                    searchResults.style.display = 'none';
                                };
                                searchResults.appendChild(item);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="search-item" style="cursor:default; color:#94a3b8;">Siswa tidak ditemukan</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        loadingIcon.style.display = 'none';
                    });
            }, 500); 
        });

        // Tutup dropdown jika klik di luar area input
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.input-wrapper')) {
                searchResults.style.display = 'none';
            }
        });
        // ================= END AJAX =================

        document.getElementById('formCekTagihan').addEventListener('submit', function(e) {
            // Validasi: Pastikan user sudah memilih siswa dari daftar (NIS Hidden terisi)
            if (document.getElementById('nis_real').value === '') {
                e.preventDefault();
                alert('⚠️ Mohon cari dan pilih nama siswa terlebih dahulu dari daftar!');
                return false;
            }

            const tanggal = tanggalLahirInput.value;
            const pattern = /^(\d{2})-(\d{2})-(\d{4})$/;
            const match = tanggal.match(pattern);
            
            if (!match) {
                e.preventDefault();
                alert('⚠️ Format tanggal salah! Gunakan format DD-MM-YYYY');
                return false;
            }
            
            if (!checkSpam()) {
                e.preventDefault();
                return false;
            }

            const btn = document.getElementById('btnSubmit');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Sedang Memproses...</span>';
        });
    </script>

</body>
</html>