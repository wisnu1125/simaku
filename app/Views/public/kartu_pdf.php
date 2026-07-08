<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Pembayaran - <?= esc($siswa['nama_lengkap']) ?></title>

    <style>
        /* ================= RESET & HALAMAN ================= */
        @page { margin: 1.2cm; size: A4; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #334155;
            line-height: 1.4;
            background-color: #ffffff;
        }
        h1, h2, h3, p { margin: 0; padding: 0; }
        table { border-collapse: collapse; width: 100%; }

        /* ================= HEADER ================= */
        .header-container {
            width: 100%;
            margin-bottom: 18px;
            background-color: #0891b2;
            padding: 14px 18px;
            border-radius: 14px;
        }
        .header-table { width: 100%; border: none; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }

        .logo-wrapper {
            background-color: #ffffff;
            width: 54px;
            height: 54px;
            border-radius: 10px;
            text-align: center;
            vertical-align: middle;
        }
        .logo-wrapper img { height: 40px; width: auto; margin-top: 7px; }

        .header-text { padding-left: 14px; color: #ffffff; }
        .header-title { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; }
        .header-school { font-size: 9.5pt; font-weight: normal; opacity: .92; margin-top: 2px; }

        .pill-tahun {
            background-color: rgba(255,255,255,.18);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 8.5pt;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap;
        }

        /* ================= KARTU IDENTITAS (BIODATA) ================= */
        .identity-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0891b2;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 22px;
        }
        .identity-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .identity-grid td { vertical-align: top; padding: 0; width: 50%; }
        .identity-item { padding-bottom: 8px; font-size: 9.5pt; }
        .identity-item .lbl {
            display: block; color: #64748b; font-size: 8pt; text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: 2px;
        }
        .identity-item .val { color: #0f172a; font-weight: bold; }

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
        .table-bill tr:nth-child(even) { background-color: #f8fafc; }
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
        .signature-section { width: 100%; margin-top: 40px; page-break-inside: avoid; }
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
                <td width="64" style="padding-right: 0;">
                    <div class="logo-wrapper">
                        <img src="assets/img/logo.png" alt="Logo">
                    </div>
                </td>
                <td class="header-text">
                    <h1 class="header-title">Kartu Pembayaran</h1>
                    <h2 class="header-school">SMPIT Wahdatul Ummah</h2>
                </td>
                <td width="26%" style="text-align: right;">
                    <div class="pill-tahun">
                        <?php
                        $text_tahun = $tahun_ajaran['nama_tahun_ajaran'];
                        $text_bersih = str_ireplace(['T.A.', 'TA', 'T.A'], '', $text_tahun);
                        ?>
                        T.A. <?= trim($text_bersih) ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="identity-card">
        <div class="identity-name"><?= esc($siswa['nama_lengkap']) ?></div>
        <table class="identity-grid">
            <tr>
                <td>
                    <div class="identity-item">
                        <span class="lbl">NIS / NISN</span>
                        <span class="val"><?= esc($siswa['nis']) ?><?php if (!empty($siswa['nisn'])): ?> / <?= esc($siswa['nisn']) ?><?php endif; ?></span>
                    </div>
                    <?php if (!empty($siswa['nama_kelas'])): ?>
                    <div class="identity-item">
                        <span class="lbl">Kelas</span>
                        <span class="val"><?= esc($siswa['nama_kelas']) ?></span>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="identity-item">
                        <span class="lbl">Tahun Ajaran</span>
                        <span class="val"><?= esc($tahun_ajaran['nama_tahun_ajaran']) ?></span>
                    </div>
                    <div class="identity-item">
                        <span class="lbl">Dicetak Pada</span>
                        <span class="val" style="font-weight: normal; color: #64748b;"><?= date('d F Y') ?></span>
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
                    <td class="text-dark"><?= esc($t['nama_tagihan']) ?></td>
                    <td class="text-right no-wrap">Rp <?= number_format($t['nominal_akhir'], 0, ',', '.') ?></td>
                    <td class="text-right no-wrap">Rp <?= number_format($t['nominal_dibayar'], 0, ',', '.') ?></td>
                    <td class="text-right text-red no-wrap">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php
                        if ($t['status_tagihan'] === 'lunas') {
                            echo '<span class="badge bg-lunas">Lunas</span>';
                        } elseif ($t['status_tagihan'] === 'cicil') {
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
                        if ($totalSisa == 0) {
                            echo '<span style="background:rgba(255,255,255,0.2); padding:2px 6px; border-radius:4px;">LUNAS</span>';
                        } elseif ($totalDibayar > 0) {
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
                    <div class="sig-city-date">Metro, <?= date('d F Y') ?></div>
                    <div class="sig-role">Bendahara Sekolah</div>
                    <div class="sig-name">Wisnu Andrean, A.Md.Kom.</div>
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
