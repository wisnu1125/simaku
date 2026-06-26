<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/favicon.png') ?>">

    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ==================== THEME VARIABLES ==================== */
        :root {
            --primary: #0891b2;       /* Cyan 600 - Bluish Teal */
            --primary-dark: #0e7490;  /* Cyan 700 */
            --primary-light: #ecfeff; /* Cyan 50 */
            --bg-body: #f8fafc;       /* Slate 50 */
            --text-main: #0f172a;     /* Slate 900 */
            --text-muted: #64748b;    /* Slate 500 */
            --border: #e2e8f0;        /* Slate 200 */
            --card-bg: #ffffff;
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --radius: 16px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --modal-overlay: rgba(0, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.5;
            padding: 16px 12px;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        a { text-decoration: none; }
        ul { list-style: none; }

        /* ==================== COMPONENTS ==================== */
        
        /* Navbar/Header Top */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 0 4px;
        }
        
        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-img {
            height: 40px;
            width: auto;
        }

        .logo-text {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }
        .logo-text small {
            font-size: 10px;
            font-weight: 500;
            color: var(--text-muted);
            display: block;
        }

        /* Header Card */
        .header-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .header-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back { background: #f1f5f9; color: var(--text-muted); }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 10px rgba(8, 145, 178, 0.25); }
        .btn-primary:active { transform: scale(0.98); }

        .student-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            margin-top: 10px;
        }

        .student-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .meta-tag {
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .meta-tag i { color: var(--primary); }
        .meta-tag b { color: var(--text-main); }

        .va-card {
            background: var(--primary-light);
            border: 1px dashed var(--primary);
            border-radius: 12px;
            padding: 12px;
            margin-top: 10px;
        }
        .va-title { font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 4px; }
        .va-code { font-size: 20px; font-weight: 800; color: var(--primary); letter-spacing: 1px; font-family: monospace; }

        /* Stats Grid */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: white;
            padding: 12px 8px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }

        .stat-title { font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .stat-num { font-size: 12px; font-weight: 800; color: var(--text-main); word-break: break-word; }
        .text-green { color: var(--success); }
        .text-red { color: var(--danger); }

        /* General Section Styles */
        .section-box { margin-bottom: 24px; }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
        }
        .section-title i { color: var(--primary); }

        /* BILL CARDS */
        .bill-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .bill-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bill-header-title {
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bg-header-pending { background: #fff1f2; color: #991b1b; }
        .bg-header-paid { background: #ecfdf5; color: #065f46; }

        .bill-list { display: flex; flex-direction: column; }

        /* NEW: Group Divider Style */
        .group-divider {
            background-color: #f8fafc;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            border-top: 1px solid var(--border);
        }
        /* Remove top border if it's the first element */
        .group-divider:first-child { border-top: none; }

        .bill-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            transition: background 0.1s;
        }
        .bill-item:last-child { border-bottom: none; }
        .bill-item:active { background: #f8fafc; }

        .bill-info { flex: 1; }
        .bill-name { font-size: 13px; font-weight: 600; color: var(--text-main); display: block; }
        .bill-month { font-size: 11px; color: var(--text-muted); }
        
        .bill-amount-wrap { text-align: right; margin-left: 10px; }
        .bill-price { font-size: 13px; font-weight: 700; display: block; }
        .bill-remaining { font-size: 10px; color: var(--danger); display: block; margin-top: 2px;}

        .badge-status {
            font-size: 9px; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-weight: 700; 
            text-transform: uppercase;
            margin-top: 4px;
            display: inline-block;
        }
        .st-lunas { background: var(--success-light); color: #065f46; }
        .st-belum { background: var(--danger-light); color: #991b1b; }
        .st-cicil { background: #ffedd5; color: #9a3412; }

        /* CARD FOOTER ACTION (Tombol Hitung) */
        .card-footer-action {
            padding: 12px 16px;
            background: var(--bg-body);
            border-top: 1px solid var(--border);
        }

        .btn-calc-full {
            width: 100%;
            background: white;
            border: 1px solid var(--primary);
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-calc-full:hover {
            background: var(--primary-light);
        }
        .btn-calc-full:active {
            transform: scale(0.98);
        }

        /* MODAL / BOTTOM SHEET STYLES */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--modal-overlay);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            display: flex;
            align-items: flex-end; /* Align bottom for mobile sheet feel */
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 24px 24px 0 0; /* Rounded top */
            padding: 0;
            max-height: 85vh; /* Max height to allow scrolling */
            display: flex;
            flex-direction: column;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.1);
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title { font-size: 16px; font-weight: 800; color: var(--text-main); }
        .btn-close-modal { 
            background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer; padding: 4px;
        }

        .modal-body {
            padding: 0 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            background: #f8fafc;
        }

        /* CHECKBOX ITEM DI DALAM MODAL */
        .check-item {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
        }
        .check-item:last-child { border-bottom: none; }

        .custom-checkbox {
            width: 24px; height: 24px;
            border: 2px solid var(--border);
            border-radius: 8px;
            margin-right: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            flex-shrink: 0;
            color: white;
        }
        
        .check-item input { display: none; } /* Hide default checkbox */
        
        .check-item input:checked + .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }
        .check-item input:checked + .custom-checkbox i {
            display: block;
        }
        .custom-checkbox i { display: none; font-size: 12px; }

        .ci-info { flex: 1; }
        .ci-name { font-size: 13px; font-weight: 600; display: block; color: var(--text-main); }
        .ci-price { font-size: 13px; font-weight: 700; color: var(--danger); display: block; text-align: right; }
        .ci-sub { font-size: 11px; color: var(--text-muted); }

        .total-display {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;
        }
        .td-label { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
        .td-amount { font-size: 20px; font-weight: 800; color: var(--primary); }

        .btn-done {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
        }

        /* History Styles */
        .history-list {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .history-item {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .history-item:last-child { border-bottom: none; }
        .h-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #f1f5f9;
            color: var(--text-muted);
            font-size: 14px;
            flex-shrink: 0;
        }
        .h-icon.transfer { background: #eff6ff; color: #2563eb; }
        .h-icon.tunai { background: #ecfdf5; color: #059669; }
        .h-content { flex: 1; }
        .h-date { font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 2px; }
        .h-title { font-size: 13px; font-weight: 600; color: var(--text-main); display: block; line-height: 1.2; }
        .h-subtitle { font-size: 11px; color: var(--text-muted); }
        .h-amount { text-align: right; font-size: 13px; font-weight: 700; color: var(--success); }
        .kwitansi-small { font-size: 9px; background: #f1f5f9; padding: 2px 5px; border-radius: 6px; color: var(--text-muted); border: 1px solid #e2e8f0; }

        /* Helpers */
        .hidden-row { display: none !important; }
        .show-more-btn {
            width: 100%; padding: 12px; background: white; border: none; border-top: 1px solid var(--border);
            color: var(--primary); font-weight: 600; font-size: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .empty-box { text-align: center; padding: 40px 20px; color: var(--text-muted); background: white; border-radius: var(--radius); border: 1px solid var(--border); }
        .empty-box i { font-size: 32px; margin-bottom: 10px; opacity: 0.3; }
        .year-separator { font-size: 14px; font-weight: 800; margin: 24px 0 12px 0; color: var(--text-muted); padding-left: 8px; border-left: 3px solid var(--primary); }
        .disclaimer-box { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 16px; border-radius: var(--radius); display: flex; gap: 12px; align-items: flex-start; margin-top: 24px; margin-bottom: 10px; font-size: 12px; line-height: 1.5; }
        .disclaimer-box.info-theme { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .d-icon { font-size: 16px; margin-top: 2px; flex-shrink: 0; }

    </style>
</head>
<body>

<div class="container fade-in">

    <div class="app-header">
        <div class="logo-area">
            <img src="<?= base_url('assets/img/logoavatar.png') ?>" alt="Logo" class="logo-img" onerror="this.src='https://ui-avatars.com/api/?name=S&background=0891b2&color=fff&size=128'">
            <div class="logo-text">
                SMPIT Wahdatul Ummah Metro<br>
                <small>Sistem Informasi Manajemen Keuangan</small>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 10px; color: var(--text-muted);"><?= date('l') ?></div>
            <div style="font-size: 12px; font-weight: 700;"><?= date('d M Y') ?></div>
        </div>
    </div>
    
    <div class="disclaimer-box info-theme">
        <div class="d-icon"><i class="fa-solid fa-circle-info"></i></div>
        <div class="d-text">
            <b>Informasi Pembayaran:</b><br>
            Status pembayaran akan diperbarui sistem dalam waktu maksimal <b>3x24 jam setelah mengirimkan bukti pembayaran ke nomor WhatsApp Bendahara Sekolah</b>. 
            Jika status Anda belum berubah atau terdapat kesalahan, silakan segera hubungi <b>Tata Usaha (TU)</b> atau <b>Bendahara Sekolah</b>.
        </div>
    </div>

    <div class="header-card">
        <h2 class="student-name"><?= esc($siswa['nama_lengkap']) ?></h2>
        
        <div class="student-meta">
            <div class="meta-tag">
                <i class="fa-solid fa-id-card"></i> <span>NIS: <b><?= esc($siswa['nis']) ?></b></span>
            </div>
            <?php if ($siswa['nama_kelas']): ?>
            <div class="meta-tag">
                <i class="fa-solid fa-school"></i> <span>Kelas: <b><?= esc($siswa['nama_kelas']) ?></b></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="va-card">
            <div class="va-title">Rekening Pembayaran (BSI)</div>
            <div class="va-code"><?= esc($siswa['virtual_account']) ?></div>
            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">a.n. SMPIT Wahdatul Ummah</div>
        </div>

        <div class="action-buttons">
            <a href="<?= base_url() ?>" class="btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <a href="<?= base_url('print-kartu/' . $siswa['id_siswa'] . '/' . $tahun_ajaran_aktif) ?>" class="btn btn-primary">
                <i class="fa-solid fa-download"></i> Download Kartu
            </a>
        </div>
    </div>

    <?php
    // --- KONFIGURASI FILTER TANGGAL ---
    $mapBulan = [
        'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4, 'MEI' => 5, 'JUNI' => 6,
        'JULI' => 7, 'AGUSTUS' => 8, 'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12
    ];
    $currentYear = intval(date('Y'));
    $currentMonth = intval(date('n'));
    $currentDateCode = ($currentYear * 100) + $currentMonth;

    // --- INISIALISASI VARIABEL TOTAL UNTUK DITAMPILKAN ---
    $totalTagihanTampil = 0;
    $totalDibayarTampil = 0;
    $totalTunggakanTampil = 0;

    // Kita akan melakukan iterasi awal untuk menghitung statistik yang BENAR-BENAR tampil
    if (!empty($tagihan_by_tahun)) {
        foreach ($tagihan_by_tahun as $thnKey => $listTagihan) {
            
            // Parsing Tahun Ajaran (misal: "2025/2026")
            $arrThn = explode('/', $thnKey);
            $startYear = isset($arrThn[0]) ? intval($arrThn[0]) : 0;
            $endYear = isset($arrThn[1]) ? intval($arrThn[1]) : $startYear + 1;

            foreach ($listTagihan as $t) {
                // 1. Jika Lunas -> SELALU HITUNG & TAMPIL
                if ($t['sisa_tagihan'] <= 0) {
                    $totalTagihanTampil += $t['nominal_akhir'];
                    $totalDibayarTampil += $t['nominal_dibayar'];
                    $totalTunggakanTampil += $t['sisa_tagihan'];
                } 
                // 2. Jika Belum Lunas -> CEK FILTER SPP MASA DEPAN
                else {
                    $isFutureSPP = false;
                    $namaTagihanUpper = strtoupper($t['nama_tagihan']);

                    if (strpos($namaTagihanUpper, 'SPP') !== false) {
                        $bulanDitemukan = 0;
                        foreach ($mapBulan as $namaBln => $angkaBln) {
                            if (strpos($namaTagihanUpper, $namaBln) !== false) {
                                $bulanDitemukan = $angkaBln;
                                break;
                            }
                        }

                        if ($bulanDitemukan > 0) {
                            $realYear = ($bulanDitemukan >= 7) ? $startYear : $endYear;
                            $billDateCode = ($realYear * 100) + $bulanDitemukan;
                            
                            // Jika tagihan SPP di masa depan, jangan hitung di statistik
                            if ($billDateCode > $currentDateCode) {
                                $isFutureSPP = true;
                            }
                        }
                    }

                    // Jika BUKAN SPP Masa Depan, maka hitung
                    if (!$isFutureSPP) {
                        $totalTagihanTampil += $t['nominal_akhir'];
                        $totalDibayarTampil += $t['nominal_dibayar'];
                        $totalTunggakanTampil += $t['sisa_tagihan'];
                    }
                }
            }
        }
    }
    ?>

    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-title">Total Tagihan</div>
            <div class="stat-num">Rp <?= number_format($totalTagihanTampil, 0, ',', '.') ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Terbayar</div>
            <div class="stat-num text-green">Rp <?= number_format($totalDibayarTampil, 0, ',', '.') ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Belum Terbayar</div>
            <div class="stat-num text-red">Rp <?= number_format($totalTunggakanTampil, 0, ',', '.') ?></div>
        </div>
    </div>

    <div class="section-box">
        <div class="section-title">
            <i class="fa-solid fa-file-invoice-dollar"></i> Rincian Tagihan
        </div>

        <?php if (empty($tagihan_by_tahun)): ?>
            <div class="empty-box">
                <i class="fa-solid fa-coins"></i>
                <p>Tidak ada data tagihan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($tagihan_by_tahun as $tahun => $tagihanList): ?>
                
                <?php
                // --- SETUP VARIABLES TAHUN AJARAN ---
                $arrThn = explode('/', $tahun);
                $startYear = isset($arrThn[0]) ? intval($arrThn[0]) : 0;
                $endYear = isset($arrThn[1]) ? intval($arrThn[1]) : $startYear + 1;

                // --- LOGIKA SORTING (Supaya Rapi) ---
                $urutanBulan = [
                    'juli' => 1, 'agustus' => 2, 'september' => 3, 'oktober' => 4, 'november' => 5, 'desember' => 6,
                    'januari' => 7, 'februari' => 8, 'maret' => 9, 'april' => 10, 'mei' => 11, 'juni' => 12
                ];

                usort($tagihanList, function($a, $b) use ($urutanBulan) {
                    $namaA = strtolower($a['nama_tagihan']);
                    $namaB = strtolower($b['nama_tagihan']);
                    $isSppA = (strpos($namaA, 'spp') !== false);
                    $isSppB = (strpos($namaB, 'spp') !== false);

                    if (!$isSppA && $isSppB) return -1;
                    if ($isSppA && !$isSppB) return 1;
                    if ($isSppA && $isSppB) {
                        $bulanA = 0; $bulanB = 0;
                        foreach ($urutanBulan as $bulan => $urutan) {
                            if (strpos($namaA, $bulan) !== false) $bulanA = $urutan;
                            if (strpos($namaB, $bulan) !== false) $bulanB = $urutan;
                        }
                        return $bulanA <=> $bulanB;
                    }
                    return strcmp($namaA, $namaB);
                });

                // --- PISAHKAN DATA & TERAPKAN FILTER HANYA PADA "BELUM LUNAS" ---
                $listBelumLunas = [];
                $listLunas = [];
                $billsForModal = []; 

                foreach ($tagihanList as $t) {
                    // KONDISI 1: SUDAH LUNAS
                    if ($t['sisa_tagihan'] <= 0) {
                        $listLunas[] = $t;
                    } 
                    // KONDISI 2: BELUM LUNAS
                    else {
                        $isFutureSPP = false;
                        $namaTagihanUpper = strtoupper($t['nama_tagihan']);

                        // Cek apakah ini SPP
                        if (strpos($namaTagihanUpper, 'SPP') !== false) {
                            $bulanDitemukan = 0;
                            foreach ($mapBulan as $namaBln => $angkaBln) {
                                if (strpos($namaTagihanUpper, $namaBln) !== false) {
                                    $bulanDitemukan = $angkaBln;
                                    break;
                                }
                            }

                            if ($bulanDitemukan > 0) {
                                $realYear = ($bulanDitemukan >= 7) ? $startYear : $endYear;
                                $billDateCode = ($realYear * 100) + $bulanDitemukan;
                                
                                // JIKA MASA DEPAN -> TRUE
                                if ($billDateCode > $currentDateCode) {
                                    $isFutureSPP = true;
                                }
                            }
                        }

                        // Jika BUKAN SPP Masa Depan, baru ditampilkan
                        if (!$isFutureSPP) {
                            $listBelumLunas[] = $t;
                            // Data untuk modal kalkulator
                            $billsForModal[] = [
                                'id' => uniqid(),
                                'name' => $t['nama_tagihan'],
                                'nominal' => $t['sisa_tagihan'],
                                'month' => $t['bulan_tagihan']
                            ];
                        }
                    }
                }
                
                $idBelum = 'list-belum-' . str_replace('/', '-', $tahun);
                $idLunas = 'list-lunas-' . str_replace('/', '-', $tahun);
                $jsonBills = json_encode($billsForModal);
                ?>
                
                <?php if (!empty($listBelumLunas) || !empty($listLunas)): ?>
                    <div class="year-separator">Tahun Ajaran <?= esc($tahun) ?></div>
                <?php endif; ?>

                <?php if (!empty($listBelumLunas)): ?>
                    <div class="bill-card">
                        <div class="bill-card-header bg-header-pending">
                            <div class="bill-header-title">
                                <i class="fa-solid fa-circle-exclamation"></i> Belum Lunas
                            </div>
                        </div>

                        <div class="bill-list" id="<?= $idBelum ?>">
                            <?php 
                            // --- LOGIC GROUPING BELUM LUNAS DI VIEW (FIXED & SORTED) ---
                            $groupedBelum = [];
                            foreach ($listBelumLunas as $item) {
                                // PERBAIKAN: Gunakan '??' untuk keamanan jika key tipe_tagihan tidak ada
                                $grupName = $item['grup_tagihan'] ?? $item['kategori'] ?? null;
                                if (!$grupName) {
                                    $tipe = $item['tipe_tagihan'] ?? ''; // Safe fallback
                                    if ($tipe === 'bulanan') $grupName = 'Iuran Bulanan';
                                    elseif ($tipe === 'tahunan') $grupName = 'Tagihan Tahunan';
                                    else $grupName = 'Lainnya';
                                }
                                $groupedBelum[$grupName][] = $item;
                            }
                            
                            // SORTING CUSTOM: 1. Daftar Ulang/Tahunan, 2. Bulanan, 3. Lainnya
                            uksort($groupedBelum, function($a, $b) {
                                $getPriority = function($str) {
                                    $s = strtolower($str);
                                    if (strpos($s, 'daftar ulang') !== false || strpos($s, 'tahunan') !== false) return 1;
                                    if (strpos($s, 'bulanan') !== false) return 2;
                                    return 3;
                                };
                                $pA = $getPriority($a);
                                $pB = $getPriority($b);
                                if ($pA === $pB) return strcmp($a, $b);
                                return $pA <=> $pB;
                            });
                            
                            $globalCount = 0; 
                            ?>

                            <?php foreach ($groupedBelum as $grupName => $items): ?>
                                <?php 
                                    // Sembunyikan header grup jika item pertama di grup ini sudah > 5
                                    $headerHiddenClass = ($globalCount >= 5) ? 'hidden-row' : ''; 
                                ?>
                                <div class="group-divider <?= $headerHiddenClass ?>"><?= esc($grupName) ?></div>

                                <?php foreach ($items as $t): $globalCount++; ?>
                                    <?php $isHidden = ($globalCount > 5) ? 'hidden-row' : ''; ?>
                                    <div class="bill-item <?= $isHidden ?>">
                                        <div class="bill-info">
                                            <span class="bill-name"><?= esc($t['nama_tagihan']) ?></span>
                                            <?php if ($t['bulan_tagihan']): ?>
                                                <span class="bill-month">Bulan <?= esc($t['bulan_tagihan']) ?></span>
                                            <?php endif; ?>
                                            <span class="badge-status <?= ($t['status_tagihan'] === 'cicil') ? 'st-cicil' : 'st-belum' ?>">
                                                <?= ($t['status_tagihan'] === 'cicil') ? 'Dicicil' : 'Belum Bayar' ?>
                                            </span>
                                        </div>
                                        <div class="bill-amount-wrap">
                                            <?php if (isset($t['nominal_potongan']) && $t['nominal_potongan'] > 0): ?>
                                                <div style="font-size: 10px; color: var(--text-muted); text-decoration: line-through; line-height: 1.2;">
                                                    Rp <?= number_format($t['nominal_tagihan'] ?? ($t['nominal_akhir'] + $t['nominal_potongan']), 0, ',', '.') ?>
                                                </div>
                                                <div style="font-size: 9px; color: var(--primary); font-weight: 700; margin-bottom: 2px;">
                                                    (Potongan/Beasiswa Rp. -<?= number_format($t['nominal_potongan'], 0, ',', '.') ?>)
                                                </div>
                                            <?php endif; ?>
                                            <span class="bill-price text-red">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></span>
                                            <?php if ($t['nominal_dibayar'] > 0): ?>
                                                <span class="bill-remaining">Sisa: Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($globalCount > 5): ?>
                            <button class="show-more-btn" onclick="toggleContainer('<?= $idBelum ?>', this, <?= $globalCount ?>)">
                                <span>Lihat Semua (<?= $globalCount ?>)</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        <?php endif; ?>

                        <div class="card-footer-action">
                            <button class="btn-calc-full" 
                                    onclick='openCalcModal(<?= htmlspecialchars($jsonBills, ENT_QUOTES, 'UTF-8') ?>, "<?= esc($tahun) ?>")'>
                                <i class="fa-solid fa-calculator"></i>
                                Hitung Beberapa Tagihan
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($listLunas)): ?>
                    <div class="bill-card">
                        <div class="bill-card-header bg-header-paid">
                            <div class="bill-header-title">
                                <i class="fa-solid fa-check-circle"></i> Sudah Lunas
                            </div>
                        </div>
                        <div class="bill-list" id="<?= $idLunas ?>">
                            <?php 
                            // --- LOGIC GROUPING LUNAS DI VIEW (FIXED & SORTED) ---
                            $groupedLunas = [];
                            foreach ($listLunas as $item) {
                                // PERBAIKAN: Gunakan '??' untuk keamanan
                                $grupName = $item['grup_tagihan'] ?? $item['kategori'] ?? null;
                                if (!$grupName) {
                                    $tipe = $item['tipe_tagihan'] ?? ''; // Safe fallback
                                    if ($tipe === 'bulanan') $grupName = 'Iuran Bulanan';
                                    elseif ($tipe === 'tahunan') $grupName = 'Tagihan Tahunan';
                                    else $grupName = 'Lainnya';
                                }
                                $groupedLunas[$grupName][] = $item;
                            }
                            
                            // SORTING CUSTOM: 1. Daftar Ulang/Tahunan, 2. Bulanan, 3. Lainnya
                            uksort($groupedLunas, function($a, $b) {
                                $getPriority = function($str) {
                                    $s = strtolower($str);
                                    if (strpos($s, 'daftar ulang') !== false || strpos($s, 'tahunan') !== false) return 1;
                                    if (strpos($s, 'bulanan') !== false) return 2;
                                    return 3;
                                };
                                $pA = $getPriority($a);
                                $pB = $getPriority($b);
                                if ($pA === $pB) return strcmp($a, $b);
                                return $pA <=> $pB;
                            });
                            
                            $globalCountLunas = 0; 
                            ?>

                            <?php foreach ($groupedLunas as $grupName => $items): ?>
                                <?php 
                                    $headerHiddenClass = ($globalCountLunas >= 5) ? 'hidden-row' : ''; 
                                ?>
                                <div class="group-divider <?= $headerHiddenClass ?>"><?= esc($grupName) ?></div>

                                <?php foreach ($items as $t): $globalCountLunas++; ?>
                                    <?php $isHidden = ($globalCountLunas > 5) ? 'hidden-row' : ''; ?>
                                    <div class="bill-item <?= $isHidden ?>">
                                        <div class="bill-info"> 
                                            <span class="bill-name"><?= esc($t['nama_tagihan']) ?></span>
                                            <?php if ($t['bulan_tagihan']): ?>
                                                <span class="bill-month">Bulan <?= esc($t['bulan_tagihan']) ?></span>
                                            <?php endif; ?>
                                            <span class="badge-status st-lunas">Lunas</span>
                                        </div>
                                        <div class="bill-amount-wrap">
                                            <?php if (isset($t['nominal_potongan']) && $t['nominal_potongan'] > 0): ?>
                                                <div style="font-size: 10px; color: var(--text-muted); text-decoration: line-through; line-height: 1.2;">
                                                    Rp <?= number_format($t['nominal_tagihan'] ?? ($t['nominal_akhir'] + $t['nominal_potongan']), 0, ',', '.') ?>
                                                </div>
                                                <div style="font-size: 9px; color: var(--primary); font-weight: 700; margin-bottom: 2px;">
                                                    (Potongan/Beasiswa Rp. -<?= number_format($t['nominal_potongan'], 0, ',', '.') ?>)
                                                </div>
                                            <?php endif; ?>
                                            <span class="bill-price text-green">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($globalCountLunas > 5): ?>
                            <button class="show-more-btn" onclick="toggleContainer('<?= $idLunas ?>', this, <?= $globalCountLunas ?>)">
                                <span>Lihat Semua (<?= $globalCountLunas ?>)</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="section-box">
        <div class="section-title">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pembayaran
        </div>

        <?php if (empty($pembayaran)): ?>
            <div class="empty-box">
                <i class="fa-solid fa-receipt"></i>
                <p>Belum ada riwayat transaksi.</p>
            </div>
        <?php else: ?>
            <div class="history-list">
                <div id="historyContainer">
                    <?php $count = 0; ?>
                    <?php foreach ($pembayaran as $p): ?>
                        <?php 
                            $count++; 
                            $isHidden = ($count > 5) ? 'hidden-row' : ''; 
                            $iconClass = ($p['metode_pembayaran'] === 'tunai') ? 'tunai' : 'transfer';
                            $icon = ($p['metode_pembayaran'] === 'tunai') ? 'fa-money-bill' : 'fa-building-columns';
                        ?>
                        <div class="history-item <?= $isHidden ?>">
                            <div class="h-icon <?= $iconClass ?>">
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>
                            <div class="h-content">
                                <span class="h-date">
                                    <?= date('d/m/Y H:i', strtotime($p['tanggal_bayar'])) ?> • <span class="kwitansi-small"><?= esc($p['nomor_kwitansi']) ?></span>
                                </span>
                                <span class="h-title"><?= esc($p['nama_tagihan']) ?></span>
                                <span class="h-subtitle"><?= esc($p['nama_tahun_ajaran']) ?></span>
                            </div>
                            <div class="h-amount">
                                +<?= number_format($p['nominal_bayar'], 0, ',', '.') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($pembayaran) > 5): ?>
                    <button class="show-more-btn" onclick="toggleContainer('historyContainer', this, <?= count($pembayaran) ?>)">
                        <span>Lihat Semua (<?= count($pembayaran) ?>)</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<div class="modal-overlay" id="calcModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Kalkulator Tagihan</div>
            <button class="btn-close-modal" onclick="closeCalcModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" id="modalList">
            </div>
        <div class="modal-footer">
            <div class="total-display">
                <span class="td-label">Total Dipilih</span>
                <span class="td-amount" id="modalTotalAmount">Rp 0</span>
            </div>
            <button class="btn-done" onclick="closeCalcModal()">Selesai</button>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk Toggle Show More/Less
    function toggleContainer(containerId, btnElement, totalCount) {
        const container = document.getElementById(containerId);
        const hiddenRows = container.querySelectorAll('.hidden-row');
        const icon = btnElement.querySelector('i');
        const text = btnElement.querySelector('span');

        if (hiddenRows.length > 0) {
            // Expand
            hiddenRows.forEach(row => {
                row.classList.remove('hidden-row');
                row.classList.add('was-hidden');
                row.style.display = 'flex';
            });
            text.textContent = "Tutup";
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            // Collapse
            const wasHiddenRows = container.querySelectorAll('.was-hidden');
            wasHiddenRows.forEach(row => {
                row.classList.add('hidden-row');
                row.classList.remove('was-hidden');
                row.style.display = 'none';
            });
            text.textContent = "Lihat Semua (" + totalCount + ")";
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // --- LOGIKA MODAL CALCULATOR ---
    
    // Format Rupiah Helper
    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function openCalcModal(bills, yearTitle) {
        const modal = document.getElementById('calcModal');
        const listContainer = document.getElementById('modalList');
        const titleEl = document.querySelector('.modal-title');
        
        // Set Title
        titleEl.textContent = "Hitung Tagihan " + yearTitle;
        
        // Clear previous items
        listContainer.innerHTML = '';

        // Generate Items
        bills.forEach(item => {
            const el = document.createElement('label');
            el.className = 'check-item';
            
            // Generate HTML for item
            // Checkbox checked by default
            el.innerHTML = `
                <input type="checkbox" class="modal-checkbox" value="${item.nominal}" checked onchange="calculateModalTotal()">
                <div class="custom-checkbox">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ci-info">
                    <span class="ci-name">${item.name}</span>
                    <span class="ci-sub">${item.month ? 'Bulan ' + item.month : 'Tagihan Tahunan'}</span>
                </div>
                <div class="ci-price">
                    ${formatRupiah(item.nominal).replace('Rp', 'Rp ')}
                </div>
            `;
            listContainer.appendChild(el);
        });

        // Show Modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent body scroll

        // Initial Calc
        calculateModalTotal();
    }

    function calculateModalTotal() {
        const checkboxes = document.querySelectorAll('.modal-checkbox:checked');
        let total = 0;
        
        checkboxes.forEach(box => {
            total += parseInt(box.value);
        });

        document.getElementById('modalTotalAmount').textContent = formatRupiah(total).replace('Rp', 'Rp ');
    }

    function closeCalcModal() {
        const modal = document.getElementById('calcModal');
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore body scroll
    }

    // Close modal when clicking outside content
    document.getElementById('calcModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCalcModal();
        }
    });
</script>

</body>
</html>