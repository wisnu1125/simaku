<?= $this->include('admin/layouts/header') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ==================== PREMIUM BLUISH TEAL THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-light: #cffafe;
    --primary-bg: #f0fdfa;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
    --radius-lg: 24px;
    --radius-md: 12px;
}

body { background: #f8fafc; font-family: 'Inter', sans-serif; color: var(--text-main); }

/* Page Header */
.page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
.page-title { font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }

/* Buttons Customization */
.btn {
    padding: 12px 22px; border-radius: var(--radius-md); font-weight: 700; 
    display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-secondary { background: white; color: var(--secondary); border: 1.5px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
.btn-secondary:hover { background: #f1f5f9; color: #0f172a; transform: translateY(-1px); }
.btn-teal { background: var(--primary); color: #ffffff; border: none; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.2); }
.btn-teal:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(8, 145, 178, 0.3); }
.btn-export { background: #10b981; color: white; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
.btn-export:hover { background: #059669; transform: translateY(-2px); }

/* Filter Card Refined - HORIZONTAL LAYOUT */
.filter-card {
    background: #ffffff; border-radius: var(--radius-lg); padding: 28px;
    border: 1px solid var(--border); margin-bottom: 28px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
}
.filter-form-horizontal { display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
.filter-group { flex: 1; min-width: 200px; }
.filter-group label {
    display: block; margin-bottom: 10px; font-size: 11px; font-weight: 800; 
    color: var(--secondary); text-transform: uppercase; letter-spacing: 0.5px;
}
.filter-group input, .filter-group select {
    width: 100%; padding: 12px 16px; border: 1.5px solid var(--border);
    border-radius: var(--radius-md); font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.filter-group input:focus, .filter-group select:focus {
    outline: none; border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
}

/* Glassmorphism Stats */
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 30px; }
.stat-box {
    background: white; padding: 26px; border-radius: var(--radius-lg);
    border: 1px solid var(--border); position: relative; transition: all 0.3s;
}
.stat-box:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.08); }
.stat-box::after {
    content: ""; position: absolute; left: 0; top: 25%; bottom: 25%; width: 5px; 
    background: var(--primary); border-radius: 0 10px 10px 0;
}
.stat-label { font-size: 11px; font-weight: 700; color: var(--secondary); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px; }
.stat-value { font-size: 26px; font-weight: 900; color: #0f172a; }

/* Table Master Card */
.card-table {
    background: white; border-radius: var(--radius-lg); padding: 32px;
    border: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
}

/* Modern Search Box Refinement */
.search-wrapper { position: relative; margin-bottom: 25px; max-width: 400px; }
.search-wrapper i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--secondary); z-index: 10; }
.search-wrapper .form-control { 
    padding-left: 48px; height: 50px; border-radius: var(--radius-md); 
    border: 2px solid var(--border); font-weight: 500; background: #fdfdfd; transition: all 0.2s;
}
.search-wrapper .form-control:focus { border-color: var(--primary); background: white; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.08); }

/* DataTable Refinement (No Bullet Points) */
.table-responsive { width: 100%; border-radius: var(--radius-md); }
table.dataTable { border-collapse: separate !important; border-spacing: 0 8px !important; margin-top: 15px !important; }
table.dataTable thead th { 
    background: #f8fafc !important; color: var(--secondary); font-size: 11px; 
    text-transform: uppercase; padding: 18px !important; border: none !important; letter-spacing: 0.5px;
}
table.dataTable tbody tr { background: white; transition: all 0.2s; }
table.dataTable tbody td { 
    padding: 18px !important; border-top: 1px solid #f1f5f9 !important; 
    border-bottom: 1px solid #f1f5f9 !important; font-size: 14px;
}
table.dataTable tbody tr td:first-child { border-left: 1px solid #f1f5f9 !important; border-radius: 12px 0 0 12px; }
table.dataTable tbody tr td:last-child { border-right: 1px solid #f1f5f9 !important; border-radius: 0 12px 12px 0; }
table.dataTable tbody tr:hover { background: #f0fdfa !important; transform: scale(1.005); }

/* FIX DATATABLES PAGINATION (Clean UI) */
.dataTables_wrapper .dataTables_paginate { margin-top: 25px !important; }
.dataTables_wrapper .dataTables_paginate .pagination { 
    display: flex !important; list-style: none !important; padding: 0 !important; margin: 0 !important; gap: 8px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button { margin: 0 !important; padding: 0 !important; border: none !important; background: transparent !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button a {
    padding: 10px 18px; border-radius: 10px; border: 1px solid var(--border);
    background: white; color: var(--text-main) !important; font-weight: 700; font-size: 13px; text-decoration: none; transition: all 0.2s;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.active a,
.dataTables_wrapper .dataTables_paginate .paginate_button.current a {
    background: var(--primary) !important; color: white !important; border-color: var(--primary) !important;
}

/* Badges Stylized */
.badge { padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; letter-spacing: 0.3px; text-transform: uppercase; }
.badge-success { background: #dcfce7; color: #15803d; }
.badge-info { background: #e0f2fe; color: #0369a1; }
</style>

<div class="page-header">
    <h1 class="page-title">Laporan Pembayaran</h1>
    <div style="display: flex; gap: 12px;">
        <a href="<?= base_url('admin/laporan') ?>" class="btn btn-secondary">
            <i class="fas fa-chevron-left"></i> Kembali
        </a>
        <?php if (!empty($pembayaran)): ?>
        <a href="<?= base_url('admin/laporan/export-pembayaran?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="filter-card">
    <form action="<?= base_url('admin/laporan/pembayaran') ?>" method="GET" class="filter-form-horizontal">
        <div class="filter-group">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>">
        </div>
        <div class="filter-group">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="<?= $tanggal_selesai ?>">
        </div>
        <div class="filter-group">
            <label>Tahun Ajaran</label>
            <select name="id_tahun_ajaran">
                <option value="">Semua Tahun Ajaran</option>
                <?php foreach ($tahun_ajaran as $ta): ?>
                    <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= ($id_tahun_ajaran == $ta['id_tahun_ajaran']) ? 'selected' : '' ?>>
                        <?= esc($ta['nama_tahun_ajaran']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-teal">
            <i class="fas fa-filter"></i> Terapkan
        </button>
    </form>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value"><?= number_format(count($pembayaran)) ?></div>
    </div>
    <div class="stat-box" style="border-left-color: #10b981;">
        <div class="stat-label">Akumulasi Pendapatan</div>
        <div class="stat-value" style="color: #10b981;">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Penerimaan Tunai</div>
        <div class="stat-value">Rp <?= number_format($total_tunai, 0, ',', '.') ?></div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Penerimaan Transfer</div>
        <div class="stat-value">Rp <?= number_format($total_transfer, 0, ',', '.') ?></div>
    </div>
</div>

<div class="card-table">
    <div class="search-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control" id="customSearch" placeholder="Cari NIS, Nama, atau Kwitansi...">
    </div>

    <div class="table-responsive">
        <table id="tableLaporan" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Kwitansi</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tagihan</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaran as $index => $p): ?>
                <tr>
                    <td style="font-weight: 700; color: var(--secondary);"><?= $index + 1 ?></td>
                    <td>
                        <div style="font-weight: 700;"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></div>
                        <small style="color: var(--secondary)"><?= date('H:i', strtotime($p['tanggal_bayar'])) ?></small>
                    </td>
                    <td>
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 800; color: var(--primary); background: var(--primary-bg); padding: 5px 10px; border-radius: 8px;">
                            <?= esc($p['nomor_kwitansi']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: #0f172a;"><?= esc($p['nama_siswa']) ?></div>
                        <small style="font-weight: 600; color: var(--secondary)">NIS: <?= esc($p['nis']) ?></small>
                    </td>
                    <td style="font-weight: 700; color: var(--secondary);"><?= esc($p['nama_kelas']) ?></td>
                    <td>
                        <div style="font-weight: 600;"><?= esc($p['nama_tagihan']) ?></div>
                        <small style="color: var(--primary); font-weight: 700;"><?= esc($p['nama_tahun_ajaran']) ?></small>
                    </td>
                    <td style="font-weight: 900; color: var(--primary);">
                        Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <?php if ($p['metode_pembayaran'] === 'tunai'): ?>
                            <span class="badge badge-success"><i class="fas fa-money-bill-wave"></i> TUNAI</span>
                        <?php else: ?>
                            <span class="badge badge-info"><i class="fas fa-university"></i> TRANSFER</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600; font-size: 12px;"><?= esc($p['nama_petugas']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tableLaporan').DataTable({
        "pageLength": 10,
        "dom": 'rt<"bottom"ip><"clear">',
        "language": {
            "search": "Cari data:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada data yang cocok",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Data tidak tersedia",
            "paginate": {
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });

    // Custom Search Integration
    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Clean Pagination Bullets
    $('.dataTables_paginate').find('ul').css('list-style-type', 'none');
});

// Logic Anti-Double Symbol Rupiah & Terbilang
document.querySelectorAll('.nominal-input').forEach(input => {
    input.addEventListener('input', function() {
        let rawValue = this.value.replace(/[^\d]/g, '');
        if (rawValue === '') { this.value = ''; return; }
        const angkaMurni = parseInt(rawValue);
        this.value = 'Rp ' + angkaMurni.toLocaleString('id-ID');
        
        const id = this.id.replace('nominal_display_', '');
        const tText = document.getElementById('terbilang_text_' + id);
        if (tText && typeof terbilang === "function") {
            tText.innerText = terbilang(angkaMurni) + " Rupiah";
            document.getElementById('terbilang_' + id).style.display = 'block';
        }
    });
});
</script>

<?= $this->include('admin/layouts/footer') ?>