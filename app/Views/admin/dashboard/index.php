<?= $this->include('admin/layouts/header') ?>



<style>

/* ==================== BLUISH TEAL THEME CSS ==================== */

:root {

    --primary: #0891b2;       /* Warna Utama */

    --primary-hover: #0e7490;

    --primary-light: #cffafe;

    --primary-bg: #ecfeff;

    --secondary: #64748b;

    --bg-body: #f8fafc;

    --card-bg: #ffffff;

    --text-main: #0f172a;

    --text-muted: #64748b;

    --border: #e2e8f0;

    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);

    --radius: 16px;

}



body {

    background-color: var(--bg-body);

    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;

    color: var(--text-main);

}



/* Layout Utils */

.dashboard-container { max-width: 1600px; margin: 0 auto; }

.grid-cols-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }

.grid-cols-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }

.mb-8 { margin-bottom: 32px; }



/* Hero Section */

.hero-section {

    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);

    border-radius: var(--radius);

    padding: 36px; color: white; position: relative; overflow: hidden; margin-bottom: 32px;

    box-shadow: 0 10px 25px -5px rgba(8, 145, 178, 0.4); display: flex; justify-content: space-between; align-items: center;

}

.hero-bg-pattern { position: absolute; top: -50%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%); opacity: 0.6; pointer-events: none; }

.hero-content { position: relative; z-index: 2; max-width: 60%; }

.hero-title { font-size: 28px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.5px; line-height: 1.2; }

.hero-subtitle { opacity: 0.95; font-size: 14px; font-weight: 500; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

.tag-year { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid rgba(255,255,255,0.3); white-space: nowrap; }



.hero-actions { display: flex; gap: 12px; position: relative; z-index: 2; flex-wrap: wrap; }

.btn-shortcut {

    background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3);

    color: white; padding: 12px 16px; border-radius: 14px; text-decoration: none; font-size: 12px; font-weight: 600;

    display: flex; flex-direction: column; align-items: center; gap: 6px; transition: all 0.3s; min-width: 90px; flex: 1;

}

.btn-shortcut i { font-size: 20px; }

.btn-shortcut:hover { background: white; color: var(--primary); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }



/* Stat Cards (UKURAN FONT DISESUAIKAN DI SINI) */

.stat-card { background: var(--card-bg); border-radius: var(--radius); padding: 20px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: all 0.3s ease; display: flex; align-items: center; gap: 16px; position: relative; overflow: hidden; }

.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px -5px rgba(8, 145, 178, 0.15); border-color: var(--primary-light); }

.stat-icon-wrapper { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }

.bg-primary-soft { background: var(--primary-bg); color: var(--primary); }

.bg-green-soft { background: #f0fdf4; color: #16a34a; }

.bg-red-soft { background: #fef2f2; color: #dc2626; }

.bg-orange-soft { background: #fff7ed; color: #ea580c; }



.stat-info { overflow: hidden; flex: 1; }

.stat-info h3 { font-size: 10px; color: var(--text-muted); font-weight: 700; margin-bottom: 4px; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0.5px; }

/* Font value dikecilkan ke 15px agar nominal miliaran muat */

.stat-info .value { font-size: 15px; font-weight: 800; color: var(--text-main); line-height: 1.2; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; } 

.stat-info .desc { font-size: 10px; color: var(--text-muted); margin-top: 2px; display: flex; align-items: center; gap: 4px; font-weight: 600; flex-wrap: wrap; }



/* Cards & Tables */

.card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; overflow: hidden; height: 100%; }

.card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 10px; }

.card-title { font-size: 14px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }

.card-title i { color: var(--primary); font-size: 14px; }

.card-body { padding: 20px; flex: 1; }



/* Quick Payment Input */

.payment-shortcut-card { background: linear-gradient(135deg, #ffffff 0%, #ecfeff 100%); border: 1px solid #a5f3fc; }

.quick-input-group { position: relative; }

.quick-input { width: 100%; padding: 10px 14px 10px 36px; border: 1px solid #cbd5e1; border-radius: 12px; outline: none; transition: 0.2s; font-size: 13px; box-sizing: border-box; }

.quick-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-bg); }

.quick-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--secondary); font-size: 14px; }



.dashboard-search-results {

    position: absolute; top: 110%; left: 0; right: 0;

    background: white; border: 1px solid var(--border); border-radius: 12px;

    box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50;

    max-height: 250px; overflow-y: auto; display: none;

}

.search-item { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: 0.2s; }

.search-item:hover { background: var(--primary-bg); }

.search-item strong { display: block; color: var(--text-main); font-size: 13px; }

.search-item small { color: var(--secondary); font-size: 10px; }



/* ==================== FIXED MODAL SCROLL CSS ==================== */

.modal-overlay {

    position: fixed; 

    top: 0; left: 0; right: 0; bottom: 0;

    background: rgba(15, 23, 42, 0.6); 

    backdrop-filter: blur(4px);

    z-index: 1000; 

    display: none; 

    justify-content: center; 

    align-items: center;

    padding: 20px;

    animation: fadeIn 0.2s ease-out;

}



.modal-content {

    background: white; 

    width: 100%; 

    max-width: 650px; 

    height: auto;

    max-height: 85vh; 

    border-radius: 20px; 

    box-shadow: 0 20px 50px rgba(0,0,0,0.2);

    display: flex; 

    flex-direction: column; 

    animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);

    overflow: hidden; 

}



#formModalPembayaran {

    display: flex;

    flex-direction: column;

    height: 100%;

    overflow: hidden;

}



.modal-header { 

    padding: 16px 20px; 

    border-bottom: 1px solid var(--border); 

    display: flex; 

    justify-content: space-between; 

    align-items: center; 

    background: var(--primary-bg); 

    flex-shrink: 0; 

}

.modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: var(--primary); }

.modal-close { background: none; border: none; font-size: 18px; color: var(--secondary); cursor: pointer; }



.modal-body { 

    padding: 20px; 

    overflow-y: auto; 

    flex-grow: 1; 

    background: #fff;

}



.modal-body::-webkit-scrollbar { width: 8px; }

.modal-body::-webkit-scrollbar-track { background: #f1f5f9; }

.modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.modal-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }



.modal-footer { 

    padding: 16px 20px; 

    border-top: 1px solid var(--border); 

    background: #f8fafc; 

    display: flex; 

    justify-content: space-between; 

    align-items: center; 

    flex-shrink: 0; 

    z-index: 10;

}



/* Modal Elements */

.student-badge { background: white; padding: 10px; border-radius: 12px; border: 1px solid var(--primary-light); margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }

.student-avatar { width: 36px; height: 36px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }



/* Bill Checklist Style */

.bill-item {

    display: grid; grid-template-columns: 36px 1fr 130px; gap: 10px;

    align-items: center; padding: 10px; border: 1px solid var(--border);

    border-radius: 10px; margin-bottom: 8px; transition: 0.2s;

}

.bill-item:hover { border-color: #cbd5e1; }

.bill-item.active { background: #f0fdfa; border-color: var(--primary); }

.checkbox-custom { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }

.input-nominal { width: 100%; padding: 6px; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 600; box-sizing: border-box; }

.badge-bill { font-size: 10px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #64748b; }



/* Animations */

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

@keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }



/* Table, Badge, Utils */

.table-container { overflow-x: auto; width: 100%; }

table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 500px; }

th { text-align: left; padding: 12px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--secondary); background: #f8fafc; border-bottom: 1px solid var(--border); white-space: nowrap; }

td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }

.badge { padding: 4px 10px; border-radius: 30px; font-size: 10px; font-weight: 700; white-space: nowrap; }

.badge-danger { background: #fee2e2; color: #b91c1c; }



/* ==================== RESPONSIVE & TEXT ADJUSTMENTS ==================== */

@media (max-width: 1200px) {

    .grid-cols-4, .grid-cols-2 { grid-template-columns: 1fr; }

    .hero-section { flex-direction: column; align-items: start; gap: 20px; padding: 24px; }

    .hero-content { max-width: 100%; }

    .hero-actions { width: 100%; overflow-x: auto; padding-bottom: 4px; justify-content: flex-start; }

    .btn-shortcut { flex: 0 0 auto; }

}



@media (max-width: 768px) {

    /* Hero Text Adjustments */

    .hero-title { font-size: 22px; }

    .hero-subtitle { font-size: 13px; }

    .tag-year { margin-left: 0; margin-top: 4px; display: inline-block; }

    

    /* Stat Card Text Adjustments (MOBILE) */

    .stat-card { padding: 16px; gap: 12px; }

    .stat-icon-wrapper { width: 42px; height: 42px; font-size: 18px; }

    .stat-info .value { font-size: 16px; } /* Lebih kecil lagi di HP */

    .stat-info h3 { font-size: 10px; }

    .stat-info .desc { font-size: 10px; }

    

    /* Card Body Padding */

    .card-header { padding: 14px; }

    .card-body { padding: 14px; }

    .card-title { font-size: 14px; }

    

    /* Modal Responsive */

    .bill-item { grid-template-columns: 30px 1fr; grid-template-rows: auto auto; gap: 8px; }

    .bill-item > div:nth-child(3) { grid-column: 1 / -1; width: 100%; }

    .modal-footer { flex-direction: column; gap: 12px; align-items: stretch; text-align: center; }

    .modal-footer button { width: 100%; }

}



@media (max-width: 480px) {

    /* Mobile Extra Small */

    .hero-section { padding: 16px; }

    .hero-title { font-size: 18px; }

    .stat-info .value { font-size: 15px; }

    .stat-card { flex-direction: row; align-items: center; }

    

    /* Table Font Shrink */

    th, td { padding: 8px; font-size: 11px; }

    

    /* Chart Height */

    #chartPembayaran { height: 220px !important; }

}

</style>



<div class="dashboard-container">

    

    <div class="hero-section">

        <div class="hero-bg-pattern"></div>

        <div class="hero-content">

            <h1 class="hero-title">Halo, <?= explode(' ', session()->get('nama_lengkap'))[0] ?>! 👋</h1>

            <div class="hero-subtitle">

                Dashboard Keuangan Sekolah

                <?php if ($tahun_ajaran_aktif): ?>

                    <span class="tag-year">TA: <?= esc($tahun_ajaran_aktif['nama_tahun_ajaran']) ?></span>

                <?php endif; ?>

            </div>

        </div>



        <div class="hero-actions">

            <a href="<?= base_url('admin/pembayaran') ?>" class="btn-shortcut">

                <i class="fa-solid fa-cash-register"></i> Bayar SPP

            </a>

            <a href="<?= base_url('admin/tagihan') ?>" class="btn-shortcut">

                <i class="fa-solid fa-file-invoice"></i> Tagihan

            </a>

            <a href="<?= base_url('admin/laporan') ?>" class="btn-shortcut">

                <i class="fa-solid fa-print"></i> Laporan

            </a>

            <a href="<?= base_url('admin/siswa') ?>" class="btn-shortcut">

                <i class="fa-solid fa-user-plus"></i> Siswa

            </a>

        </div>

    </div>



    <div class="grid-cols-4 mb-8">

        <div class="stat-card">

            <div class="stat-icon-wrapper bg-primary-soft"><i class="fa-solid fa-users"></i></div>

            <div class="stat-info">

                <h3>Total Siswa</h3>

                <div class="value"><?= number_format($total_siswa) ?></div>

                <div class="desc"><?= number_format($total_kelas) ?> Kelas Aktif</div>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon-wrapper bg-green-soft"><i class="fa-solid fa-money-bill-trend-up"></i></div>

            <div class="stat-info">

                <h3>Uang Masuk</h3>

                <div class="value">Rp <?= number_format($total_dibayar, 0, ',', '.') ?></div>

                <div class="desc" style="color: #16a34a;"><i class="fa-solid fa-arrow-up"></i> <?= $total_tagihan > 0 ? number_format(($total_dibayar / $total_tagihan) * 100, 1) : 0 ?>% Terbayar</div>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon-wrapper bg-red-soft"><i class="fa-solid fa-triangle-exclamation"></i></div>

            <div class="stat-info">

                <h3>Tunggakan</h3>

                <div class="value">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>

                <div class="desc" style="color: #dc2626;">Belum lunas</div>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon-wrapper bg-orange-soft"><i class="fa-solid fa-calendar-check"></i></div>

            <div class="stat-info">

                <h3>Hari Ini</h3>

                <div class="value">Rp <?= number_format($pembayaran_hari_ini, 0, ',', '.') ?></div>

                <div class="desc">Masuk hari ini</div>

            </div>

        </div>

    </div>



    <div class="grid-cols-2 mb-8">

        <div class="card">

            <div class="card-header">

                <div class="card-title"><i class="fa-solid fa-chart-area"></i> Arus Kas 7 Hari</div>

            </div>

            <div class="card-body">

                <div style="height: 320px; position: relative; width: 100%;">

                    <canvas id="chartPembayaran"></canvas>

                </div>

            </div>

        </div>



        <div style="display: flex; flex-direction: column; gap: 24px;">

            

            <div class="card payment-shortcut-card">

                <div class="card-body">

                    <h3 style="font-size: 15px; font-weight: 700; color: #0891b2; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">

                        <i class="fa-solid fa-bolt"></i> Pembayaran Cepat

                    </h3>

                    <p style="font-size: 12px; color: #64748b; margin-bottom: 12px;">Masukkan NIS/Nama Siswa untuk proses bayar tanpa pindah halaman.</p>

                    

                    <div class="quick-input-group">

                        <i class="fa-solid fa-magnifying-glass quick-search-icon"></i>

                        <input type="text" id="dashboard_search" class="quick-input" placeholder="Ketik nama atau NIS..." autocomplete="off">

                        <div id="dashboard_search_results" class="dashboard-search-results"></div>

                    </div>

                </div>

            </div>



            <div class="card">

                <div class="card-header" style="padding: 16px 20px;">

                    <div class="card-title" style="font-size: 14px;">⚠️ Top Tunggakan</div>

                    <a href="<?= base_url('admin/laporan/tunggakan') ?>" style="font-size: 11px; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat Semua</a>

                </div>

                <div class="card-body" style="padding: 0;">

                    <?php if (empty($top_tunggakan)): ?>

                        <div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;">Aman, tidak ada tunggakan!</div>

                    <?php else: ?>

                        <div class="table-container">

                            <table>

                                <tbody>

                                    <?php foreach (array_slice($top_tunggakan, 0, 3) as $t): ?>

                                    <tr>

                                        <td style="padding: 12px 20px;">

                                            <div style="font-weight: 600; font-size: 13px;"><?= esc($t['nama_lengkap']) ?></div>

                                            <div style="font-size: 11px; color: #94a3b8;"><?= esc($t['nama_kelas']) ?></div>

                                        </td>

                                        <td style="text-align: right; padding: 12px 20px;">

                                            <span class="badge badge-danger">Rp <?= number_format($t['total_tunggakan'], 0, ',', '.') ?></span>

                                        </td>

                                    </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>



    <div class="card">

        <div class="card-header">

            <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Transaksi Terbaru</div>

            <a href="<?= base_url('admin/pembayaran') ?>" style="font-size: 12px; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>

        </div>

        <div class="card-body" style="padding: 0;">

             <?php if (empty($pembayaran_terbaru)): ?>

                <div style="padding: 30px; text-align: center; color: #94a3b8;">

                    <i class="fa-solid fa-receipt" style="font-size: 28px; opacity: 0.5; margin-bottom: 8px;"></i>

                    <p style="font-size: 13px;">Belum ada data pembayaran</p>

                </div>

            <?php else: ?>

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Tanggal</th>

                                <th>Siswa</th>

                                <th>Pembayaran</th>

                                <th style="text-align: right;">Nominal</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($pembayaran_terbaru as $p): ?>

                            <tr>

                                <td>

                                    <div style="font-weight: 600; color: var(--text-main);"><?= date('d M Y', strtotime($p['tanggal_bayar'])) ?></div>

                                    <div style="font-size: 10px; color: #94a3b8;"><?= date('H:i', strtotime($p['tanggal_bayar'])) ?> • <?= esc($p['nomor_kwitansi']) ?></div>

                                </td>

                                <td>

                                    <div style="font-weight: 600;"><?= esc($p['nama_lengkap']) ?></div>

                                    <div style="font-size: 10px; color: #94a3b8;">NIS: <?= esc($p['nis']) ?></div>

                                </td>

                                <td><span style="color: var(--secondary);"><?= esc($p['nama_tagihan']) ?></span></td>

                                <td style="text-align: right;">

                                    <span style="font-weight: 700; color: var(--primary); font-size: 14px;">+ Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></span>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>



<div class="modal-overlay" id="paymentModal">

    <div class="modal-content">

        <form action="<?= base_url('admin/pembayaran/store-bulk') ?>" method="POST" id="formModalPembayaran">

            

            <div class="modal-header">

                <h3><i class="fa-solid fa-wallet"></i> Bayar Tagihan</h3>

                <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>

            </div>

            

            <div class="modal-body">

                <div class="student-badge">

                    <div class="student-avatar"><i class="fa-solid fa-user-graduate"></i></div>

                    <div>

                        <h4 style="margin:0; color:var(--text-main); font-size:14px;" id="modal_nama_siswa">Nama Siswa</h4>

                        <div style="font-size:11px; color:var(--secondary);" id="modal_nis_kelas">NIS - Kelas</div>

                        <input type="hidden" name="id_siswa" id="modal_id_siswa">

                    </div>

                </div>



                <div style="margin-bottom: 10px; font-weight: 700; font-size: 12px; color: var(--secondary);">DAFTAR TAGIHAN BELUM LUNAS</div>

                <div id="modal_bill_container">

                    </div>



                <div style="margin-top: 20px;">

                    <label style="font-size:12px; font-weight:700; color:var(--secondary);">METODE</label>

                    <div style="display:flex; gap:12px; margin-top:5px;">

                        <label style="font-size:12px; display:flex; gap:5px; align-items:center; cursor:pointer;">

                            <input type="radio" name="metode_pembayaran" value="tunai" checked> Tunai

                        </label>

                        <label style="font-size:12px; display:flex; gap:5px; align-items:center; cursor:pointer;">

                            <input type="radio" name="metode_pembayaran" value="transfer"> Transfer

                        </label>

                    </div>

                </div>

            </div>



            <div class="modal-footer">

                <div>

                    <div style="font-size:11px; color:var(--secondary);">Total Bayar</div>

                    <div style="font-size:18px; font-weight:800; color:var(--primary);" id="modal_total_display">Rp 0</div>

                </div>

                <button type="submit" class="btn-primary" id="modal_btn_submit" disabled>

                    <i class="fa-solid fa-paper-plane"></i> Proses

                </button>

            </div>

        </form>

    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>

// --- CHART CONFIG ---

const ctx = document.getElementById('chartPembayaran').getContext('2d');

const chartData = <?= json_encode($chart_pembayaran) ?>;

let gradient = ctx.createLinearGradient(0, 0, 0, 400);

gradient.addColorStop(0, 'rgba(6, 182, 212, 0.4)');

gradient.addColorStop(1, 'rgba(8, 145, 178, 0.05)');



new Chart(ctx, {

    type: 'line',

    data: {

        labels: chartData.map(d => d.date),

        datasets: [{

            label: 'Pemasukan', data: chartData.map(d => d.nominal),

            borderColor: '#06b6d4', backgroundColor: gradient,

            borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#0891b2',

            pointBorderWidth: 2, pointRadius: 5, fill: true, tension: 0.4

        }]

    },

    options: {

        responsive: true, maintainAspectRatio: false,

        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', callbacks: { label: function(c) { return 'Rp ' + c.parsed.y.toLocaleString('id-ID'); } } } },

        scales: { x: { grid: { display: false } }, y: { border: { display: false }, grid: { color: '#f1f5f9' }, ticks: { callback: function(v) { if (v >= 1000000) return (v / 1000000).toFixed(1) + 'jt'; return v >= 1000 ? (v / 1000).toFixed(0) + 'rb' : v; } } } }

    }

});



// --- LOGIKA PEMBAYARAN CEPAT (AJAX + MODAL) ---

const searchInput = document.getElementById('dashboard_search');

const searchResults = document.getElementById('dashboard_search_results');

const modal = document.getElementById('paymentModal');

const modalBillContainer = document.getElementById('modal_bill_container');

const modalBtnSubmit = document.getElementById('modal_btn_submit');

const modalTotalDisplay = document.getElementById('modal_total_display');

let searchTimeout;

let selectedTagihan = [];



// 1. Search Logic

searchInput.addEventListener('input', function() {

    clearTimeout(searchTimeout);

    const keyword = this.value;

    if(keyword.length < 2) { searchResults.style.display = 'none'; return; }



    searchTimeout = setTimeout(() => {

        fetch('<?= base_url('admin/siswa/search') ?>?keyword=' + encodeURIComponent(keyword))

            .then(res => res.json())

            .then(data => {

                searchResults.innerHTML = '';

                if(data.length === 0) {

                    searchResults.innerHTML = '<div style="padding:10px; color:#94a3b8; font-size:13px; text-align:center;">Tidak ditemukan</div>';

                } else {

                    data.forEach(siswa => {

                        const div = document.createElement('div');

                        div.className = 'search-item';

                        div.innerHTML = `<strong>${siswa.nama_lengkap}</strong><small>${siswa.nis} • ${siswa.nama_kelas || '-'}</small>`;

                        div.onclick = () => openPaymentModal(siswa);

                        searchResults.appendChild(div);

                    });

                }

                searchResults.style.display = 'block';

            });

    }, 300);

});



// Close search when clicking outside

document.addEventListener('click', (e) => {

    if (!e.target.closest('.quick-input-group')) searchResults.style.display = 'none';

});



// 2. Open Modal & Load Bills

function openPaymentModal(siswa) {

    searchResults.style.display = 'none';

    searchInput.value = ''; // clear input

    

    // Set Student Info

    document.getElementById('modal_id_siswa').value = siswa.id_siswa;

    document.getElementById('modal_nama_siswa').textContent = siswa.nama_lengkap;

    document.getElementById('modal_nis_kelas').textContent = `${siswa.nis} • ${siswa.nama_kelas || '-'}`;

    

    // Reset State

    selectedTagihan = [];

    updateSummary();

    modalBillContainer.innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;">Memuat tagihan...</div>';

    

    // Show Modal

    modal.style.display = 'flex';

    

    // Fetch Tagihan

    fetch('<?= base_url('admin/pembayaran/get-tagihan-by-siswa') ?>?id_siswa=' + siswa.id_siswa)

        .then(res => res.json())

        .then(data => {

            modalBillContainer.innerHTML = '';



            // ================= LOGIKA SORTING (Non-SPP -> SPP Urut Bulan) =================

            data.sort((a, b) => {

                const nameA = a.nama_tagihan.toLowerCase();

                const nameB = b.nama_tagihan.toLowerCase();

                

                const isSppA = nameA.includes('spp');

                const isSppB = nameB.includes('spp');

                

                // 1. Non-SPP di ATAS

                if (!isSppA && isSppB) return -1;

                if (isSppA && !isSppB) return 1;

                

                // 2. Jika SPP, urutkan bulan akademik

                if (isSppA && isSppB) {

                    const academicOrder = {

                        'juli': 1, 'jul': 1, 'agustus': 2, 'agu': 2, 'september': 3, 'sep': 3,

                        'oktober': 4, 'okt': 4, 'november': 5, 'nov': 5, 'desember': 6, 'des': 6,

                        'januari': 7, 'jan': 7, 'februari': 8, 'feb': 8, 'maret': 9, 'mar': 9,

                        'april': 10, 'apr': 10, 'mei': 11, 'may': 11, 'mei': 11, 'juni': 12, 'jun': 12

                    };

                    const getOrderValue = (text) => {

                        for (const [key, value] of Object.entries(academicOrder)) {

                            if (text.includes(key)) return value;

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



            if(data.length === 0) {

                modalBillContainer.innerHTML = '<div style="text-align:center; padding:20px; color:#10b981; background:#f0fdf4; border-radius:10px;">Semua tagihan lunas!</div>';

            } else {

                data.forEach((tagihan, index) => {

                    const div = document.createElement('div');

                    div.className = 'bill-item';

                    div.id = `modal_item_${index}`;

                    div.innerHTML = `

                        <div style="text-align:center;">

                            <input type="checkbox" class="checkbox-custom" id="modal_check_${index}" onchange="toggleModalTagihan(${index}, '${tagihan.id_tagihan}', ${tagihan.sisa_tagihan})">

                        </div>

                        <div>

                            <div style="font-weight:600; font-size:13px;">${tagihan.nama_tagihan}</div>

                            <div style="font-size:11px; color:#64748b;">

                                <span class="badge-bill">${tagihan.nama_tahun_ajaran}</span>

                                Sisa: <span style="color:#ef4444;">Rp ${parseInt(tagihan.sisa_tagihan).toLocaleString('id-ID')}</span>

                            </div>

                        </div>

                        <div>

                            <input type="number" class="input-nominal" id="modal_nominal_${index}" 

                            name="nominal[${tagihan.id_tagihan}]" value="${tagihan.sisa_tagihan}" 

                            max="${tagihan.sisa_tagihan}" disabled oninput="updateModalNominal(${index})">

                            <input type="hidden" name="tanggal[${tagihan.id_tagihan}]" value="<?= date('Y-m-d') ?>">

                        </div>

                    `;

                    modalBillContainer.appendChild(div);

                });

            }

        });

}



function closePaymentModal() {

    modal.style.display = 'none';

}



// 3. Logic Checkbox & Hitung

function toggleModalTagihan(index, idTagihan, maxNominal) {

    const checkbox = document.getElementById(`modal_check_${index}`);

    const item = document.getElementById(`modal_item_${index}`);

    const input = document.getElementById(`modal_nominal_${index}`);

    

    if(checkbox.checked) {

        item.classList.add('active');

        input.disabled = false;

        selectedTagihan.push({ index: index, nominal: parseInt(input.value) });

    } else {

        item.classList.remove('active');

        input.disabled = true;

        input.value = maxNominal; 

        selectedTagihan = selectedTagihan.filter(t => t.index !== index);

    }

    updateSummary();

}



function updateModalNominal(index) {

    const input = document.getElementById(`modal_nominal_${index}`);

    const val = parseInt(input.value) || 0;

    const item = selectedTagihan.find(t => t.index === index);

    if(item) {

        item.nominal = val;

        updateSummary();

    }

}



function updateSummary() {

    const total = selectedTagihan.reduce((sum, item) => sum + item.nominal, 0);

    modalTotalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');

    modalBtnSubmit.disabled = selectedTagihan.length === 0;

}



// Prevent form submission if empty

document.getElementById('formModalPembayaran').addEventListener('submit', function(e) {

    if(selectedTagihan.length === 0) { e.preventDefault(); alert('Pilih tagihan dulu!'); }

});

</script>



<?= $this->include('admin/layouts/footer') ?>