<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanModel;
use App\Models\PembayaranModel;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;

class DashboardController extends BaseController
{
    protected $siswaModel;
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $tahunAjaranModel;
    protected $kelasModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->tagihanModel = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->kelasModel = new KelasModel();
    }
    
    public function index()
    {
        // Get tahun ajaran aktif
        $tahunAjaranAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        
        // Total siswa
        $totalSiswa = $this->siswaModel->where('status_siswa', 'aktif')->countAllResults();
        
        // Total kelas
        $totalKelas = $this->kelasModel->countAllResults();
        
        // Statistics tagihan
        $db = \Config\Database::connect();
        
        // Total tagihan
        $totalTagihan = $db->table('tagihan')
                          ->selectSum('nominal_akhir')
                          ->get()
                          ->getRow()
                          ->nominal_akhir ?? 0;
        
        // Total dibayar
        $totalDibayar = $db->table('tagihan')
                          ->selectSum('nominal_dibayar')
                          ->get()
                          ->getRow()
                          ->nominal_dibayar ?? 0;
        
        // Total tunggakan
        $totalTunggakan = $db->table('tagihan')
                            ->selectSum('sisa_tagihan')
                            ->where('status_tagihan !=', 'lunas')
                            ->get()
                            ->getRow()
                            ->sisa_tagihan ?? 0;
        
        // Pembayaran hari ini
        $pembayaranHariIni = $db->table('pembayaran')
                               ->selectSum('nominal_bayar')
                               ->where('DATE(tanggal_bayar)', date('Y-m-d'))
                               ->where('status_pembayaran', 'valid')
                               ->get()
                               ->getRow()
                               ->nominal_bayar ?? 0;
        
        // Pembayaran bulan ini
        $pembayaranBulanIni = $db->table('pembayaran')
                                ->selectSum('nominal_bayar')
                                ->where('YEAR(tanggal_bayar)', date('Y'))
                                ->where('MONTH(tanggal_bayar)', date('m'))
                                ->where('status_pembayaran', 'valid')
                                ->get()
                                ->getRow()
                                ->nominal_bayar ?? 0;
        
        // Chart data - Pembayaran 7 hari terakhir
        $chartPembayaran = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $nominal = $db->table('pembayaran')
                         ->selectSum('nominal_bayar')
                         ->where('DATE(tanggal_bayar)', $date)
                         ->where('status_pembayaran', 'valid')
                         ->get()
                         ->getRow()
                         ->nominal_bayar ?? 0;
            
            $chartPembayaran[] = [
                'date' => date('d M', strtotime($date)),
                'nominal' => $nominal
            ];
        }
        
        // Status tagihan
        $statusTagihan = [
            'lunas' => $db->table('tagihan')->where('status_tagihan', 'lunas')->countAllResults(),
            'cicil' => $db->table('tagihan')->where('status_tagihan', 'cicil')->countAllResults(),
            'belum_bayar' => $db->table('tagihan')->where('status_tagihan', 'belum_bayar')->countAllResults()
        ];
        
        // Top 5 siswa dengan tunggakan terbesar
        $topTunggakan = $db->table('tagihan')
                          ->select('siswa.nis, siswa.nama_lengkap, kelas.nama_kelas, SUM(tagihan.sisa_tagihan) as total_tunggakan')
                          ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                          ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                          ->where('tagihan.status_tagihan !=', 'lunas')
                          ->groupBy('tagihan.id_siswa')
                          ->orderBy('total_tunggakan', 'DESC')
                          ->limit(5)
                          ->get()
                          ->getResultArray();
        
        // Pembayaran terbaru
        $pembayaranTerbaru = $this->pembayaranModel
                                  ->select('pembayaran.*, 
                                           siswa.nis, 
                                           siswa.nama_lengkap,
                                           jenis_tagihan.nama_tagihan')
                                  ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                                  ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                                  ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                                  ->where('pembayaran.status_pembayaran', 'valid')
                                  ->orderBy('pembayaran.tanggal_bayar', 'DESC')
                                  ->limit(10)
                                  ->findAll();
        
        $data = [
            'title' => 'Dashboard',
            'tahun_ajaran_aktif' => $tahunAjaranAktif,
            'total_siswa' => $totalSiswa,
            'total_kelas' => $totalKelas,
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'total_tunggakan' => $totalTunggakan,
            'pembayaran_hari_ini' => $pembayaranHariIni,
            'pembayaran_bulan_ini' => $pembayaranBulanIni,
            'chart_pembayaran' => $chartPembayaran,
            'status_tagihan' => $statusTagihan,
            'top_tunggakan' => $topTunggakan,
            'pembayaran_terbaru' => $pembayaranTerbaru
        ];
        
        return view('admin/dashboard/index', $data);
    }
}