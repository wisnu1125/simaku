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
        :root {
            --primary: #0891b2;
            --primary-dark: #0e7490;
            --primary-light: #ecfeff;
            --success: #16a34a;
            --success-light: #f0fdf4;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff; border-radius: 20px; padding: 36px 28px;
            max-width: 420px; width: 100%; text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,.06); border: 1px solid var(--border);
        }
        .icon-circle {
            width: 72px; height: 72px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 30px;
        }
        h1 { font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 8px; }
        p.desc { font-size: 13.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 22px; }
        .amount { font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
        .kwitansi-tag { display: inline-block; font-family: monospace; font-size: 12px; background: var(--primary-light); color: var(--primary-dark); padding: 4px 12px; border-radius: 999px; margin-bottom: 20px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 14px;
            text-decoration: none; cursor: pointer; border: none;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .refresh-note { font-size: 11.5px; color: var(--text-muted); margin-top: 14px; }
    </style>
</head>
<body>

<div class="card">
    <?php if (!$trx): ?>
        <div class="icon-circle" style="background:var(--warning-light); color:var(--warning);"><i class="fa-solid fa-question"></i></div>
        <h1>Transaksi Tidak Ditemukan</h1>
        <p class="desc">Kami tidak dapat menemukan detail transaksi ini. Jika Anda baru saja melakukan pembayaran, silakan cek kembali riwayat pembayaran di halaman Cek Tagihan.</p>
        <a href="<?= base_url() ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php elseif ($trx['status'] === 'paid'): ?>
        <div class="icon-circle" style="background:var(--success-light); color:var(--success);"><i class="fa-solid fa-circle-check"></i></div>
        <h1>Pembayaran Berhasil!</h1>
        <p class="desc">Terima kasih, pembayaran Anda sudah kami terima dan tagihan sudah diperbarui.</p>
        <div class="amount">Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?></div>
        <div class="kwitansi-tag"><i class="fa-solid fa-receipt"></i> <?= esc($trx['xendit_invoice_id']) ?></div>
        <br>
        <a href="<?= base_url() ?>" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cek Tagihan Lagi</a>

    <?php elseif ($trx['status'] === 'expired'): ?>
        <div class="icon-circle" style="background:var(--danger-light); color:var(--danger);"><i class="fa-solid fa-clock"></i></div>
        <h1>Waktu Pembayaran Habis</h1>
        <p class="desc">Link pembayaran ini sudah kedaluwarsa. Silakan cek tagihan kembali dan buat pembayaran baru.</p>
        <a href="<?= base_url() ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php else: ?>
        <div class="icon-circle" style="background:var(--warning-light); color:var(--warning);"><i class="fa-solid fa-hourglass-half"></i></div>
        <h1>Sedang Diproses</h1>
        <p class="desc">Kami sedang menunggu konfirmasi pembayaran Anda. Ini biasanya hanya perlu beberapa detik. Halaman ini akan otomatis diperbarui.</p>
        <div class="amount">Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?></div>
        <div class="refresh-note"><i class="fa-solid fa-rotate fa-spin"></i> Memuat ulang otomatis…</div>
        <script>
            // Cek ulang status setiap 4 detik, maksimal 15x (~1 menit) -- setelah itu berhenti
            // supaya tidak nge-loop selamanya kalau memang pembayarannya belum juga masuk.
            let percobaan = 0;
            const interval = setInterval(() => {
                percobaan++;
                if (percobaan > 15) { clearInterval(interval); return; }
                window.location.reload();
            }, 4000);
        </script>
    <?php endif; ?>
</div>

</body>
</html>
