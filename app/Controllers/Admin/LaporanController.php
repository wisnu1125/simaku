<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\TagihanModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanController extends BaseController
{
    protected $pembayaranModel;
    protected $tagihanModel;
    protected $siswaModel;
    protected $tahunAjaranModel;
    protected $kelasModel;
    
    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->tagihanModel = new TagihanModel();
        $this->siswaModel = new SiswaModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->kelasModel = new KelasModel();
    }
    
    /**
     * Index laporan
     */
    public function index()
    {
        $data = [
            'title' => 'Laporan',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran()
        ];
        
        return view('admin/laporan/index', $data);
    }
    
    /**
     * Laporan Pembayaran
     */
    public function pembayaran()
    {
        $tanggalMulai = $this->request->getGet('tanggal_mulai') ?: date('Y-m-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?: date('Y-m-d');
        $idTahunAjaran = $this->request->getGet('id_tahun_ajaran');
        
        $builder = $this->pembayaranModel
                        ->select('pembayaran.*, 
                                 siswa.nis, 
                                 siswa.nama_lengkap as nama_siswa,
                                 kelas.nama_kelas,
                                 jenis_tagihan.nama_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran,
                                 users.nama_lengkap as nama_petugas')
                        ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                        ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                        ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                        ->join('users', 'users.id_user = pembayaran.id_user', 'left')
                        ->where('pembayaran.status_pembayaran', 'valid')
                        ->where('DATE(pembayaran.tanggal_bayar) >=', $tanggalMulai)
                        ->where('DATE(pembayaran.tanggal_bayar) <=', $tanggalSelesai);
        
        if ($idTahunAjaran) {
            $builder->where('tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        $pembayaran = $builder->orderBy('pembayaran.tanggal_bayar', 'DESC')->findAll();
        
        // Calculate totals
        $totalPembayaran = array_sum(array_column($pembayaran, 'nominal_bayar'));
        $totalTunai = array_sum(array_map(function($p) {
            return $p['metode_pembayaran'] === 'tunai' ? $p['nominal_bayar'] : 0;
        }, $pembayaran));
        $totalTransfer = array_sum(array_map(function($p) {
            return $p['metode_pembayaran'] === 'transfer' ? $p['nominal_bayar'] : 0;
        }, $pembayaran));
        
        $data = [
            'title' => 'Laporan Pembayaran',
            'pembayaran' => $pembayaran,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'id_tahun_ajaran' => $idTahunAjaran,
            'total_pembayaran' => $totalPembayaran,
            'total_tunai' => $totalTunai,
            'total_transfer' => $totalTransfer,
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll()
        ];
        
        return view('admin/laporan/pembayaran', $data);
    }
    
    /**
     * Laporan Tunggakan
     */
    public function tunggakan()
    {
        $idTahunAjaran = $this->request->getGet('id_tahun_ajaran');
        $idKelas = $this->request->getGet('id_kelas');
        
        $builder = $this->tagihanModel
                        ->select('tagihan.*, 
                                 siswa.nis, 
                                 siswa.nama_lengkap as nama_siswa,
                                 siswa.telp_wali,
                                 kelas.nama_kelas,
                                 jenis_tagihan.nama_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran')
                        ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                        ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                        ->where('tagihan.status_tagihan !=', 'lunas')
                        ->where('tagihan.sisa_tagihan >', 0);
        
        if ($idTahunAjaran) {
            $builder->where('tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        if ($idKelas) {
            $builder->where('tagihan.id_kelas', $idKelas);
        }
        
        $tunggakan = $builder->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                            ->orderBy('siswa.nama_lengkap', 'ASC')
                            ->findAll();
        
        // Group by siswa
        $tunggakanBySiswa = [];
        foreach ($tunggakan as $t) {
            $idSiswa = $t['id_siswa'];
            if (!isset($tunggakanBySiswa[$idSiswa])) {
                $tunggakanBySiswa[$idSiswa] = [
                    'nis' => $t['nis'],
                    'nama_siswa' => $t['nama_siswa'],
                    'nama_kelas' => $t['nama_kelas'],
                    'telp_wali' => $t['telp_wali'],
                    'tagihan' => [],
                    'total_tunggakan' => 0
                ];
            }
            $tunggakanBySiswa[$idSiswa]['tagihan'][] = $t;
            $tunggakanBySiswa[$idSiswa]['total_tunggakan'] += $t['sisa_tagihan'];
        }
        
        // Sort by total tunggakan DESC
        usort($tunggakanBySiswa, function($a, $b) {
            return $b['total_tunggakan'] - $a['total_tunggakan'];
        });
        
        $totalTunggakan = array_sum(array_column($tunggakanBySiswa, 'total_tunggakan'));
        
        $data = [
            'title' => 'Laporan Tunggakan',
            'tunggakan_by_siswa' => $tunggakanBySiswa,
            'total_tunggakan' => $totalTunggakan,
            'id_tahun_ajaran' => $idTahunAjaran,
            'id_kelas' => $idKelas,
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran()
        ];
        
        return view('admin/laporan/tunggakan', $data);
    }
    
    /**
     * Laporan Per Kelas
     */
    /**
 * Laporan Per Kelas
 */
public function perKelas()
{
    $idKelas       = $this->request->getGet('kelas_id');
    $idTahunAjaran = $this->request->getGet('tahun_ajaran_id');
    $tanggalAkhir  = $this->request->getGet('tanggal_akhir');
 
    $data = [
        'title'         => 'Laporan Per Kelas',
        'tahun_ajaran'  => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
        'kelas'         => $this->kelasModel->getKelasWithTahunAjaran(),
        'tanggal_akhir' => $tanggalAkhir,
    ];
 
    if ($idKelas && $idTahunAjaran) {
        $kelas = $this->kelasModel
                      ->select('kelas.*, tahun_ajaran.nama_tahun_ajaran')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'left')
                      ->where('kelas.id_kelas', $idKelas)
                      ->first();
 
        if (!$kelas) {
            return redirect()->to(base_url('admin/laporan/per-kelas'))->with('error', 'Kelas tidak ditemukan');
        }
 
        $siswa = $this->siswaModel
                      ->where('id_kelas', $idKelas)
                      ->where('status_siswa', 'aktif')
                      ->orderBy('nama_lengkap', 'ASC')
                      ->findAll();
 
        $jenisTagihan = $this->tagihanModel
                             ->select('jenis_tagihan.id_jenis_tagihan, jenis_tagihan.nama_tagihan')
                             ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan')
                             ->where('tagihan.id_tahun_ajaran', $idTahunAjaran)
                             ->where('tagihan.id_kelas', $idKelas)
                             ->groupBy('jenis_tagihan.id_jenis_tagihan')
                             ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                             ->findAll();
 
        $db = \Config\Database::connect();
 
        $laporanSiswa   = [];
        $totalTagihan   = 0;
        $totalDibayar   = 0;
        $totalTunggakan = 0;
 
        foreach ($siswa as $s) {
            // Ambil semua tagihan siswa di kelas & tahun ajaran ini
            $tagihanList = $this->tagihanModel
                               ->select('id_tagihan, id_jenis_tagihan, nominal_akhir')
                               ->where('id_siswa', $s['id_siswa'])
                               ->where('id_tahun_ajaran', $idTahunAjaran)
                               ->findAll();
 
            $tagihanMap = [];
 
            foreach ($tagihanList as $t) {
                // Hitung nominal dibayar dari tabel pembayaran, dengan cut-off tanggal jika ada
                $pbBuilder = $db->table('pembayaran')
                                ->selectSum('nominal_bayar')
                                ->where('id_tagihan', $t['id_tagihan'])
                                ->where('status_pembayaran', 'valid');
 
                if ($tanggalAkhir) {
                    $pbBuilder->where('DATE(tanggal_bayar) <=', $tanggalAkhir);
                }
 
                $nominalDibayar = (int) ($pbBuilder->get()->getRow()->nominal_bayar ?? 0);
 
                $idJenis = $t['id_jenis_tagihan'];
 
                if (!isset($tagihanMap[$idJenis])) {
                    $tagihanMap[$idJenis] = [
                        'nominal_akhir'   => 0,
                        'nominal_dibayar' => 0,
                        'sisa_tagihan'    => 0,
                    ];
                }
 
                $tagihanMap[$idJenis]['nominal_akhir']   += (int) $t['nominal_akhir'];
                $tagihanMap[$idJenis]['nominal_dibayar'] += $nominalDibayar;
            }
 
            // Hitung sisa per jenis setelah semua tagihan dijumlah
            foreach ($tagihanMap as $idJenis => &$tm) {
                $tm['sisa_tagihan'] = max(0, $tm['nominal_akhir'] - $tm['nominal_dibayar']);
            }
            unset($tm);
 
            $siswaTagihan   = array_sum(array_column($tagihanMap, 'nominal_akhir'));
            $siswaDibayar   = array_sum(array_column($tagihanMap, 'nominal_dibayar'));
            $siswaTunggakan = array_sum(array_column($tagihanMap, 'sisa_tagihan'));
 
            $laporanSiswa[] = [
                'nis'             => $s['nis'],
                'nama_lengkap'    => $s['nama_lengkap'],
                'tagihan_detail'  => $tagihanMap,
                'total_tagihan'   => $siswaTagihan,
                'total_dibayar'   => $siswaDibayar,
                'total_tunggakan' => $siswaTunggakan,
            ];
 
            $totalTagihan   += $siswaTagihan;
            $totalDibayar   += $siswaDibayar;
            $totalTunggakan += $siswaTunggakan;
        }
 
        $data['kelas_info']      = $kelas;
        $data['siswa']           = $laporanSiswa;
        $data['jenis_tagihan']   = $jenisTagihan;
        $data['kelas_id']        = $idKelas;
        $data['tahun_ajaran_id'] = $idTahunAjaran;
        $data['total_tagihan']   = $totalTagihan;
        $data['total_dibayar']   = $totalDibayar;
        $data['total_tunggakan'] = $totalTunggakan;
    }
 
    return view('admin/laporan/per_kelas', $data);
}
    
    /**
     * Export Excel - Pembayaran
     */
    public function exportPembayaran()
    {
        $tanggalMulai = $this->request->getGet('tanggal_mulai') ?: date('Y-m-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?: date('Y-m-d');
        
        $pembayaran = $this->pembayaranModel
                           ->select('pembayaran.*, 
                                    siswa.nis, 
                                    siswa.nama_lengkap as nama_siswa,
                                    kelas.nama_kelas,
                                    jenis_tagihan.nama_tagihan,
                                    tahun_ajaran.nama_tahun_ajaran')
                           ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                           ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                           ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                           ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                           ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                           ->where('pembayaran.status_pembayaran', 'valid')
                           ->where('DATE(pembayaran.tanggal_bayar) >=', $tanggalMulai)
                           ->where('DATE(pembayaran.tanggal_bayar) <=', $tanggalSelesai)
                           ->orderBy('pembayaran.tanggal_bayar', 'DESC')
                           ->findAll();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'LAPORAN PEMBAYARAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($tanggalMulai)) . ' - ' . date('d/m/Y', strtotime($tanggalSelesai)));
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        
        // Style header
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Column headers
        $headers = ['No', 'Tanggal', 'No. Kwitansi', 'NIS', 'Nama Siswa', 'Tagihan', 'Nominal', 'Metode'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        
        // Style column headers
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB('14b8a6');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Data
        $row = 5;
        $no = 1;
        $total = 0;
        foreach ($pembayaran as $p) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($p['tanggal_bayar'])));
            $sheet->setCellValue('C' . $row, $p['nomor_kwitansi']);
            $sheet->setCellValue('D' . $row, $p['nis']);
            $sheet->setCellValue('E' . $row, $p['nama_siswa']);
            $sheet->setCellValue('F' . $row, $p['nama_tagihan']);
            $sheet->setCellValue('G' . $row, $p['nominal_bayar']);
            $sheet->setCellValue('H' . $row, strtoupper($p['metode_pembayaran']));
            
            $total += $p['nominal_bayar'];
            $row++;
        }
        
        // Total
        $sheet->setCellValue('F' . $row, 'TOTAL');
        $sheet->setCellValue('G' . $row, $total);
        $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
        
        // Format currency
        $sheet->getStyle('G5:G' . $row)->getNumberFormat()
              ->setFormatCode('#,##0');
        
        // Auto width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Borders
        $sheet->getStyle('A4:H' . $row)->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);
        
        // Download
        $filename = 'Laporan_Pembayaran_' . date('Ymd') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Export Excel - Tunggakan
     */
    public function exportTunggakan()
    {
        $idTahunAjaran = $this->request->getGet('id_tahun_ajaran');
        
        $builder = $this->tagihanModel
                        ->select('tagihan.*, 
                                 siswa.nis, 
                                 siswa.nama_lengkap as nama_siswa,
                                 siswa.telp_wali,
                                 kelas.nama_kelas,
                                 jenis_tagihan.nama_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran')
                        ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                        ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                        ->where('tagihan.status_tagihan !=', 'lunas')
                        ->where('tagihan.sisa_tagihan >', 0);
        
        if ($idTahunAjaran) {
            $builder->where('tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        $tunggakan = $builder->orderBy('siswa.nama_lengkap', 'ASC')->findAll();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'LAPORAN TUNGGAKAN');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Column headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Telp Wali', 'Tagihan', 'Total', 'Dibayar', 'Tunggakan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        
        $sheet->getStyle('A3:I3')->getFont()->setBold(true);
        $sheet->getStyle('A3:I3')->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB('ef4444');
        $sheet->getStyle('A3:I3')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Data
        $row = 4;
        $no = 1;
        $grandTotal = 0;
        foreach ($tunggakan as $t) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $t['nis']);
            $sheet->setCellValue('C' . $row, $t['nama_siswa']);
            $sheet->setCellValue('D' . $row, $t['nama_kelas']);
            $sheet->setCellValue('E' . $row, $t['telp_wali']);
            $sheet->setCellValue('F' . $row, $t['nama_tagihan']);
            $sheet->setCellValue('G' . $row, $t['nominal_akhir']);
            $sheet->setCellValue('H' . $row, $t['nominal_dibayar']);
            $sheet->setCellValue('I' . $row, $t['sisa_tagihan']);
            
            $grandTotal += $t['sisa_tagihan'];
            $row++;
        }
        
        // Total
        $sheet->setCellValue('H' . $row, 'TOTAL TUNGGAKAN');
        $sheet->setCellValue('I' . $row, $grandTotal);
        $sheet->getStyle('H' . $row . ':I' . $row)->getFont()->setBold(true);
        
        // Format
        $sheet->getStyle('G4:I' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Borders
        $sheet->getStyle('A3:I' . $row)->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);
        
        // Download
        $filename = 'Laporan_Tunggakan_' . date('Ymd') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
 * Export Excel - Per Kelas
 */
/**
 * Export Excel - Per Kelas
 */
/**
 * Export Excel - Per Kelas
 */
/**
 * Export Excel - Per Kelas
 */
public function exportPerKelas()
{
    $idKelas       = $this->request->getGet('kelas_id');
    $idTahunAjaran = $this->request->getGet('tahun_ajaran_id');
    $tanggalAkhir  = $this->request->getGet('tanggal_akhir');
 
    if (!$idKelas || !$idTahunAjaran) {
        return redirect()->back()->with('error', 'Parameter tidak lengkap');
    }
 
    // 1. Info kelas & tahun ajaran
    $kelas = $this->kelasModel
                  ->select('kelas.*, tahun_ajaran.nama_tahun_ajaran')
                  ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran')
                  ->where('kelas.id_kelas', $idKelas)
                  ->first();
 
    if (!$kelas) {
        return redirect()->back()->with('error', 'Kelas tidak ditemukan');
    }
 
    // 2. Jenis tagihan — urut non-SPP dulu, SPP urut Juli-Juni
    $jenisTagihan = $this->tagihanModel
                         ->select('jenis_tagihan.id_jenis_tagihan, jenis_tagihan.nama_tagihan')
                         ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan')
                         ->where('tagihan.id_tahun_ajaran', $idTahunAjaran)
                         ->where('tagihan.id_kelas', $idKelas)
                         ->groupBy('jenis_tagihan.id_jenis_tagihan')
                         ->orderBy("CASE WHEN jenis_tagihan.nama_tagihan NOT LIKE '%SPP%' THEN 0 ELSE 1 END", "ASC", false)
                         ->orderBy("CASE 
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Juli%'     THEN 1
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Agustus%'  THEN 2
                            WHEN jenis_tagihan.nama_tagihan LIKE '%September%' THEN 3
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Oktober%'  THEN 4
                            WHEN jenis_tagihan.nama_tagihan LIKE '%November%' THEN 5
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Desember%' THEN 6
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Januari%'  THEN 7
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Februari%' THEN 8
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Maret%'    THEN 9
                            WHEN jenis_tagihan.nama_tagihan LIKE '%April%'    THEN 10
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Mei%'      THEN 11
                            WHEN jenis_tagihan.nama_tagihan LIKE '%Juni%'     THEN 12
                            ELSE 99 END", "ASC", false)
                         ->findAll();
 
    // 3. Siswa aktif di kelas ini
    $siswa = $this->siswaModel
                  ->where('id_kelas', $idKelas)
                  ->where('status_siswa', 'aktif')
                  ->orderBy('nama_lengkap', 'ASC')
                  ->findAll();
 
    $db = \Config\Database::connect();
 
    // ── SPREADSHEET ──────────────────────────────────────────
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Rekap Pembayaran');
 
    $judulPeriode = $tanggalAkhir
        ? 'Per Tanggal: ' . date('d/m/Y', strtotime($tanggalAkhir))
        : 'Per Tanggal: Semua Waktu';
 
    // Baris judul (1-3)
    $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PEMBAYARAN SISWA');
    $sheet->setCellValue('A2', 'KELAS: ' . strtoupper($kelas['nama_kelas']) . '  |  TAHUN AJARAN: ' . $kelas['nama_tahun_ajaran']);
    $sheet->setCellValue('A3', $judulPeriode);
    $sheet->getStyle('A1:A3')->getFont()->setBold(true);
    $sheet->getStyle('A1')->getFont()->setSize(13);
    $sheet->getStyle('A2:A3')->getFont()->setSize(11);
 
    // Header tabel — baris 5
    $sheet->setCellValue('A5', 'NO');
    $sheet->setCellValue('B5', 'NIS');
    $sheet->setCellValue('C5', 'NAMA SISWA');
 
    $col = 'D';
    foreach ($jenisTagihan as $jt) {
        $sheet->setCellValue($col . '5', strtoupper($jt['nama_tagihan']));
        $col++;
    }
    $sheet->setCellValue($col . '5', 'TOTAL DIBAYAR');
    $lastCol = $col;
 
    // Style header tabel
    $headerRange = 'A5:' . $lastCol . '5';
    $sheet->getStyle($headerRange)->applyFromArray([
        'font' => [
            'bold'  => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size'  => 11,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0891B2'],
        ],
    ]);
    $sheet->getRowDimension('5')->setRowHeight(28);
 
    // ── DATA SISWA (mulai baris 6) ────────────────────────────
    $row = 6;
    $no  = 1;
 
    foreach ($siswa as $s) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $s['nis']);
        $sheet->setCellValue('C' . $row, $s['nama_lengkap']);
 
        // Ambil semua tagihan siswa di tahun ajaran ini
        $tagihanList = $this->tagihanModel
                           ->select('id_tagihan, id_jenis_tagihan, nominal_akhir')
                           ->where('id_siswa', $s['id_siswa'])
                           ->where('id_tahun_ajaran', $idTahunAjaran)
                           ->findAll();
 
        // Hitung nominal dibayar per jenis tagihan
        $dibayarPerJenis = [];
        foreach ($tagihanList as $t) {
            $pbBuilder = $db->table('pembayaran')
                            ->selectSum('nominal_bayar')
                            ->where('id_tagihan', $t['id_tagihan'])
                            ->where('status_pembayaran', 'valid');
 
            if ($tanggalAkhir) {
                $pbBuilder->where('DATE(tanggal_bayar) <=', $tanggalAkhir);
            }
 
            $nominal = (int) ($pbBuilder->get()->getRow()->nominal_bayar ?? 0);
            $idJenis = $t['id_jenis_tagihan'];
 
            $dibayarPerJenis[$idJenis] = ($dibayarPerJenis[$idJenis] ?? 0) + $nominal;
        }
 
        $totalBayarSiswa = 0;
        $colData = 'D';
 
        foreach ($jenisTagihan as $jt) {
            $nominal = $dibayarPerJenis[$jt['id_jenis_tagihan']] ?? 0;
            $sheet->setCellValue($colData . $row, $nominal);
            $totalBayarSiswa += $nominal;
            $colData++;
        }
 
        $sheet->setCellValue($colData . $row, $totalBayarSiswa);
        $sheet->getStyle($colData . $row)->getFont()->setBold(true);
 
        // Zebra stripe
        if ($no % 2 === 0) {
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)
                  ->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('F8FAFC');
        }
 
        $row++;
    }
 
    // ── FOOTER TOTAL ──────────────────────────────────────────
    $lastDataRow = $row - 1;
 
    $sheet->setCellValue('A' . $row, 'TOTAL PEMASUKAN KELAS');
    $sheet->mergeCells('A' . $row . ':C' . $row);
 
    $colSum = 'D';
    for ($i = 0; $i <= count($jenisTagihan); $i++) {
        $sheet->setCellValue($colSum . $row, '=SUM(' . $colSum . '6:' . $colSum . $lastDataRow . ')');
        $colSum++;
    }
 
    // Style footer
    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
        'font' => [
            'bold'  => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0F172A'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ],
    ]);
    $sheet->getStyle('A' . $row)->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
 
    // ── FINAL STYLING ─────────────────────────────────────────
    // Border seluruh tabel
    $sheet->getStyle('A5:' . $lastCol . $row)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color'       => ['rgb' => 'E2E8F0'],
            ],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
 
    // Format angka
    $sheet->getStyle('D6:' . $lastCol . $row)
          ->getNumberFormat()
          ->setFormatCode('#,##0');
 
    // Alignment kolom NO & NIS
    $sheet->getStyle('A6:A' . $row)
          ->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B6:B' . $lastDataRow)
          ->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
 
    // Lebar kolom
    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setWidth(14);
    $sheet->getColumnDimension('C')->setAutoSize(true);
 
    $colDim = 'D';
    for ($i = 0; $i <= count($jenisTagihan); $i++) {
        $sheet->getColumnDimension($colDim)->setWidth(16);
        $colDim++;
    }
 
    // Merge judul agar rapi
    $totalCols = 3 + count($jenisTagihan) + 1; // NO+NIS+NAMA + jenis + total
    $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
    $sheet->mergeCells('A1:' . $lastColLetter . '1');
    $sheet->mergeCells('A2:' . $lastColLetter . '2');
    $sheet->mergeCells('A3:' . $lastColLetter . '3');
    $sheet->getStyle('A1:A3')->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
 
    // ── DOWNLOAD ──────────────────────────────────────────────
    $suffix   = $tanggalAkhir ? '_per_' . $tanggalAkhir : '';
    $filename = 'LAPORAN_PEMBAYARAN_'
              . str_replace(' ', '_', strtoupper($kelas['nama_kelas']))
              . $suffix
              . '.xlsx';
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
 
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
}