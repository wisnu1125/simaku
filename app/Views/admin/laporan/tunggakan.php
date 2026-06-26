<?= $this->include('admin/layouts/header') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
/* ==================== PREMIUM BLUISH TEAL ARREARS THEME ==================== */
:root {
    --primary: #0891b2;
    --primary-hover: #0e7490;
    --primary-light: #cffafe;
    --primary-bg: #f0fdfa;
    --secondary: #64748b;
    --text-main: #1e293b;
    --border: #e2e8f0;
    --danger: #ef4444;
    --radius-lg: 24px;
    --radius-md: 12px;
}

body { 
    background: #f8fafc; 
    font-family: 'Inter', sans-serif; 
    color: var(--text-main); 
}

/* Page Header */
.page-header { 
    margin-bottom: 35px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

.page-title { 
    font-size: 28px; 
    font-weight: 800; 
    color: #0f172a; 
    letter-spacing: -0.5px; 
}

/* Buttons Customization */
.btn {
    padding: 12px 22px; 
    border-radius: var(--radius-md); 
    font-weight: 700; 
    display: inline-flex; 
    align-items: center; 
    gap: 10px; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
}

.btn-secondary { 
    background: white; 
    color: var(--secondary); 
    border: 1.5px solid var(--border); 
}

.btn-secondary:hover { 
    background: #f1f5f9; 
    color: #0f172a; 
    transform: translateY(-1px); 
}

.btn-export { 
    background: #10b981; 
    color: white; 
    border: none; 
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); 
}

.btn-export:hover { 
    background: #059669; 
    transform: translateY(-2px); 
}

.btn-teal { 
    background: var(--primary); 
    color: #ffffff; 
    border: none; 
    box-shadow: 0 4px 12px rgba(8, 145, 178, 0.2); 
}

/* Horizontal Filter Card */
.filter-card {
    background: #ffffff; 
    border-radius: var(--radius-lg); 
    padding: 28px;
    border: 1px solid var(--border); 
    margin-bottom: 30px; 
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
}

.filter-form-horizontal { 
    display: flex; 
    align-items: flex-end; 
    gap: 20px; 
    flex-wrap: wrap; 
}

.filter-group { 
    flex: 1; 
    min-width: 200px; 
}

.filter-group label {
    display: block; 
    margin-bottom: 10px; 
    font-size: 11px; 
    font-weight: 800; 
    color: var(--secondary); 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
}

.filter-group select {
    width: 100%; 
    padding: 12px 16px; 
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md); 
    font-size: 14px; 
    background: #f8fafc; 
    transition: all 0.2s;
}

.filter-group select:focus { 
    border-color: var(--primary); 
    background: white; 
    outline: none; 
    box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1); 
}

/* Total Summary Banner */
.stat-box {
    background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%);
    padding: 35px; 
    border-radius: var(--radius-lg);
    border: 1px solid #fecaca; 
    margin-bottom: 35px; 
    text-align: center;
    position: relative; 
    overflow: hidden;
}

.stat-box::before { 
    content: ""; 
    position: absolute; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 6px; 
    background: var(--danger); 
}

.stat-label { 
    font-size: 12px; 
    font-weight: 800; 
    color: #991b1b; 
    text-transform: uppercase; 
    margin-bottom: 10px; 
    letter-spacing: 1px; 
}

.stat-value { 
    font-size: 48px; 
    font-weight: 900; 
    color: var(--danger); 
}

/* Data Container */
.main-card {
    background: white; 
    border-radius: var(--radius-lg); 
    padding: 35px;
    border: 1px solid var(--border); 
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
}

/* Modern Search Box */
.search-wrapper { 
    position: relative; 
    margin-bottom: 30px; 
    width: 100%; /* Lebar Penuh */
}

.search-wrapper i { 
    position: absolute; 
    left: 20px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: var(--secondary); 
    z-index: 10; 
}

.search-wrapper .form-control { 
    padding-left: 55px; 
    height: 54px; 
    border-radius: 15px; 
    border: 2px solid var(--border); 
    font-weight: 500; 
    background: #fdfdfd; 
    transition: all 0.2s; 
    width: 100%;
}

.search-wrapper .form-control:focus { 
    border-color: var(--primary); 
    background: white; 
    box-shadow: 0 8px 20px rgba(8, 145, 178, 0.08); 
    outline: none; 
}

/* Student Profile Card - FULL WIDTH SEJAJAR ATAS */
.siswa-card {
    border: 1.5px solid var(--border); 
    border-radius: 20px; 
    padding: 35px; /* Padding lebih luas */
    background: #ffffff; 
    transition: all 0.3s;
    width: 100%; /* Mengikuti lebar container */
}

.siswa-header {
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    margin-bottom: 30px; 
    padding-bottom: 30px; 
    border-bottom: 2px solid #f8fafc;
}

.siswa-info h4 { 
    font-size: 24px; /* Font lebih besar */
    font-weight: 900; 
    color: #0f172a; 
    margin-bottom: 10px; 
}

.siswa-detail { 
    font-size: 15px; 
    color: var(--secondary); 
    font-weight: 600; 
    display: flex; 
    gap: 20px; 
}

.siswa-detail span i { 
    color: var(--primary); 
    margin-right: 8px; 
}

/* Badge Tunggakan */
.tunggakan-badge { 
    background: #fff1f2; 
    padding: 20px 30px; 
    border-radius: 18px; 
    text-align: right; 
    border: 1px solid #fecaca; 
}

.tunggakan-label { 
    font-size: 12px; 
    font-weight: 800; 
    color: #b91c1c; 
    text-transform: uppercase; 
    margin-bottom: 8px; 
    letter-spacing: 0.5px;
}

.tunggakan-value { 
    font-size: 28px; 
    font-weight: 900; 
    color: var(--danger); 
}

/* Itemized Bills - GRID DUA KOLOM JIKA LEBAR */
.tagihan-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); 
    gap: 15px; 
}

.tagihan-item {
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    padding: 20px 25px; 
    background: #f8fafc; 
    border-radius: 15px;
    border-left: 6px solid var(--danger); 
    transition: 0.3s;
}

.tagihan-item:hover { 
    transform: scale(1.01); 
    background: #ffffff; 
    box-shadow: 0 5px 15px rgba(0,0,0,0.03); 
    border-left-color: #b91c1c;
}

.tagihan-name { 
    color: #1e293b; 
    font-weight: 700; 
    font-size: 16px; 
}

.tagihan-name small { 
    color: var(--secondary); 
    display: block; 
    font-weight: 500; 
    margin-top: 5px; 
    font-size: 13px;
}

.tagihan-amount { 
    color: var(--danger); 
    font-weight: 900; 
    font-family: 'JetBrains Mono', monospace; 
    font-size: 18px; 
}

/* DataTables Pagination (1 Row Mode) */
.dataTables_wrapper .dataTables_paginate { 
    margin-top: 40px !important; 
    display: flex; 
    justify-content: center; 
}

.dataTables_wrapper .dataTables_paginate .pagination { 
    display: flex !important; 
    list-style: none !important; 
    padding: 0; 
    gap: 10px; 
}

.dataTables_wrapper .dataTables_paginate .paginate_button { 
    margin: 0; 
    padding: 0; 
    border: none !important; 
    background: transparent !important; 
}

.dataTables_wrapper .dataTables_paginate .paginate_button a {
    padding: 12px 24px; 
    border-radius: 15px; 
    border: 1px solid var(--border);
    background: white; 
    color: var(--primary) !important; 
    font-weight: 800; 
    font-size: 14px; 
    text-decoration: none; 
    transition: 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current a {
    background: var(--primary) !important; 
    color: white !important; 
    border-color: var(--primary) !important; 
    box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
}

.empty-state { 
    text-align: center; 
    padding: 80px 20px; 
}

.empty-state i {
    color: #10b981;
    margin-bottom: 25px;
}
</style>

<div class="page-header">
    <h1 class="page-title">Laporan Tunggakan</h1>
    <div style="display: flex; gap: 12px;">
        <a href="<?= base_url('admin/laporan') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <?php if (!empty($tunggakan_by_siswa)): ?>
        <a href="<?= base_url('admin/laporan/export-tunggakan?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="filter-card">
    <form action="<?= base_url('admin/laporan/tunggakan') ?>" method="GET" class="filter-form-horizontal">
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
        <div class="filter-group">
            <label>Pilih Kelas</label>
            <select name="id_kelas">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelas as $k): ?>
                    <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?> - <?= esc($k['nama_tahun_ajaran']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-teal">
            <i class="fas fa-filter"></i> Tampilkan Laporan
        </button>
    </form>
</div>

<div class="stat-box">
    <div class="stat-label">Total Outstanding Piutang Keseluruhan</div>
    <div class="stat-value">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
    <div style="font-size: 15px; color: #7f1d1d; margin-top: 12px; font-weight: 700;">
        <i class="fas fa-exclamation-circle"></i> Terdeteksi <?= count($tunggakan_by_siswa) ?> Siswa yang masih memiliki tagihan aktif
    </div>
</div>

<div class="main-card">
    <?php if (empty($tunggakan_by_siswa)): ?>
        <div class="empty-state">
            <i class="fas fa-check-circle fa-5x"></i>
            <h3 style="font-weight: 900; font-size: 24px; color: #0f172a; margin-top: 20px;">Data Bersih!</h3>
            <p style="color: var(--secondary); font-size: 16px;">Tidak ditemukan tunggakan pada kriteria filter yang Anda pilih.</p>
        </div>
    <?php else: ?>
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" id="customSearchSiswa" placeholder="Ketik NIS, Nama Siswa, atau Nama Kelas untuk mencari cepat...">
        </div>

        <div class="table-responsive">
            <table id="tableTunggakan" class="table" style="border: none; width: 100%;">
                <thead style="display: none;"><tr><th>Data Profil Tunggakan</th></tr></thead>
                <tbody>
                    <?php foreach ($tunggakan_by_siswa as $siswa): ?>
                    <tr>
                        <td style="border: none; padding: 0 0 40px 0;">
                            <div class="siswa-card">
                                <div class="siswa-header">
                                    <div class="siswa-info">
                                        <h4><?= esc($siswa['nama_siswa']) ?></h4>
                                        <div class="siswa-detail">
                                            <span><i class="fas fa-id-card"></i> NIS: <?= esc($siswa['nis']) ?></span>
                                            <span><i class="fas fa-school"></i> Kelas: <?= esc($siswa['nama_kelas'] ?? '-') ?></span>
                                            <?php if ($siswa['telp_wali']): ?>
                                                <span><i class="fab fa-whatsapp"></i> WA: <?= esc($siswa['telp_wali']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="tunggakan-badge">
                                        <div class="tunggakan-label">Total Tunggakan Personal</div>
                                        <div class="tunggakan-value">Rp <?= number_format($siswa['total_tunggakan'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <div class="tagihan-grid">
                                    <?php foreach ($siswa['tagihan'] as $t): ?>
                                    <div class="tagihan-item">
                                        <div class="tagihan-name">
                                            <?= esc($t['nama_tagihan']) ?>
                                            <small>
                                                <?= $t['bulan_tagihan'] ? 'Periode Bulan '.$t['bulan_tagihan'].' • ' : '' ?>
                                                Tahun Ajaran <?= esc($t['nama_tahun_ajaran']) ?>
                                            </small>
                                        </div>
                                        <div class="tagihan-amount">Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    var table = $('#tableTunggakan').DataTable({
        "pageLength": 1, // Fokus pada 1 siswa per halaman sesuai permintaan sebelumnya
        "dom": 'rt<"bottom"ip><"clear">',
        "ordering": false,
        "language": {
            "paginate": { 
                "next": "Lihat Siswa Berikutnya <i class='fas fa-arrow-right'></i>", 
                "previous": "<i class='fas fa-arrow-left'></i> Lihat Siswa Sebelumnya" 
            },
            "info": "Menampilkan Profil Tunggakan ke-_START_ dari total _TOTAL_ Penunggak"
        }
    });

    // Integrasi Pencarian Custom
    $('#customSearchSiswa').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Reset list-style untuk pagination agar bersih
    $('.dataTables_paginate').find('ul').css('list-style-type', 'none');
});
</script>

<?= $this->include('admin/layouts/footer') ?>