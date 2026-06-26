<?= $this->include('admin/layouts/header') ?>

<style>
/* ==================== BLUISH TEAL AESTHETIC THEME ==================== */
:root {
    --primary: #0891b2;       /* Cyan 600 */
    --primary-dark: #0e7490;  /* Cyan 700 */
    --primary-light: #ecfeff; /* Cyan 50 */
    --secondary: #64748b;     /* Slate 500 */
    --text-main: #0f172a;     /* Slate 900 */
    --text-light: #94a3b8;    /* Slate 400 */
    --bg-body: #f8fafc;       /* Slate 50 */
    --surface: #ffffff;
    --border: #e2e8f0;
    --danger: #ef4444;
    --warning: #f59e0b;
    --success: #10b981;
    --radius: 12px;
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
}

body {
    background-color: var(--bg-body);
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    color: var(--text-main);
    -webkit-font-smoothing: antialiased;
}

.content-wrapper {
    padding: 32px;
    max-width: 100%; /* Full width */
    margin: 0 auto;
}

/* --- ANIMATION --- */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-enter {
    animation: fadeIn 0.4s ease-out forwards;
}

/* --- CARDS --- */
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
}

.card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 24px;
}

/* --- STATS HEADER --- */
.header-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: center;
    margin-bottom: 32px;
}

.stats-wrapper {
    display: flex;
    gap: 16px;
}

.stat-box {
    padding: 16px 24px;
    border-radius: var(--radius);
    min-width: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.stat-box.primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.25);
}

.stat-box.light {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text-main);
}

.stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.8;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 22px;
    font-weight: 800;
}

/* --- FORMS GRID SYSTEM (SINGLE ROW LAYOUT) --- */
.form-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr); /* 12 Kolom Grid */
    gap: 12px;
    align-items: start; /* Penting: Align start agar terbilang tidak merusak baris */
}

/* Utility Span Classes */
.col-span-1 { grid-column: span 1; }
.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }
.col-span-12 { grid-column: span 12; }

.form-group {
    position: relative;
    display: flex;
    flex-direction: column;
}

.form-group label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--secondary);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    transition: all 0.2s;
    background: var(--surface);
    height: 38px;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-light);
}

.form-control.rupiah-input {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 500;
    text-align: right;
}

/* --- BUTTONS --- */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-primary { background: var(--primary); color: white; }
.btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }

.btn-outline { background: transparent; border: 1px solid var(--border); color: var(--secondary); }
.btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

.btn-danger-soft { background: #fef2f2; color: var(--danger); }
.btn-danger-soft:hover { background: #fee2e2; }

.btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; }

/* --- ACTION BUTTONS --- */
.btn-action {
    padding: 6px 10px;
    border: 1px solid var(--border);
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-edit {
    color: var(--warning);
    border-color: #fef3c7;
}

.btn-edit:hover {
    background: #fffbeb;
    border-color: var(--warning);
}

.btn-delete {
    color: var(--danger);
    border-color: #fee2e2;
}

.btn-delete:hover {
    background: #fef2f2;
    border-color: var(--danger);
}

/* --- TABLE & PAGINATION --- */
.table-container {
    width: 100%;
    overflow-x: auto;
}

.esthetic-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.esthetic-table th {
    background: #f1f5f9;
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border);
}

.esthetic-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    vertical-align: middle;
}

.esthetic-table tr:hover td {
    background-color: var(--primary-light);
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    background: #fff;
}

.pagination-btn {
    padding: 6px 12px;
    border: 1px solid var(--border);
    background: white;
    border-radius: 6px;
    font-size: 12px;
    color: var(--secondary);
    cursor: pointer;
    margin: 0 2px;
}

.pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* --- UTILS --- */
.terbilang-text {
    font-size: 10px;
    color: var(--primary);
    font-style: italic;
    margin-top: 4px;
    line-height: 1.2;
    min-height: 12px; /* Menjaga tinggi agar tidak flickering */
}

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: var(--primary-light);
    color: var(--primary);
}

/* Responsive Handling */
@media (max-width: 1200px) {
    .form-grid { grid-template-columns: repeat(6, 1fr); gap: 16px; }
    .col-span-1 { grid-column: span 1; }
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }
}

@media (max-width: 768px) {
    .header-grid { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: 1fr; }
    .col-span-1, .col-span-2, .col-span-3, .col-span-12 { grid-column: span 1; }
    .stats-wrapper { overflow-x: auto; padding-bottom: 10px; }
    .btn { width: 100%; }
}
</style>

<div class="content-wrapper animate-enter">
    
    <div class="header-grid">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
                Pengeluaran Operasional
            </h1>
            <p style="color: var(--secondary); font-size: 14px; margin: 0;">
                Kelola pencatatan arus kas keluar sekolah.
            </p>
        </div>
        <div class="stats-wrapper">
            <div class="stat-box primary">
                <div class="stat-label">Saldo Tersisa</div>
                <div class="stat-value">Rp <?= number_format($total_saldo, 0, ',', '.') ?></div>
            </div>
            <div class="stat-box light">
                <div class="stat-label">Pengeluaran Bulan Ini</div>
                <div class="stat-value" style="color: var(--primary);">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    

    <div class="card">
        <div class="card-body" style="padding: 12px 24px;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
                
                <form method="GET" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--secondary); margin-right: 4px;">
                        <i class="fas fa-filter"></i> Periode:
                    </div>
                    <select name="bulan" class="form-control" style="width: auto; height: 34px; padding: 4px 12px;">
                        <?php 
                        $bulanNama = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                        foreach ($bulanNama as $num => $nama): 
                        ?>
                            <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="tahun" class="form-control" style="width: auto; height: 34px; padding: 4px 12px;">
                        <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>

                <div style="display: flex; gap: 10px;">
                    <button onclick="toggleFormSaldo()" class="btn btn-outline btn-sm">
                        <i class="fas fa-wallet"></i> Kelola Saldo
                    </button>
                    <a href="<?= base_url('admin/operasional/laporan?bulan=' . $bulan . '&tahun=' . $tahun) ?>" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-print"></i> Download Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="formSaldo" style="display: none; margin-bottom: 24px;">
        <div class="card" style="border-left: 4px solid var(--primary);">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-wallet" style="color: var(--primary);"></i> Tambah Saldo Masuk</div>
                <button onclick="toggleFormSaldo()" class="btn btn-danger-soft btn-sm"><i class="fas fa-times"></i></button>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/operasional/storeSaldo') ?>" method="POST" class="form-rupiah-submit">
                    <?= csrf_field() ?>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                        <div>
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal_masuk" value="<?= date('Y-m-d') ?>" required class="form-control">
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>Keterangan Sumber Dana</label>
                                <input type="text" name="keterangan" placeholder="Contoh: Dana BOS Tahap 1" required class="form-control">
                            </div>
                        </div>
                        <div style="display: flex; align-items: start; gap: 10px;">
                            <div class="form-group" style="flex: 1;">
                                <label>Nominal (Rp)</label>
                                <input type="text" name="nominal_masuk" class="form-control rupiah-input input-rupiah" placeholder="0" required onkeyup="formatRupiahElement(this, 'terbilang_saldo')">
                                <div id="terbilang_saldo" class="terbilang-text">Nol Rupiah</div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-top: 24px;"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </form>
                
                <div style="margin-top: 24px;">
                    <h5 style="font-size: 13px; color: var(--secondary); text-transform: uppercase; margin-bottom: 12px; font-weight: 700;">Riwayat Saldo</h5>
                    <div class="table-container">
                        <table class="esthetic-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th style="text-align: right;">Nominal</th>
                                    <th style="text-align: right;">Sisa</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($saldo_list)): ?>
                                    <?php foreach ($saldo_list as $saldo): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($saldo['tanggal_masuk'])) ?></td>
                                        <td><?= esc($saldo['keterangan']) ?></td>
                                        <td style="text-align: right;">Rp <?= number_format($saldo['nominal_masuk'], 0, ',', '.') ?></td>
                                        <td style="text-align: right; font-weight: 700; color: var(--primary);">Rp <?= number_format($saldo['saldo_tersisa'], 0, ',', '.') ?></td>
                                        <td style="text-align: center;">
                                            <?php if ($saldo['saldo_tersisa'] == $saldo['nominal_masuk']): ?>
                                                <form action="<?= base_url('admin/operasional/deleteSaldo/' . $saldo['id_saldo']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Hapus saldo ini?')">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-danger-soft btn-sm" style="padding: 4px 8px;"><i class="fas fa-trash"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <span style="font-size: 10px; color: var(--text-light);">Terpakai</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" style="text-align:center; padding: 20px;">Belum ada data saldo.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span id="formIcon"><i class="fas fa-plus-circle" style="color: var(--primary);"></i></span>
                <span id="formTitle">Catat Pengeluaran Baru</span>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/operasional/store') ?>" method="POST" id="formPengeluaran" class="form-rupiah-submit">
                <?= csrf_field() ?>
                <input type="hidden" name="id_pengeluaran" id="id_pengeluaran">
                
                <div class="form-grid">
                    
                    <div class="col-span-2">
                        <div class="form-group">
                            <label>Tanggal *</label>
                            <input type="date" name="tanggal" id="input_tanggal" value="<?= date('Y-m-d') ?>" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-span-1">
                        <div class="form-group">
                            <label>Kode Ref</label>
                            <input type="text" name="kode" id="input_kode" value="OP" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-span-3">
                        <div class="form-group">
                            <label>Keterangan Pengeluaran *</label>
                            <input type="text" name="keterangan" id="input_keterangan" placeholder="Nama Barang / Jasa" required class="form-control">
                        </div>
                    </div>

                    <div class="col-span-1">
                        <div class="form-group">
                            <label>Qty *</label>
                            <input type="number" name="jumlah" id="jumlah" value="1" step="0.01" min="0.01" required class="form-control" style="text-align: center;">
                        </div>
                    </div>
                    
                    <div class="col-span-1">
                        <div class="form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" id="input_satuan" value="PCS" class="form-control" style="text-align: center;">
                        </div>
                    </div>
                    
                    <div class="col-span-2">
                        <div class="form-group">
                            <label>Harga Satuan (Rp) *</label>
                            <input type="text" name="harga_satuan" id="harga_satuan" required class="form-control rupiah-input input-rupiah" placeholder="0" onkeyup="formatRupiahElement(this, 'terbilang_harga')">
                            <div id="terbilang_harga" class="terbilang-text">Nol Rupiah</div>
                        </div>
                    </div>
                    
                    <div class="col-span-2">
                        <div class="form-group">
                            <label>Total Otomatis</label>
                            <input type="text" id="total_display" readonly class="form-control rupiah-input" style="color: var(--primary); background: #ecfeff; border-color: #cffafe; font-weight: 800;">
                            <div id="terbilang_total" class="terbilang-text">Nol Rupiah</div>
                        </div>
                    </div>

                    <div class="col-span-12" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; border-top: 1px dashed var(--border); padding-top: 16px;">
                        <button type="button" id="btnCancel" onclick="resetForm()" class="btn btn-danger-soft" style="display: none;">Batal Edit</button>
                        <button type="submit" id="btnSubmit" class="btn btn-primary" style="min-width: 150px;">Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-list"></i> Riwayat Transaksi</div>
            <div class="badge"><?= count($pengeluaran) ?> Data</div>
        </div>
        
        <div class="table-container">
            <table class="esthetic-table" id="tablePengeluaran">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Keterangan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Total</th>
                        <th>User</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($pengeluaran)): ?>
                        <tr><td colspan="9" class="text-center p-5 text-muted">Belum ada data.</td></tr>
                    <?php else: ?>
                        <?php 
                        // Urutkan dari yang terbaru berdasarkan tanggal dan id
                        usort($pengeluaran, function($a, $b) {
                            $dateCompare = strtotime($b['tanggal']) - strtotime($a['tanggal']);
                            if ($dateCompare === 0) {
                                return $b['id_pengeluaran'] - $a['id_pengeluaran'];
                            }
                            return $dateCompare;
                        });
                        ?>
                        <?php foreach ($pengeluaran as $index => $item): ?>
                            <tr class="data-row">
                                <td align="center" style="color: var(--secondary); font-weight: 600;"><?= $index + 1 ?></td>
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 600; color: var(--text-main);"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></div>
                                    <div style="font-size: 10px; color: var(--text-light);"><?= date('H:i', strtotime($item['created_at'] ?? $item['tanggal'])) ?></div>
                                </td>
                                <td>
                                    <?php if($item['kode']): ?>
                                        <span style="background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; color: var(--primary); border: 1px solid #a5f3fc;"><?= esc($item['kode']) ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-size: 11px;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: var(--text-main);"><?= esc($item['keterangan']) ?></div>
                                </td>
                                <td align="center">
                                    <span style="font-weight: 600; color: var(--text-main);"><?= number_format($item['jumlah'] + 0, 0, ',', '.') ?></span>
                                    <small style="color: var(--text-light); font-size: 10px; margin-left: 2px;"><?= esc($item['satuan']) ?></small>
                                </td>
                                <td align="right" style="font-family: 'JetBrains Mono', monospace; color: var(--secondary); font-size: 12px;">
                                    Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                                </td>
                                <td align="right">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary); font-size: 13px;">
                                        Rp <?= number_format($item['total'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td style="font-size: 11px; color: var(--secondary);">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-user-circle" style="color: var(--primary);"></i>
                                        <span><?= esc($item['nama_user']) ?></span>
                                    </div>
                                </td>
                                <td align="center">
                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                        <button onclick='editPengeluaran(<?= json_encode($item) ?>)' class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= base_url('admin/operasional/delete/' . $item['id_pengeluaran']) ?>" method="POST" style="display:inline; margin: 0;" onsubmit="return confirm('Yakin hapus data ini?\n\n' + '<?= esc($item['keterangan']) ?>' + '\nTotal: Rp <?= number_format($item['total'], 0, ',', '.') ?>')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper" id="paginationControls">
            <div style="font-size: 12px; color: var(--secondary);">
                Menampilkan <span id="pageInfoStart">0</span> - <span id="pageInfoEnd">0</span> dari <span id="pageInfoTotal">0</span> data
            </div>
            <div id="paginationButtons">
                </div>
        </div>
    </div>
</div>

<script>
// --- LOGIKA AUTO-FOCUS & KURSOR OTOMATIS (SETIAP LOAD) ---
document.addEventListener('DOMContentLoaded', function() {
    const inputKode = document.getElementById('input_kode');
    if (inputKode) {
        // Paksa fokus ke input
        inputKode.focus();
        
        // Letakkan kursor di posisi paling akhir
        const length = inputKode.value.length;
        inputKode.setSelectionRange(length, length);
    }
});


// --- 1. CONFIG PAGINATION CLIENT-SIDE ---
const ROWS_PER_PAGE = 20;
let currentPage = 1;
const rows = document.querySelectorAll('.data-row');
const totalRows = rows.length;

function renderPagination() {
    if(totalRows === 0) return;
    const totalPages = Math.ceil(totalRows / ROWS_PER_PAGE);
    
    rows.forEach(row => row.style.display = 'none'); 

    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    
    for(let i = start; i < end; i++) {
        if(rows[i]) rows[i].style.display = 'table-row';
    }

    document.getElementById('pageInfoStart').textContent = start + 1;
    document.getElementById('pageInfoEnd').textContent = Math.min(end, totalRows);
    document.getElementById('pageInfoTotal').textContent = totalRows;

    const btnContainer = document.getElementById('paginationButtons');
    btnContainer.innerHTML = '';

    const prevBtn = document.createElement('button');
    prevBtn.className = 'pagination-btn';
    prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => { currentPage--; renderPagination(); };
    btnContainer.appendChild(prevBtn);

    for(let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => { currentPage = i; renderPagination(); };
            btnContainer.appendChild(btn);
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            const span = document.createElement('span');
            span.textContent = '...';
            span.style.fontSize = '12px';
            span.style.color = '#94a3b8';
            span.style.margin = '0 4px';
            btnContainer.appendChild(span);
        }
    }

    const nextBtn = document.createElement('button');
    nextBtn.className = 'pagination-btn';
    nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => { currentPage++; renderPagination(); };
    btnContainer.appendChild(nextBtn);
}

document.addEventListener('DOMContentLoaded', renderPagination);

// --- 2. FORMAT RUPIAH & TERBILANG ---
function terbilang(angka) {
    angka = Math.floor(Math.abs(angka));
    var baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    var hasil = '';
    if (angka < 12) hasil = ' ' + baca[angka];
    else if (angka < 20) hasil = terbilang(angka - 10) + ' Belas';
    else if (angka < 100) hasil = terbilang(Math.floor(angka / 10)) + ' Puluh' + terbilang(angka % 10);
    else if (angka < 200) hasil = ' Seratus' + terbilang(angka - 100);
    else if (angka < 1000) hasil = terbilang(Math.floor(angka / 100)) + ' Ratus' + terbilang(angka % 100);
    else if (angka < 2000) hasil = ' Seribu' + terbilang(angka - 1000);
    else if (angka < 1000000) hasil = terbilang(Math.floor(angka / 1000)) + ' Ribu' + terbilang(angka % 1000);
    else if (angka < 1000000000) hasil = terbilang(Math.floor(angka / 1000000)) + ' Juta' + terbilang(angka % 1000000);
    return hasil;
}

// Update Helper: Menerima ID target terbilang opsional
function formatRupiahElement(e, targetTerbilangId) {
    let val = e.value.replace(/[^0-9]/g, '');
    let numberVal = parseFloat(val) || 0;

    e.value = val.length > 0 ? new Intl.NumberFormat('id-ID').format(val) : '';
    
    // Update text terbilang jika ID target diberikan
    if (targetTerbilangId) {
        const textElem = document.getElementById(targetTerbilangId);
        if (textElem) {
            textElem.textContent = numberVal > 0 ? terbilang(numberVal) + " Rupiah" : "Nol Rupiah";
        }
    }

    // Trigger kalkulasi total jika ini input harga
    if(e.id === 'harga_satuan') calculateTotal();
}

document.getElementById('jumlah').addEventListener('input', calculateTotal);
// Event listener harga satuan sudah di-handle inline via onkeyup

function calculateTotal() {
    const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
    const hargaStr = document.getElementById('harga_satuan').value.replace(/\./g, '');
    const hargaSatuan = parseFloat(hargaStr) || 0;
    const total = jumlah * hargaSatuan;

    document.getElementById('total_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    // Update terbilang Total
    document.getElementById('terbilang_total').textContent = total > 0 ? terbilang(total) + " Rupiah" : "Nol Rupiah";
}

document.querySelectorAll('.form-rupiah-submit').forEach(form => {
    form.addEventListener('submit', function() {
        this.querySelectorAll('.input-rupiah').forEach(input => {
            input.value = input.value.replace(/\./g, '');
        });
    });
});

// --- 3. UI LOGIC (Toggle & Edit) ---
function toggleFormSaldo() {
    const form = document.getElementById('formSaldo');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if(form.style.display === 'block') form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function editPengeluaran(item) {
    document.getElementById('formPengeluaran').scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('formPengeluaran').action = '<?= base_url('admin/operasional/update') ?>/' + item.id_pengeluaran;
    
    document.getElementById('id_pengeluaran').value = item.id_pengeluaran;
    document.getElementById('input_tanggal').value = item.tanggal;
    
    document.getElementById('input_kode').value = item.kode || 'OP-'; 
    
    document.getElementById('input_keterangan').value = item.keterangan;
    document.getElementById('jumlah').value = item.jumlah;
    document.getElementById('input_satuan').value = item.satuan;
    document.getElementById('harga_satuan').value = new Intl.NumberFormat('id-ID').format(item.harga_satuan);
    
    // Trigger update manual untuk terbilang harga saat edit
    const hargaVal = parseFloat(item.harga_satuan) || 0;
    document.getElementById('terbilang_harga').textContent = hargaVal > 0 ? terbilang(hargaVal) + " Rupiah" : "Nol Rupiah";

    calculateTotal();
    
    document.getElementById('formIcon').innerHTML = '<i class="fas fa-edit text-warning"></i>';
    document.getElementById('formTitle').innerHTML = 'Edit Pengeluaran (ID: ' + item.id_pengeluaran + ')';
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.classList.remove('btn-primary');
    btnSubmit.style.backgroundColor = '#f59e0b';
    btnSubmit.textContent = 'Update Data';
    
    document.getElementById('btnCancel').style.display = 'inline-block';
}

function resetForm() {
    document.getElementById('formPengeluaran').action = '<?= base_url('admin/operasional/store') ?>';
    document.getElementById('formPengeluaran').reset();
    document.getElementById('id_pengeluaran').value = '';
    
    document.getElementById('input_tanggal').value = '<?= date('Y-m-d') ?>';
    document.getElementById('input_kode').value = 'OP-';
    document.getElementById('jumlah').value = '1';
    document.getElementById('input_satuan').value = 'pcs';
    document.getElementById('total_display').value = '';
    
    // Reset semua terbilang
    document.getElementById('terbilang_total').textContent = 'Nol Rupiah';
    document.getElementById('terbilang_harga').textContent = 'Nol Rupiah';
    
    document.getElementById('formIcon').innerHTML = '<i class="fas fa-plus-circle" style="color: var(--primary);"></i>';
    document.getElementById('formTitle').textContent = 'Catat Pengeluaran Baru';
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.classList.add('btn-primary');
    btnSubmit.style.backgroundColor = '';
    btnSubmit.textContent = 'Simpan Data';
    
    document.getElementById('btnCancel').style.display = 'none';
}
</script>

<?= $this->include('admin/layouts/footer') ?>