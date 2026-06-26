<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperasionalModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OperasionalController extends BaseController
{
    protected $operasionalModel;
    
    public function __construct()
    {
        $this->operasionalModel = new OperasionalModel();
    }
    
    // ========================================
    // INDEX - GABUNGAN PENGELUARAN & SALDO
    // ========================================
    
    public function index()
    {
        // Filter berdasarkan bulan/tahun
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $startDate = "$tahun-$bulan-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get data pengeluaran
        $pengeluaran = $this->operasionalModel->getPengeluaranByPeriode($startDate, $endDate);
        
        // Get total
        $totalPengeluaran = $this->operasionalModel->getTotalPengeluaranByPeriode($startDate, $endDate);
        
        // Get grouped by kode
        $groupedByKode = $this->operasionalModel->getPengeluaranGroupedByKode($startDate, $endDate);
        
        // Get saldo
        $totalSaldo = $this->operasionalModel->getTotalSaldoTersisa();
        $saldoList = $this->operasionalModel->getAllSaldo();
        
        $data = [
            'title' => 'Pengeluaran Operasional',
            'pengeluaran' => $pengeluaran,
            'total_pengeluaran' => $totalPengeluaran,
            'grouped_by_kode' => $groupedByKode,
            'total_saldo' => $totalSaldo,
            'saldo_list' => $saldoList,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/operasional/index', $data);
    }
    
    // ========================================
    // PENGELUARAN - CRUD
    // ========================================
    
    public function store()
    {
        // Validasi
        if (!$this->validate([
            'tanggal' => 'required|valid_date',
            'kode' => 'permit_empty|max_length[50]',
            'keterangan' => 'required|min_length[3]',
            'jumlah' => 'required|decimal',
            'satuan' => 'permit_empty|max_length[50]',
            'harga_satuan' => 'required|decimal'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jumlah = (float) $this->request->getPost('jumlah');
        $hargaSatuan = (float) $this->request->getPost('harga_satuan');
        $total = $jumlah * $hargaSatuan;
        
        // Cek saldo cukup
        $totalSaldo = $this->operasionalModel->getTotalSaldoTersisa();
        if ($totalSaldo < $total) {
            return redirect()->back()->withInput()->with('error', 'Saldo operasional tidak mencukupi! Saldo tersisa: Rp ' . number_format($totalSaldo, 0, ',', '.'));
        }
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Insert pengeluaran
            $data = [
                'tanggal' => $this->request->getPost('tanggal'),
                'kode' => $this->request->getPost('kode') ?: null,
                'keterangan' => $this->request->getPost('keterangan'),
                'jumlah' => $jumlah,
                'satuan' => $this->request->getPost('satuan') ?: 'unit',
                'harga_satuan' => $hargaSatuan,
                'total' => $total,
                'id_user' => session()->get('id_user')
            ];
            
            $this->operasionalModel->insert($data);
            
            // Kurangi saldo
            $this->operasionalModel->kurangiSaldo($total);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan pengeluaran');
            }
            
            return redirect()->to('/admin/operasional')->with('success', 'Pengeluaran berhasil dicatat');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    
    public function delete($id)
    {
        $pengeluaran = $this->operasionalModel->find($id);
        
        if (!$pengeluaran) {
            return redirect()->to('/admin/operasional')->with('error', 'Data pengeluaran tidak ditemukan');
        }
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Kembalikan saldo
            $this->operasionalModel->tambahSaldo($pengeluaran['total']);
            
            // Hapus pengeluaran
            $this->operasionalModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus pengeluaran');
            }
            
            return redirect()->to('/admin/operasional')->with('success', 'Pengeluaran berhasil dihapus');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/admin/operasional')->with('error', $e->getMessage());
        }
    }
    
    public function laporan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $startDate = "$tahun-$bulan-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $pengeluaran = $this->operasionalModel->getPengeluaranByPeriode($startDate, $endDate);
        $totalPengeluaran = $this->operasionalModel->getTotalPengeluaranByPeriode($startDate, $endDate);
        $groupedByKode = $this->operasionalModel->getPengeluaranGroupedByKode($startDate, $endDate);
        
        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIMAKU')
            ->setTitle('Laporan Pengeluaran Operasional')
            ->setSubject('Laporan Pengeluaran')
            ->setDescription('Laporan pengeluaran operasional periode ' . $bulanNama[$bulan] . ' ' . $tahun);
        
        // Header
        $sheet->setCellValue('A1', 'SMPIT WAHDHATUL UMMAH');
        $sheet->setCellValue('A2', 'LAPORAN PENGELUARAN OPERASIONAL');
        $sheet->setCellValue('A3', 'Periode: ' . $bulanNama[$bulan] . ' ' . $tahun);
        $sheet->setCellValue('A4', 'Tanggal Cetak: ' . date('d F Y, H:i') . ' WIB');
        
        // Merge cells untuk header
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->mergeCells('A4:I4');
        
        // Style header
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:A4')->getFont()->setSize(11);
        
        // Summary by Kode
        $row = 6;
        if (!empty($groupedByKode)) {
            $sheet->setCellValue('A' . $row, 'RINGKASAN PER KODE');
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0F2FE');
            $row++;
            
            // Header ringkasan
            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Jumlah Item');
            $sheet->setCellValue('C' . $row, 'Total Pengeluaran');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0891B2');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $row++;
            
            foreach ($groupedByKode as $group) {
                $sheet->setCellValue('A' . $row, $group['kode'] ?: '(Tanpa Kode)');
                $sheet->setCellValue('B' . $row, $group['jumlah_item']);
                $sheet->setCellValue('C' . $row, $group['total_pengeluaran']);
                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }
            $row += 2;
        }
        
        // Table Header
        $headerRow = $row;
        $sheet->setCellValue('A' . $headerRow, 'No');
        $sheet->setCellValue('B' . $headerRow, 'Tanggal');
        $sheet->setCellValue('C' . $headerRow, 'Kode');
        $sheet->setCellValue('D' . $headerRow, 'Keterangan');
        $sheet->setCellValue('E' . $headerRow, 'Jumlah');
        $sheet->setCellValue('F' . $headerRow, 'Satuan');
        $sheet->setCellValue('G' . $headerRow, 'Harga Satuan');
        $sheet->setCellValue('H' . $headerRow, 'Total');
        $sheet->setCellValue('I' . $headerRow, 'User');
        
        // Style table header
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0891B2');
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Data
        $row = $headerRow + 1;
        $no = 1;
        foreach ($pengeluaran as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['kode']);
            $sheet->setCellValue('D' . $row, $item['keterangan']);
            $sheet->setCellValue('E' . $row, $item['jumlah']);
            $sheet->setCellValue('F' . $row, $item['satuan']);
            $sheet->setCellValue('G' . $row, $item['harga_satuan']);
            $sheet->setCellValue('H' . $row, $item['total']);
            $sheet->setCellValue('I' . $row, $item['nama_user']);
            
            // Number format
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            // Alignment
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
        }
        
        // Total
        if (!empty($pengeluaran)) {
            $sheet->setCellValue('G' . $row, 'TOTAL:');
            $sheet->setCellValue('H' . $row, $totalPengeluaran);
            $sheet->getStyle('G' . $row . ':H' . $row)->getFont()->setBold(true);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row . ':H' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0F2FE');
        }
        
        // Border untuk tabel
        $lastRow = $row;
        $sheet->getStyle('A' . $headerRow . ':I' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Output
        $filename = 'Laporan_Pengeluaran_' . $bulanNama[$bulan] . '_' . $tahun . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    // ========================================
    // SALDO OPERASIONAL - CRUD
    // ========================================
    
    public function storeSaldo()
    {
        // Validasi
        if (!$this->validate([
            'tanggal_masuk' => 'required|valid_date',
            'keterangan' => 'required|min_length[3]',
            'nominal_masuk' => 'required|decimal'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $nominal = (float) $this->request->getPost('nominal_masuk');
        
        $data = [
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'keterangan' => $this->request->getPost('keterangan'),
            'nominal_masuk' => $nominal,
            'saldo_tersisa' => $nominal
        ];
        
        $this->operasionalModel->insertSaldo($data);
        
        return redirect()->to('/admin/operasional')->with('success', 'Saldo operasional berhasil ditambahkan');
    }
    
    public function deleteSaldo($id)
    {
        $saldo = $this->operasionalModel->getSaldoById($id);
        
        if (!$saldo) {
            return redirect()->to('/admin/operasional')->with('error', 'Data saldo tidak ditemukan');
        }
        
        // Cek apakah saldo sudah terpakai
        if ($saldo['saldo_tersisa'] != $saldo['nominal_masuk']) {
            return redirect()->to('/admin/operasional')->with('error', 'Saldo tidak dapat dihapus karena sudah terpakai');
        }
        
        $this->operasionalModel->deleteSaldo($id);
        
        return redirect()->to('/admin/operasional')->with('success', 'Saldo operasional berhasil dihapus');
    }

    public function update($id)
{
    $pengeluaran = $this->operasionalModel->find($id);
    
    if (!$pengeluaran) {
        return redirect()->to('/admin/operasional')->with('error', 'Data pengeluaran tidak ditemukan');
    }
    
    // Validasi
    if (!$this->validate([
        'tanggal' => 'required|valid_date',
        'kode' => 'permit_empty|max_length[50]',
        'keterangan' => 'required|min_length[3]',
        'jumlah' => 'required|decimal',
        'satuan' => 'permit_empty|max_length[50]',
        'harga_satuan' => 'required|decimal'
    ])) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    $jumlah = (float) $this->request->getPost('jumlah');
    $hargaSatuan = (float) $this->request->getPost('harga_satuan');
    $totalBaru = $jumlah * $hargaSatuan;
    $totalLama = $pengeluaran['total'];
    $selisih = $totalBaru - $totalLama;
    
    // Cek saldo jika pengeluaran bertambah
    if ($selisih > 0) {
        $totalSaldo = $this->operasionalModel->getTotalSaldoTersisa();
        if ($totalSaldo < $selisih) {
            return redirect()->back()->withInput()->with('error', 'Saldo operasional tidak mencukupi! Saldo tersisa: Rp ' . number_format($totalSaldo, 0, ',', '.'));
        }
    }
    
    // Start transaction
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        // Update pengeluaran
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'kode' => $this->request->getPost('kode') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'jumlah' => $jumlah,
            'satuan' => $this->request->getPost('satuan') ?: 'unit',
            'harga_satuan' => $hargaSatuan,
            'total' => $totalBaru
        ];
        
        $this->operasionalModel->update($id, $data);
        
        // Sesuaikan saldo
        if ($selisih > 0) {
            // Pengeluaran bertambah, kurangi saldo
            $this->operasionalModel->kurangiSaldo($selisih);
        } elseif ($selisih < 0) {
            // Pengeluaran berkurang, tambah saldo
            $this->operasionalModel->tambahSaldo(abs($selisih));
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            throw new \Exception('Gagal mengupdate pengeluaran');
        }
        
        return redirect()->to('/admin/operasional')->with('success', 'Pengeluaran berhasil diupdate');
        
    } catch (\Exception $e) {
        $db->transRollback();
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}
}