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
        // Get tahun ajaran aktif -- SEMUA statistik di bawah ini disaring ke tahun ajaran
        // ini saja, supaya dashboard tidak mencampur data tahun-tahun lama.
        $tahunAjaranAktif = $this->tahunAjaranModel->where('status', 'aktif')->first();
        $idTA = $tahunAjaranAktif['id_tahun_ajaran'] ?? null;
        
        $db = \Config\Database::connect();
        
        if (!$idTA) {
            // Tidak ada tahun ajaran aktif sama sekali -- tampilkan kosong daripada
            // sepanjang waktu (supaya tidak menyesatkan, biar jelas belum ada TA aktif).
            $data = [
                'title' => 'Dashboard', 'tahun_ajaran_aktif' => null,
                'total_siswa' => 0, 'total_kelas' => 0, 'total_dibayar' => 0, 'total_tunggakan' => 0,
                'pembayaran_hari_ini' => 0, 'chart_pembayaran' => [],
                'status_tagihan' => ['lunas' => 0, 'cicil' => 0, 'belum_bayar' => 0],
                'top_tunggakan' => [], 'pembayaran_terbaru' => [],
            ];
            return view('admin/dashboard/index', $data);
        }
        
        // Total siswa aktif DI TAHUN AJARAN INI (lewat kelas)
        $totalSiswa = $this->siswaModel
                           ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
                           ->where('siswa.status_siswa', 'aktif')
                           ->where('kelas.id_tahun_ajaran', $idTA)
                           ->countAllResults();
        
        // Total kelas di tahun ajaran ini
        $totalKelas = $this->kelasModel->where('id_tahun_ajaran', $idTA)->countAllResults();
        
        // Total dibayar (tahun ajaran aktif saja)
        $totalDibayar = $db->table('tagihan')
                          ->selectSum('nominal_dibayar')
                          ->where('id_tahun_ajaran', $idTA)
                          ->get()
                          ->getRow()
                          ->nominal_dibayar ?? 0;
        
        // Total tunggakan (tahun ajaran aktif saja)
        $totalTunggakan = $db->table('tagihan')
                            ->selectSum('sisa_tagihan')
                            ->where('id_tahun_ajaran', $idTA)
                            ->where('status_tagihan !=', 'lunas')
                            ->get()
                            ->getRow()
                            ->sisa_tagihan ?? 0;
        
        // Pembayaran hari ini (untuk tagihan tahun ajaran aktif saja)
        $pembayaranHariIni = $db->table('pembayaran')
                               ->selectSum('pembayaran.nominal_bayar')
                               ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                               ->where('DATE(pembayaran.tanggal_bayar)', date('Y-m-d'))
                               ->where('pembayaran.status_pembayaran', 'valid')
                               ->where('tagihan.id_tahun_ajaran', $idTA)
                               ->get()
                               ->getRow()
                               ->nominal_bayar ?? 0;
        
        // Chart data - Pembayaran 7 hari terakhir (tagihan tahun ajaran aktif saja)
        $chartPembayaran = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $nominal = $db->table('pembayaran')
                         ->selectSum('pembayaran.nominal_bayar')
                         ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                         ->where('DATE(pembayaran.tanggal_bayar)', $date)
                         ->where('pembayaran.status_pembayaran', 'valid')
                         ->where('tagihan.id_tahun_ajaran', $idTA)
                         ->get()
                         ->getRow()
                         ->nominal_bayar ?? 0;
            
            $chartPembayaran[] = [
                'date' => date('d M', strtotime($date)),
                'nominal' => $nominal
            ];
        }
        
        // Status tagihan (tahun ajaran aktif saja)
        $statusTagihan = [
            'lunas' => $db->table('tagihan')->where('id_tahun_ajaran', $idTA)->where('status_tagihan', 'lunas')->countAllResults(),
            'cicil' => $db->table('tagihan')->where('id_tahun_ajaran', $idTA)->where('status_tagihan', 'cicil')->countAllResults(),
            'belum_bayar' => $db->table('tagihan')->where('id_tahun_ajaran', $idTA)->where('status_tagihan', 'belum_bayar')->countAllResults()
        ];
        
        // Top 5 siswa dengan tunggakan terbesar (tahun ajaran aktif saja)
        $topTunggakan = $db->table('tagihan')
                          ->select('siswa.nis, siswa.nama_lengkap, kelas.nama_kelas, SUM(tagihan.sisa_tagihan) as total_tunggakan')
                          ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                          ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                          ->where('tagihan.id_tahun_ajaran', $idTA)
                          ->where('tagihan.status_tagihan !=', 'lunas')
                          ->groupBy('tagihan.id_siswa')
                          ->orderBy('total_tunggakan', 'DESC')
                          ->limit(5)
                          ->get()
                          ->getResultArray();
        
        // Pembayaran terbaru (tagihan tahun ajaran aktif saja) -- daftar pendek, cuma
        // untuk konfirmasi cepat setelah input
        $pembayaranTerbaru = $this->pembayaranModel
                                  ->select('pembayaran.*, 
                                           siswa.nis, 
                                           siswa.nama_lengkap,
                                           jenis_tagihan.nama_tagihan')
                                  ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                                  ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                                  ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                                  ->where('pembayaran.status_pembayaran', 'valid')
                                  ->where('tagihan.id_tahun_ajaran', $idTA)
                                  ->orderBy('pembayaran.tanggal_bayar', 'DESC')
                                  ->limit(6)
                                  ->findAll();
        
        $data = [
            'title' => 'Dashboard',
            'tahun_ajaran_aktif' => $tahunAjaranAktif,
            'total_siswa' => $totalSiswa,
            'total_kelas' => $totalKelas,
            'total_dibayar' => $totalDibayar,
            'total_tunggakan' => $totalTunggakan,
            'pembayaran_hari_ini' => $pembayaranHariIni,
            'chart_pembayaran' => $chartPembayaran,
            'status_tagihan' => $statusTagihan,
            'top_tunggakan' => $topTunggakan,
            'pembayaran_terbaru' => $pembayaranTerbaru
        ];
        
        return view('admin/dashboard/index', $data);
    }
}