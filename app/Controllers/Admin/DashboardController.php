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
    
    /**
     * Urutan bulan tahun ajaran (Juli = awal, Juni = akhir) -- dipakai untuk
     * menentukan "SPP bulan berjalan" vs "SPP bulan-bulan sebelumnya".
     */
    private function bulanAkademik(): array
    {
        return ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
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
                'bulan_berjalan' => null, 'status_per_kelas' => [],
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
        
        // ============= Status Pembayaran per Kelas (Daftar Ulang & SPP) =============
        $bulanList = $this->bulanAkademik();
        $n = (int) date('n');
        $namaBulanSekarang = $bulanList[$n >= 7 ? $n - 7 : $n + 5];
        $indexBulanSekarang = array_search($namaBulanSekarang, $bulanList);
        $bulanSebelumnyaList = array_slice($bulanList, 0, $indexBulanSekarang); // kosong kalau bulan ini = Juli
        $spBulanIniNama = 'SPP ' . mb_strtoupper($namaBulanSekarang);
        $spSebelumnyaNama = array_map(fn ($b) => 'SPP ' . mb_strtoupper($b), $bulanSebelumnyaList);
        
        $kelasList = $this->kelasModel->where('id_tahun_ajaran', $idTA)->orderBy('nama_kelas', 'ASC')->findAll();
        $statusPerKelas = [];
        foreach ($kelasList as $k) {
            $totalSiswaKelas = $this->siswaModel->where('id_kelas', $k['id_kelas'])->where('status_siswa', 'aktif')->countAllResults();
            if ($totalSiswaKelas === 0) continue; // lewati kelas yang belum ada siswanya
            
            // Daftar siswa yang PUNYA tunggakan di kelas ini, dipecah 3 kategori:
            // Daftar Ulang, SPP bulan berjalan, SPP bulan-bulan sebelumnya (bisa lebih dari 1 bulan).
            $sppSebelumnyaCase = empty($spSebelumnyaNama)
                ? '0'
                : 'SUM(CASE WHEN jt.nama_tagihan IN (' . implode(',', array_map(fn ($x) => $db->escape($x), $spSebelumnyaNama)) . ") AND t.status_tagihan != 'lunas' THEN 1 ELSE 0 END)";
            
            $sppSebelumnyaInList = empty($spSebelumnyaNama)
                ? "''"
                : implode(',', array_map(fn ($x) => $db->escape($x), $spSebelumnyaNama));
            
            $siswaTunggakan = $db->table('siswa s')
                ->select("s.id_siswa, s.nama_lengkap, s.nis,
                          SUM(CASE WHEN jt.grup_tagihan='DAFTAR ULANG' AND jt.nama_tagihan != 'SPP JULI' AND t.status_tagihan != 'lunas' THEN 1 ELSE 0 END) as jml_du,
                          SUM(CASE WHEN jt.nama_tagihan=" . $db->escape($spBulanIniNama) . " AND t.status_tagihan != 'lunas' THEN 1 ELSE 0 END) as belum_bulan_ini,
                          {$sppSebelumnyaCase} as jml_sebelumnya,
                          SUM(CASE WHEN t.status_tagihan != 'lunas' AND (
                                (jt.grup_tagihan='DAFTAR ULANG' AND jt.nama_tagihan != 'SPP JULI')
                                OR jt.nama_tagihan=" . $db->escape($spBulanIniNama) . "
                                OR jt.nama_tagihan IN ({$sppSebelumnyaInList})
                              ) THEN t.sisa_tagihan ELSE 0 END) as total_rp", false)
                ->join('tagihan t', 't.id_siswa = s.id_siswa')
                ->join('jenis_tagihan jt', 'jt.id_jenis_tagihan = t.id_jenis_tagihan')
                ->where('s.id_kelas', $k['id_kelas'])
                ->where('s.status_siswa', 'aktif')
                ->where('t.status_tagihan !=', 'lunas')
                ->groupStart()
                    ->where('jt.grup_tagihan', 'DAFTAR ULANG')
                    ->orLike('jt.nama_tagihan', 'SPP ', 'after')
                ->groupEnd()
                ->groupBy('s.id_siswa')
                ->having('jml_du >', 0)
                ->orHaving('belum_bulan_ini >', 0)
                ->orHaving('jml_sebelumnya >', 0)
                ->orderBy('s.nama_lengkap', 'ASC')
                ->get()->getResultArray();
            
            $siswaLunasSemua = max(0, $totalSiswaKelas - count($siswaTunggakan));
            
            // Untuk siswa yang muncul di daftar tunggakan ini, cek juga apakah mereka
            // punya tunggakan dari tahun ajaran LAIN (bukan yang sedang aktif) -- misalnya
            // siswa naik kelas tapi masih ada sisa tagihan dari tahun sebelumnya.
            if (!empty($siswaTunggakan)) {
                $idSiswaList = array_column($siswaTunggakan, 'id_siswa');
                $tunggakanLaluRows = $db->table('tagihan')
                    ->select('id_siswa, SUM(sisa_tagihan) as total_lalu', false)
                    ->whereIn('id_siswa', $idSiswaList)
                    ->where('id_tahun_ajaran !=', $idTA)
                    ->where('status_tagihan !=', 'lunas')
                    ->groupBy('id_siswa')
                    ->get()->getResultArray();
                $tunggakanLaluMap = array_column($tunggakanLaluRows, 'total_lalu', 'id_siswa');
                
                foreach ($siswaTunggakan as &$s) {
                    $s['tunggakan_lalu'] = (float) ($tunggakanLaluMap[$s['id_siswa']] ?? 0);
                }
                unset($s);
            }
            
            $statusPerKelas[] = [
                'id_kelas' => $k['id_kelas'],
                'nama_kelas' => $k['nama_kelas'],
                'total_siswa' => $totalSiswaKelas,
                'lunas_semua' => $siswaLunasSemua,
                'persen_lunas' => round($siswaLunasSemua / $totalSiswaKelas * 100),
                'siswa_tunggakan' => $siswaTunggakan,
            ];
        }
        
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
            'pembayaran_terbaru' => $pembayaranTerbaru,
            'bulan_berjalan' => $namaBulanSekarang,
            'status_per_kelas' => $statusPerKelas,
        ];
        
        return view('admin/dashboard/index', $data);
    }
    
    /**
     * AJAX: daftar nama siswa yang belum lunas di 1 kelas, dipecah per kategori
     * (Daftar Ulang / SPP bulan berjalan / SPP bulan sebelumnya). Dipanggil saat
     * baris kelas di-klik/expand di dashboard.
     */
    public function kelasDetail($idKelas)
    {
        $db = \Config\Database::connect();
        $bulanList = $this->bulanAkademik();
        $namaBulanSekarang = $bulanList[(int) date('n') >= 7 ? (int) date('n') - 7 : (int) date('n') + 5];
        $indexBulanSekarang = array_search($namaBulanSekarang, $bulanList);
        $bulanSebelumnyaList = array_slice($bulanList, 0, $indexBulanSekarang);
        $spBulanIniNama = 'SPP ' . mb_strtoupper($namaBulanSekarang);
        $spSebelumnyaNama = array_map(fn ($b) => 'SPP ' . mb_strtoupper($b), $bulanSebelumnyaList);
        
        $ambilNama = function (callable $applyWhere) use ($db, $idKelas) {
            $q = $db->table('tagihan t')
                ->select('DISTINCT s.id_siswa, s.nama_lengkap, s.nis')
                ->join('siswa s', 's.id_siswa = t.id_siswa')
                ->join('jenis_tagihan jt', 'jt.id_jenis_tagihan = t.id_jenis_tagihan')
                ->where('t.id_kelas', $idKelas)
                ->where('t.status_tagihan !=', 'lunas');
            $applyWhere($q);
            return $q->orderBy('s.nama_lengkap', 'ASC')->get()->getResultArray();
        };
        
        $daftarUlang = $ambilNama(function ($q) {
            $q->where('jt.grup_tagihan', 'DAFTAR ULANG')->where('jt.nama_tagihan !=', 'SPP JULI');
        });
        $sppBulanIni = $ambilNama(function ($q) use ($spBulanIniNama) {
            $q->where('jt.nama_tagihan', $spBulanIniNama);
        });
        $sppSebelumnya = empty($spSebelumnyaNama) ? [] : $ambilNama(function ($q) use ($spSebelumnyaNama) {
            $q->whereIn('jt.nama_tagihan', $spSebelumnyaNama);
        });
        
        return $this->response->setJSON([
            'daftar_ulang' => $daftarUlang,
            'spp_bulan_ini' => $sppBulanIni,
            'spp_sebelumnya' => $sppSebelumnya,
            'bulan_berjalan' => $namaBulanSekarang,
        ]);
    }
}