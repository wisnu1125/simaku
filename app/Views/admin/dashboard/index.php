<?= $this->include('admin/layouts/header') ?>

<style>
.welcome-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.welcome-row h1 { font-size: 20px; font-weight: 800; color: var(--ink); }
.welcome-row p { font-size: 13px; color: var(--muted); margin-top: 2px; }

.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
@media (min-width: 640px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .stat-grid { grid-template-columns: repeat(5, 1fr); gap: 16px; } }

.stat-card { padding: 18px; position: relative; overflow: hidden; }
.stat-card .icon { width: 36px; height: 36px; border-radius: var(--r-sm); display: flex; align-items: center; justify-content: center; font-size: 15px; margin-bottom: 12px; }
.stat-card .value { font-size: 21px; font-weight: 900; color: var(--ink); letter-spacing: -.3px; }
.stat-card .label { font-size: 11.5px; color: var(--muted); font-weight: 600; margin-top: 3px; }

.quick-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 24px; }
@media (min-width: 640px) { .quick-row { grid-template-columns: repeat(4, 1fr); } }
.quick-btn {
    display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 18px 10px;
    text-decoration: none; color: var(--ink); text-align: center;
}
.quick-btn i { width: 42px; height: 42px; border-radius: 50%; background: var(--brand-bg); color: var(--brand); display: flex; align-items: center; justify-content: center; font-size: 17px; }
.quick-btn span { font-size: 12.5px; font-weight: 700; }
.quick-btn:hover { border-color: var(--brand-light); }

.dash-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 1024px) { .dash-grid { grid-template-columns: 1.6fr 1fr; align-items: start; } }

.section-title { font-size: 14px; font-weight: 800; color: var(--ink); padding: 18px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between; }
.section-title a { font-size: 12px; font-weight: 600; color: var(--brand); text-decoration: none; }

.status-bars { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }
.status-bar-row { display: flex; align-items: center; gap: 10px; }
.status-bar-row .dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.status-bar-row .label { font-size: 12.5px; font-weight: 600; color: var(--body); width: 78px; flex-shrink: 0; }
.status-bar-track { flex: 1; height: 8px; border-radius: 999px; background: var(--border-soft); overflow: hidden; }
.status-bar-fill { height: 100%; border-radius: 999px; }
.status-bar-row .count { font-size: 12.5px; font-weight: 800; color: var(--ink); width: 34px; text-align: right; flex-shrink: 0; }

.mini-row { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border-soft); }
.mini-row:last-child { border-bottom: none; }
.mini-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--brand-bg); color: var(--brand-darker); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12.5px; flex-shrink: 0; }
.mini-row .body { flex: 1; min-width: 0; }
.mini-row .title { font-size: 13px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mini-row .sub { font-size: 11.5px; color: var(--muted); }
.mini-row .amount { font-size: 13px; font-weight: 800; flex-shrink: 0; font-family: 'Roboto Mono', monospace; }
</style>

<div class="welcome-row">
    <div>
        <h1>Halo, <?= esc(explode(' ', session()->get('nama_lengkap'))[0] ?? '') ?> 👋</h1>
        <p>Ringkasan keuangan sekolah hari ini.</p>
    </div>
    <?php if ($tahun_ajaran_aktif): ?>
    <span class="badge badge-brand"><i class="fa-solid fa-calendar-check"></i> TA Aktif: <?= esc($tahun_ajaran_aktif['nama_tahun_ajaran']) ?></span>
    <?php endif; ?>
</div>

<div class="stat-grid">
    <div class="card stat-card">
        <div class="icon" style="background:var(--brand-bg);color:var(--brand);"><i class="fa-solid fa-user-graduate"></i></div>
        <div class="value"><?= number_format($total_siswa) ?></div>
        <div class="label">Siswa Aktif</div>
    </div>
    <div class="card stat-card">
        <div class="icon" style="background:var(--info-bg);color:var(--info);"><i class="fa-solid fa-chalkboard"></i></div>
        <div class="value"><?= number_format($total_kelas) ?></div>
        <div class="label">Total Kelas</div>
    </div>
    <div class="card stat-card">
        <div class="icon" style="background:var(--success-bg);color:var(--success);"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="value" style="font-size:16px;">Rp <?= number_format($total_dibayar, 0, ',', '.') ?></div>
        <div class="label">Total Terkumpul</div>
    </div>
    <div class="card stat-card">
        <div class="icon" style="background:var(--danger-bg);color:var(--danger);"><i class="fa-solid fa-circle-exclamation"></i></div>
        <div class="value" style="font-size:16px;">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
        <div class="label">Total Tunggakan</div>
    </div>
    <div class="card stat-card">
        <div class="icon" style="background:var(--warning-bg);color:var(--warning);"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="value" style="font-size:16px;">Rp <?= number_format($pembayaran_hari_ini, 0, ',', '.') ?></div>
        <div class="label">Masuk Hari Ini</div>
    </div>
</div>

<div class="quick-row">
    <a href="<?= base_url('admin/pembayaran/create') ?>" class="card quick-btn">
        <i class="fa-solid fa-plus"></i>
        <span>Input Bayar</span>
    </a>
    <a href="<?= base_url('admin/siswa') ?>" class="card quick-btn">
        <i class="fa-solid fa-user-graduate"></i>
        <span>Data Siswa</span>
    </a>
    <a href="<?= base_url('admin/tagihan') ?>" class="card quick-btn">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span>Tagihan</span>
    </a>
    <a href="<?= base_url('admin/laporan') ?>" class="card quick-btn">
        <i class="fa-solid fa-chart-line"></i>
        <span>Laporan</span>
    </a>
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
                <div class="mini-row">
                    <div class="mini-avatar"><?= esc(strtoupper(substr($p['nama_lengkap'] ?? '?', 0, 1))) ?></div>
                    <div class="body">
                        <div class="title"><?= esc($p['nama_lengkap']) ?></div>
                        <div class="sub"><?= esc($p['nama_tagihan']) ?> · <?= date('d M, H:i', strtotime($p['tanggal_bayar'])) ?></div>
                    </div>
                    <div class="amount" style="color:var(--success);">+<?= number_format($p['nominal_bayar'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
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
                <div class="mini-row">
                    <div class="mini-avatar"><?= esc(strtoupper(substr($t['nama_lengkap'] ?? '?', 0, 1))) ?></div>
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
