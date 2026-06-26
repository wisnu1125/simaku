<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Pembayaran - <?= esc($siswa['nama_lengkap']) ?></title>
    
    <style>
        /* ================= RESET & HALAMAN ================= */
        @page {
            margin: 1cm;
            size: A4;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #334155; /* Slate 700 */
            line-height: 1.4;
            background-color: #ffffff;
        }

        /* Reset margin default heading agar rapi */
        h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* ================= HEADER STYLE (LAYOUT TABEL DOMPDF) ================= */
        .header-container {
            width: 100%;
            margin-bottom: 30px;
            /* Background Biru Langit */
            background-color: #0891b2; 
            padding: 8px 3px;
            border-radius: 15px;
            overflow: hidden; /* Memastikan sudut tumpul terlihat rapi */
        }

        /* Tabel di dalam header untuk membagi 3 kolom */
        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            vertical-align: middle; /* Pastikan semua elemen rata tengah vertikal */
            border: none;
            padding: 0;
        }

        /* 1. KOTAK LOGO (KIRI) */
        .logo-wrapper {
            background-color: #ffffff;
            width: 60px;
            height: 60px;
            border-radius: 10px;
            text-align: center;
            /* Padding kecil agar gambar tidak mentok */
            padding: 5px;
        }

        .logo-wrapper img {
            height: 50px;
            width: auto;
            margin-top: 5px; /* Sedikit margin atas manual untuk centering */
        }

        /* 2. TEKS JUDUL (TENGAH) */
        .header-text {
            padding-left: 15px;
            color: #ffffff;
        }

        .header-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header-school {
            font-size: 10pt;
            font-weight: normal;
            opacity: 0.9;
        }

        /* 3. PILL TAHUN (KANAN) */
        .pill-tahun {
            background-color: #ffffff;
            color: #0891b2; /* Warna teks biru sama dengan background header */
            padding: 4px 4px;
            border-radius: 5px; /* Membuat bentuk oval/pill */
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap; /* Mencegah teks turun baris */
        }

        /* ================= BIODATA & VA SECTION ================= */
        .info-container {
            width: 100%;
            margin-bottom: 30px;
        }

        .layout-table {
            width: 100%;
            vertical-align: top;
        }

        .layout-table td {
            vertical-align: top;
            padding: 0;
        }

        .biodata-table td {
            padding-bottom: 6px;
            font-size: 10pt;
        }
        
        .label-cell {
            width: 120px;
            color: #64748b;
            font-weight: normal;
        }
        
        .sep-cell {
            width: 15px;
            text-align: center;
            color: #64748b;
        }
        
        .value-cell {
            color: #0f172a;
            font-weight: bold;
        }

        /* Card VA */
        .va-box {
            background-color: #ecfeff;
            border: 1px solid #0891b2;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-left: 20px;
        }

        .va-label {
            font-size: 9pt;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .va-number {
            font-size: 16pt;
            font-weight: 800;
            color: #0891b2;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        
        .va-note {
            font-size: 8pt;
            color: #64748b;
            margin-top: 4px;
        }

        /* ================= TABEL TAGIHAN ================= */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-left: 4px solid #0891b2;
            padding-left: 10px;
        }

        .table-bill {
            width: 100%;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            font-size: 9pt;
            table-layout: fixed;
        }

        .table-bill th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            padding: 10px 4px;
            text-transform: uppercase;
            font-size: 8pt;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .table-bill td {
            padding: 8px 4px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        .table-bill tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .row-total td {
            background-color: #0891b2;
            color: #ffffff;
            font-weight: bold;
            border-top: 2px solid #0e7490;
            font-size: 10pt;
        }

        /* Utilities */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .no-wrap { white-space: nowrap; }
        .text-red { color: #dc2626; font-weight: bold; }
        .text-dark { color: #0f172a; font-weight: bold; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            min-width: 50px;
            text-align: center;
        }

        .bg-lunas { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .bg-cicil { background-color: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .bg-belum { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Footer & Signature */
        .signature-section {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .sig-city-date { margin-bottom: 20px; }
        .sig-role { font-weight: bold; margin-bottom: 60px; }
        .sig-name { font-weight: bold; text-decoration: underline; }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            font-style: italic;
            border-top: 1px dashed #cbd5e1;
            padding-top: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td width="80" style="padding-right: 0;">
                    <div class="logo-wrapper">
                        <img src="assets/img/logo.png" alt="Logo">
                    </div>
                </td>

                <td class="header-text" style="padding-right: 0;">
                    <h1 class="header-title">KARTU PEMBAYARAN </h1>
                    <h2 class="header-school">SMPIT Wahdatul Ummah</h2>
                </td>

                <td width="10%" style="vertical-align: middle; text-align: right;">
                    <div class="pill-tahun">
                        <?php 
                        // Perbaikan Logika: Hapus "T.A." atau "TA" dari data jika ada, 
                        // lalu tambahkan kembali secara manual agar tidak double.
                        $text_tahun = $tahun_ajaran['nama_tahun_ajaran'];
                        $text_bersih = str_ireplace(['T.A.', 'TA', 'T.A'], '', $text_tahun); 
                        ?>
                        T.A. <?= trim($text_bersih) ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-container">
        <table class="layout-table">
            <tr>
                <td width="60%">
                    <table class="biodata-table">
                        <tr>
                            <td class="label-cell">Nama Lengkap</td>
                            <td class="sep-cell">:</td>
                            <td class="value-cell"><?= esc($siswa['nama_lengkap']) ?></td>
                        </tr>
                        <tr>
                            <td class="label-cell">NIS / NISN</td>
                            <td class="sep-cell">:</td>
                            <td class="value-cell">
                                <?= esc($siswa['nis']) ?> 
                                <?php if (!empty($siswa['nisn'])): ?> / <?= esc($siswa['nisn']) ?><?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($siswa['nama_kelas'])): ?>
                        <tr>
                            <td class="label-cell">Kelas</td>
                            <td class="sep-cell">:</td>
                            <td class="value-cell"><?= esc($siswa['nama_kelas']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="label-cell">Dicetak Pada</td>
                            <td class="sep-cell">:</td>
                            <td class="value-cell" style="font-weight: normal; color: #64748b;">
                                <?= date('d F Y') ?>
                            </td>
                        </tr>
                    </table>
                </td>

                <td width="40%">
                    <div class="va-box">
                        <div class="va-label">Rekening Pembayaran (BSI)</div>
                        <div class="va-label"><?= esc($siswa['virtual_account']) ?></div>
                        <div class="va-note">a.n. SMPIT Wahdatul Ummah</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Rincian Tagihan</div>

    <?php if (empty($tagihan)): ?>
        <div class="empty-state">
            Tidak ada data tagihan yang tersedia untuk tahun ajaran ini.
        </div>
    <?php else: ?>
        <table class="table-bill">
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="25%">Nama Tagihan</th> 
                    <th class="text-right" width="20%">Nominal</th>
                    <th class="text-right" width="20%">Dibayar</th> 
                    <th class="text-right" width="20%">Sisa</th>    
                    <th class="text-center" width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // LOGIKA SORTING (TIDAK DIUBAH)
                $urutanBulan = [
                    'juli' => 1, 'agustus' => 2, 'september' => 3, 'oktober' => 4, 'november' => 5, 'desember' => 6,
                    'januari' => 7, 'februari' => 8, 'maret' => 9, 'april' => 10, 'mei' => 11, 'juni' => 12
                ];

                usort($tagihan, function($a, $b) use ($urutanBulan) {
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

                $no = 1;
                $totalNominal = 0;
                $totalDibayar = 0;
                $totalSisa = 0;
                
                foreach($tagihan as $t):
                    $totalNominal += $t['nominal_akhir'];
                    $totalDibayar += $t['nominal_dibayar'];
                    $totalSisa += $t['sisa_tagihan'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-dark">
                        <?= esc($t['nama_tagihan']) ?>
                    </td>    
                    <td class="text-right no-wrap">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></td>
                    <td class="text-right no-wrap">Rp <?= number_format($t['nominal_dibayar'], 0, ',', '.') ?></td>
                    <td class="text-right text-red no-wrap">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php 
                        if($t['status_tagihan'] === 'lunas') {
                            echo '<span class="badge bg-lunas">Lunas</span>';
                        } elseif($t['status_tagihan'] === 'cicil') {
                            echo '<span class="badge bg-cicil">Cicil</span>';
                        } else {
                            echo '<span class="badge bg-belum">Belum</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="row-total">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-right no-wrap">Rp <?= number_format($totalNominal, 0, ',', '.') ?></td>
                    <td class="text-right no-wrap">Rp <?= number_format($totalDibayar, 0, ',', '.') ?></td>
                    <td class="text-right no-wrap">Rp <?= number_format($totalSisa, 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php 
                        if($totalSisa == 0) {
                            echo '<span style="background:rgba(255,255,255,0.2); padding:2px 6px; border-radius:4px;">LUNAS</span>';
                        } elseif($totalDibayar > 0) {
                            echo '<span style="background:rgba(255,255,255,0.2); padding:2px 6px; border-radius:4px;">CICIL</span>';
                        } else {
                            echo '<span style="background:rgba(255,255,255,0.2); padding:2px 6px; border-radius:4px;">BELUM</span>';
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="signature-section">
        <table width="100%">
            <tr>
                <td width="60%"></td> 
                <td width="40%" align="center">
                    <div class="sig-city-date">
                        Metro, <?= date('d F Y') ?>
                    </div>
                    <div class="sig-role">
                        Bendahara Sekolah
                    </div>
                    <div class="sig-name">
                        Wisnu Andrean, A.Md.Kom.
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem pada <?= date('d/m/Y H:i') ?> WIB.<br>
        Dokumen ini dinyatakan sah apabila telah dibubuhi tanda tangan dan stempel resmi pihak sekolah.
    </div>

</body>
</html>