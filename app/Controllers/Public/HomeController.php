<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanModel;
use App\Models\PembayaranModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class HomeController extends BaseController
{
    protected $siswaModel;
    protected $tagihanModel;
    protected $pembayaranModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->tagihanModel = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
    }
    
    /**
     * Landing page
     */
    public function index()
    {
        $data = [
            'title' => 'SIMAKU - Sistem Informasi Manajemen Keuangan Santri'
        ];
        
        return view('public/home', $data);
    }
    
    /**
     * Cek tagihan
     * Logic: Validasi -> Ambil Data -> Grouping Berdasarkan Tahun Ajaran -> Kirim ke View
     */
    public function cekTagihan()
    {
        $rules = [
            'nis' => 'required',
            'tanggal_lahir' => 'required|regex_match[/^\d{2}-\d{2}-\d{4}$/]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Format data tidak valid. Pastikan NIS dan Tanggal Lahir diisi dengan benar.');
        }
        
        $nis = $this->request->getPost('nis');
        $tanggalLahirInput = $this->request->getPost('tanggal_lahir'); // Format: DD-MM-YYYY
        
        // Konversi format DD-MM-YYYY ke YYYY-MM-DD untuk database
        $tanggalParts = explode('-', $tanggalLahirInput);
        
        // Validasi parts array
        if (count($tanggalParts) !== 3) {
            return redirect()->back()->withInput()->with('error', 'Format tanggal lahir tidak valid. Gunakan format: DD-MM-YYYY (contoh: 15-08-2010)');
        }
        
        $day = (int)$tanggalParts[0];
        $month = (int)$tanggalParts[1];
        $year = (int)$tanggalParts[2];
        
        // Validasi tanggal valid menggunakan checkdate
        if (!checkdate($month, $day, $year)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal lahir tidak valid. Pastikan tanggal, bulan, dan tahun benar.');
        }
        
        // Format untuk query database: YYYY-MM-DD
        $tanggalLahir = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
        
        // Cari siswa
        $siswa = $this->siswaModel
                      ->where('nis', $nis)
                      ->where('tanggal_lahir', $tanggalLahir)
                      ->first();
        
        if (!$siswa) {
            return redirect()->back()->withInput()->with('error', 'Data tidak ditemukan. Pastikan NIS dan Tanggal Lahir sudah benar.');
        }
        
        // Get siswa detail with kelas
        $siswaDetail = $this->siswaModel->getSiswaWithKelas($siswa['id_siswa']);
        
        // Get tagihan
        $tagihan = $this->tagihanModel->getTagihanBySiswa($siswa['id_siswa']);
        
        // Group by tahun ajaran
        // Kita mengelompokkan berdasarkan Tahun Ajaran agar View bisa me-looping per tahun.
        // Grouping kategori (Bulanan/Daftar Ulang) akan ditangani oleh Logic di dalam View (detail.php).
        $tagihanByTahun = [];
        foreach ($tagihan as $t) {
            $tahun = $t['nama_tahun_ajaran'];
            if (!isset($tagihanByTahun[$tahun])) {
                $tagihanByTahun[$tahun] = [];
            }
            $tagihanByTahun[$tahun][] = $t;
        }
        
        // Get history pembayaran (hanya yang valid)
        $pembayaran = $this->pembayaranModel
                            ->select('pembayaran.*, 
                                      jenis_tagihan.nama_tagihan,
                                      tahun_ajaran.nama_tahun_ajaran')
                            ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                            ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                            ->where('tagihan.id_siswa', $siswa['id_siswa'])
                            ->where('pembayaran.status_pembayaran', 'valid')
                            ->orderBy('pembayaran.tanggal_bayar', 'DESC')
                            ->findAll();
        
        // Get tahun ajaran aktif untuk link print
        $db = \Config\Database::connect();
        $tahunAjaranAktif = $db->table('tahun_ajaran')
                               ->where('status', 'aktif')
                               ->get()
                               ->getRowArray();
        
        $data = [
            'title' => 'Detail Tagihan - ' . $siswaDetail['nama_lengkap'],
            'siswa' => $siswaDetail,
            'tagihan_by_tahun' => $tagihanByTahun, // Data dikirim per tahun
            'pembayaran' => $pembayaran,
            'tahun_ajaran_aktif' => $tahunAjaranAktif['id_tahun_ajaran'] ?? null
        ];
        
        return view('public/detail', $data);
    }
    
    /**
     * Download kartu pembayaran PDF (Dompdf)
     * FIXED: Enable Remote & Chroot agar gambar muncul
     */
    public function printKartu($idSiswa, $idTahunAjaran)
    {
        // Get siswa detail
        $siswa = $this->siswaModel->getSiswaWithKelas($idSiswa);
        
        if (!$siswa) {
            return redirect()->to(base_url())->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get tagihan by tahun ajaran
        $tagihan = $this->tagihanModel->getTagihanBySiswa($idSiswa, $idTahunAjaran);
        
        // Get tahun ajaran
        $db = \Config\Database::connect();
        $tahunAjaran = $db->table('tahun_ajaran')
                           ->where('id_tahun_ajaran', $idTahunAjaran)
                           ->get()
                           ->getRowArray();
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url())->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        $data = [
            'siswa' => $siswa,
            'tagihan' => $tagihan,
            'tahun_ajaran' => $tahunAjaran
        ];
        
        // Generate HTML dari view
        $html = view('public/kartu_pdf', $data);
        
        // ============================================================
        // DOMPDF SETUP
        // ============================================================
        
        $options = new Options();
        
        // Opsi ini PENTING agar gambar (Logo/Foto) bisa dimuat di PDF
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $options->set('defaultFont', 'Arial');
        $options->set('isFontSubsettingEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('dpi', 96);
        $options->set('chroot', FCPATH); // Pastikan root folder benar
        
        // Disable debugging (production mode)
        $options->set('debugPng', false);
        $options->set('debugKeepTemp', false);
        $options->set('debugCss', false);
        $options->set('debugLayout', false);
        $options->set('debugLayoutLines', false);
        $options->set('debugLayoutBlocks', false);
        $options->set('debugLayoutInline', false);
        $options->set('debugLayoutPaddingBox', false);
        
        $dompdf = new Dompdf($options);
        
        // Load HTML
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Get PDF output
        $output = $dompdf->output();
        
        // Generate filename
        $filename = 'Kartu_Pembayaran_' . $siswa['nis'] . '_' . $tahunAjaran['nama_tahun_ajaran'] . '.pdf';
        
        // ============================================================
        // SET HEADERS - CRITICAL UNTUK SAFARI iOS!
        // ============================================================
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($output));
        header('Accept-Ranges: bytes');
        
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        echo $output;
        exit;
    }

    /**
     * Method untuk AJAX Search Siswa (Halaman Home)
     * Mengembalikan JSON berisi Nama, NIS (Hidden), dan Kelas
     */
    public function searchSiswa()
    {
        $keyword = trim($this->request->getVar('keyword') ?? '');

        if (empty($keyword)) {
            return $this->response->setJSON([]);
        }

        $results = $this->siswaModel->asArray()
                                    ->select('siswa.nis, siswa.nama_lengkap, kelas.nama_kelas')
                                    ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
                                    ->groupStart() 
                                        ->like('siswa.nama_lengkap', $keyword)
                                        ->orLike('siswa.nis', $keyword)
                                    ->groupEnd()
                                    ->orderBy('siswa.nama_lengkap', 'ASC')
                                    ->limit(10)
                                    ->findAll();

        $response = [];
        foreach ($results as $row) {
            $namaKelas = !empty($row['nama_kelas']) ? $row['nama_kelas'] : 'Tanpa Kelas';
            
            $response[] = [
                'nis'   => $row['nis'], 
                'nama'  => $row['nama_lengkap'],
                'kelas' => $namaKelas,
                'text_display' => $row['nama_lengkap'] . ' (' . $namaKelas . ')'
            ];
        }

        return $this->response->setJSON($response);
    }
}