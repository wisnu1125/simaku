<?= $this->include('admin/layouts/header') ?>

<style>
.welcome-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
.welcome-row h1 { font-size: 19px; font-weight: 800; color: var(--ink); }
.welcome-row p { font-size: 12.5px; color: var(--muted); margin-top: 2px; }

.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 18px 0; }
@media (min-width: 640px) { .stat-grid { grid-template-columns: repeat(4, 1fr); gap: 14px; } }
.stat-card { padding: 14px 16px; }
.stat-card .value { font-size: 15px; font-weight: 900; color: var(--ink); letter-spacing: -.3px; }
@media (min-width: 640px) { .stat-card .value { font-size: 18px; } }
.stat-card .label { font-size: 10.5px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-top: 3px; }

.dash-grid { display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px; }
@media (min-width: 1024px) { .dash-grid { grid-template-columns: 1.6fr 1fr; align-items: start; } }
.section-title { font-size: 13.5px; font-weight: 800; color: var(--ink); padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between; }
.section-title a { font-size: 11.5px; font-weight: 600; color: var(--brand); text-decoration: none; }

.status-bars { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }
.status-bar-row { display: flex; align-items: center; gap: 10px; }
.status-bar-row .dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.status-bar-row .label { font-size: 12.5px; font-weight: 600; color: var(--body); width: 78px; flex-shrink: 0; }
.status-bar-track { flex: 1; height: 8px; border-radius: 999px; background: var(--border-soft); overflow: hidden; }
.status-bar-fill { height: 100%; border-radius: 999px; }
.status-bar-row .count { font-size: 12.5px; font-weight: 800; color: var(--ink); width: 34px; text-align: right; flex-shrink: 0; }

/* Kartu Input Pembayaran -- pusat halaman ini */
.pay-card { border: 1.5px solid var(--brand-light); }
.pay-card-header { display: flex; align-items: center; gap: 10px; padding: 18px 20px; border-bottom: 1px solid var(--brand-light); background: var(--brand-bg); border-radius: var(--r-lg) var(--r-lg) 0 0; }
.pay-card-header i { font-size: 17px; color: var(--brand); }
.pay-card-header h2 { font-size: 15.5px; font-weight: 800; color: var(--brand-darker); }
.pay-card-body { padding: 20px; }

.section-step { display: flex; align-items: center; gap: 10px; margin: 22px 0 12px; }
.section-step:first-child { margin-top: 0; }
.section-step .num { width: 22px; height: 22px; border-radius: 50%; background: var(--brand); color: #fff; font-size: 11.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.section-step .txt { font-size: 13.5px; font-weight: 800; color: var(--ink); }

.checklist { border: 1px solid var(--border-soft); border-radius: var(--r-md); overflow: hidden; }
.checklist-header { display: none; padding: 10px 16px; background: var(--border-soft); font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--muted); grid-template-columns: 28px 1.6fr 1fr 1fr; gap: 14px; }
@media (min-width: 640px) { .checklist-header { display: grid; } }
.checklist-item { display: grid; grid-template-columns: 28px 1fr; row-gap: 10px; column-gap: 14px; align-items: center; padding: 14px 16px; border-bottom: 1px solid var(--border-soft); }
.checklist-item:last-child { border-bottom: none; }
.checklist-item.checked { background: var(--brand-bg); }
.checklist-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--brand); cursor: pointer; }
.checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: 1 / -1; }
@media (min-width: 640px) { .checklist-item { grid-template-columns: 28px 1.6fr 1fr 1fr; row-gap: 0; } .checklist-item .nominal-cell, .checklist-item .date-cell { grid-column: auto; } }
.tagihan-info strong { font-size: 13.5px; color: var(--ink); display: block; margin-bottom: 4px; }
.tagihan-info .sisa { font-size: 12px; color: var(--danger); font-weight: 700; margin-top: 4px; }
.nominal-input, .date-input { width: 100%; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--r-sm); font-size: 13.5px; font-family: 'Roboto Mono', monospace; }
.nominal-input:disabled, .date-input:disabled { background: var(--border-soft); color: var(--faint); }
.terbilang-text { font-size: 10.5px; color: var(--muted); font-style: italic; margin-top: 3px; font-family: 'Roboto', sans-serif; }
.summary-box { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); border-radius: var(--r-lg); padding: 18px; margin-top: 16px; color: #fff; }
.summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.2); font-size: 13px; }
.summary-row:last-child { border-bottom: none; padding-top: 10px; margin-top: 2px; border-top: 2px solid rgba(255,255,255,.3); }
.summary-row .v.total { font-size: 19px; font-weight: 900; }
.selected-siswa-box { display: flex; align-items: center; justify-content: space-between; gap: 12px; background: var(--brand-bg); border: 1.5px solid var(--brand-light); border-radius: var(--r-md); padding: 14px 16px; margin-top: 10px; }

.recent-row { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border-soft); }
.recent-row:last-child { border-bottom: none; }
.recent-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--success-bg); color: var(--success); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.recent-row .body { flex: 1; min-width: 0; }
.recent-row .title { font-size: 13px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-row .sub { font-size: 11px; color: var(--muted); }
.recent-row .amount { font-size: 12.5px; font-weight: 800; color: var(--success); flex-shrink: 0; font-family: 'Roboto Mono', monospace; }

.kelas-section { margin-top: 16px; }
.kelas-grid { display: grid; grid-template-columns: 1fr; gap: 14px; padding: 16px 20px 20px; }
@media (min-width: 640px) { .kelas-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1300px) { .kelas-grid { grid-template-columns: repeat(3, 1fr); } }
.kelas-card { border: 1px solid var(--border-soft); border-radius: var(--r-lg); overflow: hidden; }
.kelas-card-head { padding: 14px 16px; border-bottom: 1px solid var(--border-soft); background: var(--border-soft); }
.kelas-card-head .top-row { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; margin-bottom: 8px; }
.kelas-card-head .nama { font-size: 13.5px; font-weight: 800; color: var(--ink); }
.kelas-card-head .count { font-size: 11px; color: var(--muted); font-weight: 700; flex-shrink: 0; }
.kelas-card-body { max-height: 280px; overflow-y: auto; }
.siswa-tunggakan-row { padding: 10px 16px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; gap: 10px; }
.siswa-tunggakan-row:last-child { border-bottom: none; }
.siswa-tunggakan-row .info { min-width: 0; }
.siswa-tunggakan-row .nama { font-size: 12.5px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.siswa-tunggakan-row .nis { font-size: 10.5px; color: var(--muted); font-family: 'Roboto Mono', monospace; }
.siswa-tunggakan-row .tags { display: flex; gap: 4px; flex-shrink: 0; }
.tag-mini { font-size: 10px; font-weight: 700; padding: 3px 7px; border-radius: 999px; white-space: nowrap; }
.tag-du { background: var(--danger-bg); color: var(--danger); }
.tag-spp-ini { background: var(--warning-bg); color: #92400e; }
.tag-spp-lalu { background: var(--danger-bg); color: var(--danger); }
.tag-ta-lalu { background: #f3e8ff; color: #7e22ce; }
.kelas-card-empty { padding: 20px 16px; text-align: center; color: var(--success); font-size: 12px; }
.progress-track { height: 6px; border-radius: 999px; background: var(--surface); overflow: hidden; }
.progress-fill { height: 100%; border-radius: 999px; transition: width .3s ease; }
</style>

<div class="welcome-row">
    <div>
        <h1>Halo, <?= esc(explode(' ', session()->get('nama_lengkap'))[0] ?? '') ?> 👋</h1>
        <p>Cari siswa untuk langsung input pembayaran.</p>
    </div>
    <?php if ($tahun_ajaran_aktif): ?>
    <span class="badge badge-brand"><i class="fa-solid fa-calendar-check"></i> TA Aktif: <?= esc($tahun_ajaran_aktif['nama_tahun_ajaran']) ?></span>
    <?php endif; ?>
</div>

<!-- ===================== INPUT PEMBAYARAN (pusat halaman, selalu tampil) ===================== -->
<div class="card pay-card">
    <div class="pay-card-header"><i class="fa-solid fa-wallet"></i><h2>Input Pembayaran</h2></div>
    <form id="formPembayaran" action="<?= base_url('admin/pembayaran/store-bulk') ?>" method="POST">
        <div class="pay-card-body">
            <div class="search-box">
                <input type="text" class="input" id="siswa_search" placeholder="Ketik NIS atau nama siswa…" autocomplete="off">
                <div class="search-results" id="siswa_results"></div>
            </div>
            <input type="hidden" name="id_siswa" id="id_siswa" required>
            <div id="selected_siswa"></div>

            <div id="tagihan_section" style="display:none;">
                <div class="section-step"><span class="num">1</span><span class="txt">Tagihan Belum Lunas</span></div>
                <div class="checklist">
                    <div class="checklist-header"><div></div><div>Tagihan</div><div>Nominal Bayar</div><div>Tanggal Bayar</div></div>
                    <div id="tagihan_list"></div>
                </div>
            </div>

            <div id="pembayaran_section" style="display:none;">
                <div class="section-step"><span class="num">2</span><span class="txt">Metode &amp; Catatan</span></div>
                <div class="field">
                    <label class="required">Metode Pembayaran</label>
                    <div class="segmented">
                        <label><input type="radio" name="metode_pembayaran" value="tunai" checked required> 💵 Tunai</label>
                        <label><input type="radio" name="metode_pembayaran" value="transfer" required> 🏦 Transfer</label>
                    </div>
                </div>
                <div class="field" style="margin-bottom:0;">
                    <label>Catatan (opsional)</label>
                    <textarea class="input" name="keterangan" rows="2" placeholder="Contoh: dibayar oleh wali murid langsung"></textarea>
                </div>
                <div class="summary-box" id="summary_box" style="display:none;">
                    <div class="summary-row"><span>Tagihan dipilih</span><span id="summary_count">0</span></div>
                    <div class="summary-row"><span>Total dibayar</span><span class="v total" id="summary_total">Rp 0</span></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="btn_submit" disabled style="margin-top:16px;"><i class="fa-solid fa-check-double"></i> Konfirmasi Pembayaran</button>
            </div>
        </div>
    </form>
</div>

<div class="stat-grid">
    <div class="card stat-card">
        <div class="value"><?= number_format($total_siswa) ?></div>
        <div class="label">Siswa Aktif</div>
    </div>
    <div class="card stat-card">
        <div class="value"><?= number_format($total_kelas) ?></div>
        <div class="label">Total Kelas</div>
    </div>
    <div class="card stat-card">
        <div class="value" style="color:var(--danger);">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
        <div class="label">Tunggakan</div>
    </div>
    <div class="card stat-card">
        <div class="value" style="color:var(--success);">Rp <?= number_format($pembayaran_hari_ini, 0, ',', '.') ?></div>
        <div class="label">Masuk Hari Ini</div>
    </div>
</div>

<div class="dash-grid">
    <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="card">
            <div class="section-title">Pembayaran 7 Hari Terakhir</div>
            <div style="padding: 18px 20px;">
                <canvas id="chartPembayaran" height="90"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="section-title">
                Pembayaran Terbaru
                <a href="<?= base_url('admin/pembayaran') ?>">Lihat semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <?php if (empty($pembayaran_terbaru)): ?>
                <div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Belum ada pembayaran.</p></div>
            <?php else: ?>
                <?php foreach ($pembayaran_terbaru as $p): ?>
                <div class="recent-row">
                    <div class="recent-avatar"><?= esc(strtoupper(substr($p['nama_lengkap'] ?? '?', 0, 1))) ?></div>
                    <div class="body">
                        <div class="title"><?= esc($p['nama_lengkap']) ?></div>
                        <div class="sub"><?= esc($p['nama_tagihan']) ?> · <?= date('d M, H:i', strtotime($p['tanggal_bayar'])) ?></div>
                    </div>
                    <div class="amount">+<?= number_format($p['nominal_bayar'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="card">
            <div class="section-title">
                Payment Monitoring
                <a href="<?= base_url('admin/rekonsiliasi') ?>">Kelola <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div style="padding:16px 20px 4px; display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div>
                    <div style="font-size:19px; font-weight:900; color:var(--warning);"><?= number_format($payment_monitoring['pending']) ?></div>
                    <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:.3px;">Pending</div>
                </div>
                <div>
                    <div style="font-size:19px; font-weight:900; color:var(--success);"><?= number_format($payment_monitoring['paid_hari_ini']) ?></div>
                    <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:.3px;">Lunas Hari Ini</div>
                </div>
                <div>
                    <div style="font-size:19px; font-weight:900; color:<?= $payment_monitoring['webhook_gagal'] > 0 ? 'var(--danger)' : 'var(--ink)' ?>;"><?= number_format($payment_monitoring['webhook_gagal']) ?></div>
                    <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:.3px;">Webhook Gagal (24j)</div>
                </div>
                <div>
                    <div style="font-size:19px; font-weight:900; color:<?= $payment_monitoring['perlu_sync'] > 0 ? '#c2410c' : 'var(--ink)' ?>;"><?= number_format($payment_monitoring['perlu_sync']) ?></div>
                    <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:.3px;">Perlu Sync</div>
                </div>
            </div>
            <div style="padding:12px 20px 16px; font-size:11px; color:var(--muted); border-top:1px solid var(--border-soft); margin-top:12px;">
                <i class="fa-solid fa-clock"></i> Sinkronisasi terakhir: <?= $payment_monitoring['sinkronisasi_terakhir'] ? date('d M Y, H:i', strtotime($payment_monitoring['sinkronisasi_terakhir'])) : 'Belum pernah' ?>
            </div>
        </div>

        <div class="card">
            <div class="section-title">Status Tagihan</div>
            <div class="status-bars">
                <?php
                $__totalStatus = max(1, ($status_tagihan['lunas'] ?? 0) + ($status_tagihan['cicil'] ?? 0) + ($status_tagihan['belum_bayar'] ?? 0));
                $__bars = [
                    ['label' => 'Lunas', 'count' => $status_tagihan['lunas'] ?? 0, 'color' => 'var(--success)'],
                    ['label' => 'Dicicil', 'count' => $status_tagihan['cicil'] ?? 0, 'color' => 'var(--warning)'],
                    ['label' => 'Belum Bayar', 'count' => $status_tagihan['belum_bayar'] ?? 0, 'color' => 'var(--danger)'],
                ];
                ?>
                <?php foreach ($__bars as $b): ?>
                <div class="status-bar-row">
                    <span class="dot" style="background:<?= $b['color'] ?>;"></span>
                    <span class="label"><?= $b['label'] ?></span>
                    <div class="status-bar-track"><div class="status-bar-fill" style="width:<?= round($b['count'] / $__totalStatus * 100) ?>%; background:<?= $b['color'] ?>;"></div></div>
                    <span class="count"><?= number_format($b['count']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="section-title">
                Tunggakan Terbesar
                <a href="<?= base_url('admin/laporan/tunggakan') ?>">Lihat semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <?php if (empty($top_tunggakan)): ?>
                <div class="empty-state"><i class="fa-solid fa-face-smile"></i><p>Tidak ada tunggakan 🎉</p></div>
            <?php else: ?>
                <?php foreach ($top_tunggakan as $t): ?>
                <div class="recent-row">
                    <div class="recent-avatar" style="background:var(--danger-bg); color:var(--danger);"><?= esc(strtoupper(substr($t['nama_lengkap'] ?? '?', 0, 1))) ?></div>
                    <div class="body">
                        <div class="title"><?= esc($t['nama_lengkap']) ?></div>
                        <div class="sub"><?= esc($t['nama_kelas'] ?? '-') ?></div>
                    </div>
                    <div class="amount" style="color:var(--danger);"><?= number_format($t['total_tunggakan'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===================== Status Pembayaran per Kelas ===================== -->
<div class="card kelas-section">
    <div class="section-title">
        Status Pembayaran per Kelas
        <?php if ($bulan_berjalan): ?><span class="badge badge-brand"><i class="fa-solid fa-calendar-day"></i> Bulan Berjalan: <?= esc($bulan_berjalan) ?></span><?php endif; ?>
    </div>
    <?php if (empty($status_per_kelas)): ?>
        <div class="empty-state"><i class="fa-solid fa-chalkboard"></i><p>Belum ada data kelas untuk tahun ajaran ini.</p></div>
    <?php else: ?>
        <div class="kelas-grid">
            <?php foreach ($status_per_kelas as $k): ?>
            <div class="kelas-card">
                <div class="kelas-card-head">
                    <div class="top-row">
                        <span class="nama"><?= esc($k['nama_kelas']) ?></span>
                        <span class="count"><?= $k['lunas_semua'] ?>/<?= $k['total_siswa'] ?> lunas</span>
                    </div>
                    <div class="progress-track"><div class="progress-fill" style="width:<?= $k['persen_lunas'] ?>%; background:<?= $k['persen_lunas'] >= 80 ? 'var(--success)' : ($k['persen_lunas'] >= 50 ? 'var(--warning)' : 'var(--danger)') ?>;"></div></div>
                </div>
                <div class="kelas-card-body">
                    <?php if (empty($k['siswa_tunggakan'])): ?>
                        <div class="kelas-card-empty"><i class="fa-solid fa-circle-check"></i> Semua siswa sudah lunas</div>
                    <?php else: ?>
                        <?php foreach ($k['siswa_tunggakan'] as $s): ?>
                        <div class="siswa-tunggakan-row">
                            <div class="info">
                                <div class="nama"><?= esc($s['nama_lengkap']) ?></div>
                                <div class="nis">NIS <?= esc($s['nis']) ?> · <span style="color:var(--danger); font-weight:700;">Rp <?= number_format($s['total_rp'], 0, ',', '.') ?></span></div>
                            </div>
                            <div class="tags">
                                <?php if ($s['jml_du'] > 0): ?><span class="tag-mini tag-du">DU ×<?= (int) $s['jml_du'] ?></span><?php endif; ?>
                                <?php if ($s['belum_bulan_ini'] > 0): ?><span class="tag-mini tag-spp-ini">SPP <?= esc($bulan_berjalan) ?></span><?php endif; ?>
                                <?php if ($s['jml_sebelumnya'] > 0): ?><span class="tag-mini tag-spp-lalu">SPP Lalu ×<?= (int) $s['jml_sebelumnya'] ?></span><?php endif; ?>
                                <?php if (!empty($s['tunggakan_lalu']) && $s['tunggakan_lalu'] > 0): ?><span class="tag-mini tag-ta-lalu">TA Lalu: Rp <?= number_format($s['tunggakan_lalu'], 0, ',', '.') ?></span><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($siswa_tanpa_kelas_tunggakan)): ?>
<div class="card" style="margin-top:16px; border-left:4px solid #7e22ce;">
    <div class="section-title">
        <i class="fa-solid fa-user-clock" style="color:#7e22ce;"></i> Siswa Tanpa Kelas dengan Tunggakan
        <a href="<?= base_url('admin/siswa?kelas=__tanpa_kelas__') ?>">Lihat di Data Siswa <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <p style="padding:0 20px 14px; margin-top:-6px; font-size:12px; color:var(--muted);">
        Biasanya siswa yang sudah lulus atau nonaktif dan sudah dikeluarkan dari kelasnya, tapi masih ada tagihan yang belum lunas. Tidak muncul di "Status Pembayaran per Kelas" di atas karena memang sudah tidak berkelas.
    </p>
    <?php foreach ($siswa_tanpa_kelas_tunggakan as $s): ?>
    <div class="recent-row">
        <div class="recent-avatar" style="background:#f3e8ff; color:#7e22ce;"><?= esc(strtoupper(substr($s['nama_lengkap'] ?? '?', 0, 1))) ?></div>
        <div class="body">
            <div class="title"><?= esc($s['nama_lengkap']) ?></div>
            <div class="sub">NIS <?= esc($s['nis']) ?> · <?= $s['status_siswa'] === 'lulus' ? 'Lulus' : 'Nonaktif' ?> · <?= (int) $s['jml_tagihan'] ?> tagihan belum lunas</div>
        </div>
        <div class="amount" style="color:var(--danger);">Rp <?= number_format($s['total_tunggakan'], 0, ',', '.') ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
let selectedTagihan = [];

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }

let searchTimeout;
document.getElementById('siswa_search').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const keyword = this.value;
    const inputEl = this;
    if (keyword.length < 2) { closeSearchDropdown(document.getElementById('siswa_results')); return; }
    searchTimeout = setTimeout(() => {
        fetch(BASE_URL + '/admin/siswa/search?keyword=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(data => {
                const html = data.length === 0
                    ? '<div class="search-result-item" style="color:var(--faint);">Tidak ada hasil.</div>'
                    : data.map(s => `<div class="search-result-item" onclick='selectSiswaObj(${JSON.stringify(s)})'><strong>${esc(s.nama_lengkap)}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${esc(s.nis)} · ${esc(s.nama_kelas || 'Belum dikelas')}</small></div>`).join('');
                openSearchDropdown(inputEl, document.getElementById('siswa_results'), html);
            });
    }, 300);
});
document.addEventListener('click', function (e) { if (!e.target.closest('.search-box') && !e.target.closest('#siswa_results')) closeSearchDropdown(document.getElementById('siswa_results')); });

function resetForm() {
    document.getElementById('formPembayaran').reset();
    document.getElementById('id_siswa').value = '';
    document.getElementById('selected_siswa').innerHTML = '';
    document.getElementById('tagihan_section').style.display = 'none';
    document.getElementById('pembayaran_section').style.display = 'none';
    document.getElementById('btn_submit').disabled = true;
    selectedTagihan = [];
}

function selectSiswaObj(siswa) {
    selectedTagihan = [];
    document.getElementById('id_siswa').value = siswa.id_siswa;
    document.getElementById('siswa_search').value = '';
    closeSearchDropdown(document.getElementById('siswa_results'));
    document.getElementById('selected_siswa').innerHTML = `
        <div class="selected-siswa-box">
            <div><strong>${esc(siswa.nama_lengkap)}</strong><br><small><i class="fa-solid fa-id-card"></i> NIS ${esc(siswa.nis)}</small></div>
            <button type="button" class="icon-action danger" onclick="resetForm()" title="Ganti siswa"><i class="fa-solid fa-xmark"></i></button>
        </div>`;
    loadTagihanUntukBayar(siswa.id_siswa);
}

function loadTagihanUntukBayar(idSiswa) {
    fetch(BASE_URL + '/admin/pembayaran/get-tagihan-by-siswa?id_siswa=' + idSiswa)
        .then(r => r.json())
        .then(data => {
            const tagihanList = document.getElementById('tagihan_list');
            data.sort((a, b) => {
                const nameA = a.nama_tagihan.toLowerCase(), nameB = b.nama_tagihan.toLowerCase();
                const isSppA = nameA.includes('spp'), isSppB = nameB.includes('spp');
                if (!isSppA && isSppB) return -1;
                if (isSppA && !isSppB) return 1;
                if (isSppA && isSppB) {
                    const order = { juli: 1, jul: 1, agustus: 2, agu: 2, september: 3, sep: 3, oktober: 4, okt: 4, november: 5, nov: 5, desember: 6, des: 6, januari: 7, jan: 7, februari: 8, feb: 8, maret: 9, mar: 9, april: 10, apr: 10, mei: 11, may: 11, juni: 12, jun: 12 };
                    const val = t => { for (const [k, v] of Object.entries(order)) if (t.includes(k)) return v; return 99; };
                    return val(nameA) - val(nameB);
                }
                return 0;
            });

            if (data.length === 0) {
                tagihanList.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-check"></i><p>Tidak ada tagihan yang belum lunas.</p></div>';
                document.getElementById('tagihan_section').style.display = 'block';
                setTimeout(() => document.getElementById('tagihan_section').scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
                return;
            }

            tagihanList.innerHTML = data.map((tagihan, index) => {
                const today = new Date().toISOString().split('T')[0];
                return `
                    <div class="checklist-item" id="item_${index}">
                        <input type="checkbox" id="check_${index}" onchange='toggleTagihan(${index}, ${JSON.stringify(tagihan)})'>
                        <div class="tagihan-info">
                            <strong>${esc(tagihan.nama_tagihan)}</strong>
                            ${tagihan.bulan_tagihan ? '<span class="badge badge-info">Bulan ' + tagihan.bulan_tagihan + '</span> ' : ''}
                            <span class="badge badge-neutral">${esc(tagihan.nama_tahun_ajaran)}</span>
                            <div class="sisa">Sisa: ${formatRupiah(tagihan.sisa_tagihan)}</div>
                        </div>
                        <div class="nominal-cell">
                            <input type="number" class="nominal-input" id="nominal_${index}" name="nominal[${tagihan.id_tagihan}]" value="${tagihan.sisa_tagihan}" min="0" max="${tagihan.sisa_tagihan}" oninput="updateNominalInput(${index})" disabled>
                            <div id="terbilang_${index}" class="terbilang-text"></div>
                        </div>
                        <div class="date-cell">
                            <input type="date" class="date-input" id="tanggal_${index}" name="tanggal[${tagihan.id_tagihan}]" value="${today}" max="${today}" disabled>
                        </div>
                    </div>`;
            }).join('');

            document.getElementById('tagihan_section').style.display = 'block';
            document.getElementById('pembayaran_section').style.display = 'block';
            setTimeout(() => document.getElementById('tagihan_section').scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
        });
}

function updateTerbilangDisplay(index) {
    const val = parseInt(document.getElementById('nominal_' + index).value) || 0;
    document.getElementById('terbilang_' + index).textContent = val > 0 ? (terbilang(val).trim() + ' Rupiah') : '';
}
function updateNominalInput(index) { updateTerbilangDisplay(index); updateNominal(index); }

function toggleTagihan(index, tagihan) {
    const checkbox = document.getElementById('check_' + index);
    const item = document.getElementById('item_' + index);
    const nominalInput = document.getElementById('nominal_' + index);
    const tanggalInput = document.getElementById('tanggal_' + index);
    if (checkbox.checked) {
        item.classList.add('checked'); nominalInput.disabled = false; tanggalInput.disabled = false;
        updateTerbilangDisplay(index);
        selectedTagihan.push({ id_tagihan: tagihan.id_tagihan, nominal: parseInt(nominalInput.value), tanggal: tanggalInput.value });
    } else {
        item.classList.remove('checked'); nominalInput.disabled = true; tanggalInput.disabled = true;
        document.getElementById('terbilang_' + index).textContent = '';
        selectedTagihan = selectedTagihan.filter(t => t.id_tagihan !== tagihan.id_tagihan);
    }
    updateBayarSummary();
}
function updateNominal(index) {
    const nominalInput = document.getElementById('nominal_' + index);
    const checkbox = document.getElementById('check_' + index);
    if (checkbox.checked) {
        const found = selectedTagihan.find(t => document.querySelector(`input[name="nominal[${t.id_tagihan}]"]`) === nominalInput);
        if (found) found.nominal = parseInt(nominalInput.value) || 0;
        updateBayarSummary();
    }
}
function updateBayarSummary() {
    const count = selectedTagihan.length;
    const total = selectedTagihan.reduce((sum, t) => sum + t.nominal, 0);
    document.getElementById('summary_count').textContent = count;
    document.getElementById('summary_total').textContent = 'Rp ' + formatRupiah(total, '');
    document.getElementById('summary_box').style.display = count > 0 ? 'block' : 'none';
    document.getElementById('btn_submit').disabled = count === 0;
}
document.getElementById('formPembayaran').addEventListener('submit', function (e) {
    if (selectedTagihan.length === 0) { e.preventDefault(); alert('Pilih minimal 1 tagihan untuk dibayar.'); }
});

// Jaga-jaga: fallback kalau footer.php belum sempat diperbarui
if (typeof openSearchDropdown !== 'function') {
    window.openSearchDropdown = function (inputEl, dropdownEl, html) { dropdownEl.innerHTML = html; dropdownEl.style.display = 'block'; };
}
if (typeof closeSearchDropdown !== 'function') {
    window.closeSearchDropdown = function (dropdownEl) { dropdownEl.style.display = 'none'; };
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('chartPembayaran');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($chart_pembayaran, 'date'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        datasets: [{
            label: 'Pembayaran',
            data: <?= json_encode(array_column($chart_pembayaran, 'nominal'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            borderColor: '#0d9488',
            backgroundColor: 'rgba(13,148,136,.08)',
            fill: true,
            tension: 0.35,
            pointRadius: 3,
            pointBackgroundColor: '#0d9488',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000) + 'rb', font: { family: 'Roboto', size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { font: { family: 'Roboto', size: 11 } }, grid: { display: false } }
        }
    }
});
</script>

<?= $this->include('admin/layouts/footer') ?>
