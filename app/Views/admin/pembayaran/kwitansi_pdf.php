<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - <?= esc($pembayaran['nomor_kwitansi']) ?></title>
    
    <style>
        /* ==================== BLUISH TEAL THEME & PRINT CONFIG ==================== */
        :root {
            --primary: #0891b2;       /* Warna Utama */
            --primary-dark: #0e7490;
            --primary-light: #ecfeff; /* Background Box */
            --secondary: #64748b;
            --text-main: #0f172a;
            --border: #cbd5e1;
            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --danger-border: #fecaca;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important; /* Force Background Print */
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            background: #f1f5f9; /* Light gray background for screen */
            color: var(--text-main);
            font-size: 12px; /* Base font size reduced slightly for compact fit */
        }
        
        .kwitansi-container {
            max-width: 210mm; /* A4 Width */
            margin: 0 auto;
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #0e7490;
        }
        
        .header-left h1 {
            font-size: 24px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
        }
        
        .header-left h2 {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
        }

        .header-right {
            text-align: right;
        }

        .header-kwitansi-no {
            font-size: 16px;
            font-weight: 700;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .kwitansi-body {
            padding: 30px;
        }

        /* GRID LAYOUT FOR COMPACTNESS */
        .row-grid {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .col-half {
            flex: 1;
        }

        /* SECTIONS */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 6px 0;
            font-size: 12px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 130px;
            color: var(--secondary);
            font-weight: 500;
        }
        
        .info-table td:nth-child(2) {
            width: 15px;
            text-align: center;
            color: var(--secondary);
        }
        
        .info-table td:last-child {
            color: var(--text-main);
            font-weight: 600;
        }

        /* NOMINAL BOX - CENTER PIECE */
        .nominal-box {
            background: var(--primary-light);
            border: 2px dashed var(--primary);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 10px 0 25px 0;
            position: relative;
        }
        
        .nominal-label {
            font-size: 12px;
            color: var(--secondary);
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .nominal-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
            font-family: 'Courier New', Courier, monospace;
        }
        
        .nominal-text {
            font-size: 12px;
            color: #0e7490;
            font-style: italic;
            font-weight: 500;
        }

        /* STATUS BADGES */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-valid, .status-tunai {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #86efac;
        }
        
        .status-transfer {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        .status-batal {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* SIGNATURES */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-label {
            font-size: 12px;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .signature-date {
            font-size: 11px;
            color: var(--secondary);
            margin-bottom: 60px; /* Space for signature */
        }
        
        .signature-line {
            border-top: 1px solid var(--text-main);
            margin-bottom: 5px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
        }
        
        .signature-title {
            font-size: 11px;
            color: var(--secondary);
        }

        /* FOOTER */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px dotted #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-note {
            font-size: 10px;
            color: #94a3b8;
            font-style: italic;
        }

        .footer-warning {
            font-size: 9px;
            color: #ef4444;
            background: #fff1f2;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #fecdd3;
            max-width: 60%;
            text-align: right;
        }

        /* ALERT DANGER (BATAL) */
        .alert-batal {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* PRINT BUTTON */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(8, 145, 178, 0.3);
            z-index: 9999;
            transition: 0.2s;
        }
        
        .print-button:hover {
            background: var(--primary-dark);
        }
        
        /* PRINT MEDIA QUERY */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .kwitansi-container {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 100%;
            }

            .print-button {
                display: none;
            }

            @page {
                size: A4;
                margin: 0; /* Let container handle margins if needed */
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak / Save PDF
    </button>
    
    <div class="kwitansi-container">
        <div class="header">
            <div class="header-left">
                <h1>KWITANSI PEMBAYARAN</h1>
                <h2>SMPIT Wahdhatul Ummah</h2>
            </div>
            <div class="header-right">
                <div class="header-kwitansi-no">
                    No: <?= esc($pembayaran['nomor_kwitansi']) ?>
                </div>
            </div>
        </div>
        
        <div class="kwitansi-body">
            
            <?php if ($pembayaran['status_pembayaran'] === 'dibatalkan'): ?>
            <div class="alert-batal">
                <div style="font-size: 20px;">⚠️</div>
                <div>
                    <strong>PEMBAYARAN DIBATALKAN</strong><br>
                    Alasan: <?= esc($pembayaran['alasan_batal']) ?> (Oleh: <?= esc($pembayaran['nama_pembatal']) ?>)
                </div>
            </div>
            <?php endif; ?>

            <div class="row-grid">
                <div class="col-half">
                    <div class="section-title">Data Siswa</div>
                    <table class="info-table">
                        <tr>
                            <td>NIS</td>
                            <td>:</td>
                            <td><strong><?= esc($pembayaran['nis']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Nama Lengkap</td>
                            <td>:</td>
                            <td><?= esc($pembayaran['nama_siswa']) ?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td><?= $pembayaran['nama_kelas'] ? esc($pembayaran['nama_kelas']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td>Virtual Account</td>
                            <td>:</td>
                            <td><?= esc($pembayaran['virtual_account']) ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-half">
                    <div class="section-title">Detail Transaksi</div>
                    <table class="info-table">
                        <tr>
                            <td>Tanggal Bayar</td>
                            <td>:</td>
                            <td><?= date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></td>
                        </tr>
                        <tr>
                            <td>Metode</td>
                            <td>:</td>
                            <td>
                                <?php if ($pembayaran['metode_pembayaran'] === 'tunai'): ?>
                                    <span class="status-badge status-tunai">TUNAI</span>
                                <?php else: ?>
                                    <span class="status-badge status-transfer">TRANSFER</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>
                                <?php if ($pembayaran['status_pembayaran'] === 'valid'): ?>
                                    <span class="status-badge status-valid">VALID</span>
                                <?php else: ?>
                                    <span class="status-badge status-batal">DIBATALKAN</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Petugas</td>
                            <td>:</td>
                            <td><?= esc($pembayaran['nama_petugas']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="nominal-box">
                <div class="nominal-label">Total Nominal Pembayaran</div>
                <div class="nominal-value">Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.') ?></div>
                <div class="nominal-text">
                    <?php
                    // Fungsi terbilang
                    function terbilang($x) {
                        $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                        if ($x < 12) return $angka[$x];
                        elseif ($x < 20) return terbilang($x - 10) . " Belas";
                        elseif ($x < 100) return terbilang($x / 10) . " Puluh " . terbilang($x % 10);
                        elseif ($x < 200) return "Seratus " . terbilang($x - 100);
                        elseif ($x < 1000) return terbilang($x / 100) . " Ratus " . terbilang($x % 100);
                        elseif ($x < 2000) return "Seribu " . terbilang($x - 1000);
                        elseif ($x < 1000000) return terbilang($x / 1000) . " Ribu " . terbilang($x % 1000);
                        elseif ($x < 1000000000) return terbilang($x / 1000000) . " Juta " . terbilang($x % 1000000);
                        elseif ($x < 1000000000000) return terbilang($x / 1000000000) . " Miliar " . terbilang($x % 1000000000);
                        else return "Terbilang tidak tersedia";
                    }
                    echo "# " . terbilang($pembayaran['nominal_bayar']) . " Rupiah #";
                    ?>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Alokasi Dana Pembayaran</div>
                <table class="info-table">
                    <tr>
                        <td style="width: 200px;">Pembayaran Untuk</td>
                        <td style="width: 15px;">:</td>
                        <td>
                            <strong><?= esc($pembayaran['nama_tagihan']) ?></strong>
                            <?php if ($pembayaran['bulan_tagihan']): ?>
                                <?php
                                $bulanNama = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                ?>
                                (Bulan <?= $bulanNama[(int)$pembayaran['bulan_tagihan']] ?>)
                            <?php endif; ?>
                            - TA <?= esc($pembayaran['nama_tahun_ajaran']) ?>
                        </td>
                    </tr>
                    <?php if ($pembayaran['keterangan']): ?>
                    <tr>
                        <td>Keterangan Tambahan</td>
                        <td>:</td>
                        <td><?= nl2br(esc($pembayaran['keterangan'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <div style="margin-top: 10px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="text-align: center; font-size: 11px; color: var(--secondary);">Total Tagihan</td>
                            <td style="text-align: center; font-size: 11px; color: var(--secondary);">Sudah Dibayar</td>
                            <td style="text-align: center; font-size: 11px; color: var(--secondary);">Sisa Kekurangan</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-size: 14px; font-weight: bold;">Rp <?= number_format($pembayaran['nominal_akhir'], 0, ',', '.') ?></td>
                            <td style="text-align: center; font-size: 14px; font-weight: bold; color: var(--primary);">Rp <?= number_format($pembayaran['nominal_dibayar'], 0, ',', '.') ?></td>
                            <td style="text-align: center; font-size: 14px; font-weight: bold; color: <?= $pembayaran['sisa_tagihan'] > 0 ? '#ef4444' : '#16a34a' ?>;">
                                Rp <?= number_format($pembayaran['sisa_tagihan'], 0, ',', '.') ?>
                                <?php if ($pembayaran['sisa_tagihan'] <= 0): ?> <span style="font-size: 10px;">(LUNAS)</span> <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-label">Penyetor / Wali Murid,</div>
                    <div class="signature-date">Metro, <?= date('d F Y') ?></div>
                    <div style="height: 50px;"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">( _______________________ )</div>
                </div>
                
                <div class="signature-box">
                    <div class="signature-label">Penerima / Bendahara,</div>
                    <div class="signature-date">Metro, <?= date('d F Y') ?></div>
                    <div style="height: 50px;"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name"><?= esc($pembayaran['nama_petugas']) ?></div>
                    <div class="signature-title">Bendahara Sekolah</div>
                </div>
            </div>
            
            <div class="footer">
                <div class="footer-note">
                    Dicetak secara otomatis oleh sistem.<br>
                    ID Pembayaran: #<?= $pembayaran['id_pembayaran'] ?> | Tgl Cetak: <?= date('d/m/Y H:i') ?>
                </div>
                <div class="footer-warning">
                    Simpan kwitansi ini sebagai bukti pembayaran yang sah.<br>
                    Validasi pembayaran dapat dicek melalui nomor kwitansi.
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Opsional: Auto print saat load
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>