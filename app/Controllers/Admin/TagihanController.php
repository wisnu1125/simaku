<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagihanModel;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\JenisTagihanModel;
use App\Services\TagihanService;
use App\Models\AuditLogModel;

class TagihanController extends BaseController
{
    protected $tagihanModel;
    protected $tahunAjaranModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $jenisTagihanModel;
    protected $tagihanService;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->tagihanModel = new TagihanModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->jenisTagihanModel = new JenisTagihanModel();
        $this->tagihanService = new TagihanService();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List tagihan
     */
    public function index()
    {
        $filterTahunAjaran = $this->request->getGet('filter_tahun_ajaran');
        $filterKelas = $this->request->getGet('filter_kelas');
        $filterStatus = $this->request->getGet('filter_status');
        $keyword = $this->request->getGet('keyword');

        $builder = $this->tagihanModel
            ->select('tagihan.*, 
                      siswa.nis, 
                      siswa.nama_lengkap as nama_siswa,
                      jenis_tagihan.nama_tagihan,
                      jenis_tagihan.tipe_tagihan,
                      tahun_ajaran.nama_tahun_ajaran,
                      kelas.nama_kelas')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
            ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
            ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left');

        // ================= FILTER SERVER SIDE =================
        // Filter tetap diperlukan agar browser tidak berat meload semua data histori
        
        if ($filterTahunAjaran) {
            $builder->where('tagihan.id_tahun_ajaran', $filterTahunAjaran);
        }

        if ($filterKelas) {
            $builder->where('tagihan.id_kelas', $filterKelas);
        }

        if ($filterStatus) {
            $builder->where('tagihan.status_tagihan', $filterStatus);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('siswa.nis', $keyword)
                ->orLike('siswa.nama_lengkap', $keyword)
                ->groupEnd();
        }
        // ==========================================

        // FIXED: Menggunakan findAll() menggantikan paginate()
        // Agar DataTables di View bisa mengelola pagination, search, dan sorting secara client-side
        // serta agar perhitungan Total Tagihan/Tunggakan di View akurat untuk semua data yang difilter.
        $tagihan = $builder
            ->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Tagihan',
            'tagihan' => $tagihan,
            'pager' => null, // Pager bawaan CI4 dimatikan karena sudah pakai DataTables
            'tahun_ajaran' => $this->tahunAjaranModel
                ->orderBy('nama_tahun_ajaran', 'DESC')
                ->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'filter_tahun_ajaran' => $filterTahunAjaran,
            'filter_kelas' => $filterKelas,
            'filter_status' => $filterStatus,
            'keyword' => $keyword
        ];

        return view('admin/tagihan/index', $data);
    }
    
    /**
     * Halaman generate tagihan
     */
    public function generate()
    {
        $data = [
            'title' => 'Generate Tagihan',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran()
        ];
        
        return view('admin/tagihan/generate', $data);
    }
    
    /**
     * Proses generate tagihan
     * MODIFIED: Memperbaiki logika Overwrite agar TIDAK menghapus data kelas/siswa lain.
     */
    public function doGenerate()
    {
        $rules = [
            'id_tahun_ajaran' => 'required|integer',
            'tipe_generate' => 'required|in_list[semua,kelas,siswa]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $idTahunAjaran = $this->request->getPost('id_tahun_ajaran');
        $tipeGenerate = $this->request->getPost('tipe_generate');
        $idKelas = null;
        $idSiswa = null;
        
        // 1. Inisialisasi Builder Delete
        $deleteBuilder = $this->tagihanModel->where('id_tahun_ajaran', $idTahunAjaran);
        
        // 2. Safety Check Wajib: Hanya hapus yang BELUM ada pembayaran (nominal_dibayar = 0)
        // Ini memastikan data historis pembayaran aman.
        $deleteBuilder->where('nominal_dibayar', 0);

        // 3. Logika Filter Spesifik (PENTING: Agar tidak menghapus semua data)
        if ($tipeGenerate === 'kelas') {
            $idKelas = $this->request->getPost('id_kelas');
            if (!$idKelas) {
                return redirect()->back()->withInput()->with('error', 'Kelas harus dipilih');
            }
            
            // Perbaikan: Pastikan HANYA menghapus data milik kelas yang dipilih
            $deleteBuilder->where('id_kelas', $idKelas);

            // Eksekusi Delete HANYA jika idKelas valid
            $deleteBuilder->delete();

        } elseif ($tipeGenerate === 'siswa') {
            $idSiswa = $this->request->getPost('id_siswa');
            if (!$idSiswa) {
                return redirect()->back()->withInput()->with('error', 'Siswa harus dipilih');
            }
            
            // Perbaikan: Pastikan HANYA menghapus data milik siswa yang dipilih
            $deleteBuilder->where('id_siswa', $idSiswa);

            // Eksekusi Delete HANYA jika idSiswa valid
            $deleteBuilder->delete();

        } elseif ($tipeGenerate === 'semua') {
            // Jika tipe 'semua', barulah kita menghapus semua tagihan di tahun ajaran tersebut (yg belum dibayar)
            // Ini untuk merefresh tagihan satu sekolah
            $deleteBuilder->delete();
        }
        
        // 4. Generate tagihan baru via Service (akan mengisi kekosongan yang baru saja dihapus)
        $result = $this->tagihanService->generateTagihan(
            $idTahunAjaran, 
            $idKelas, 
            $idSiswa, 
            session()->get('id_user')
        );
        
        if ($result['success']) {
            return redirect()->to(base_url('admin/tagihan'))->with('success', $result['message']);
        } else {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }
    }
    
    /**
     * Detail tagihan siswa
     */
    public function detail($idSiswa)
    {
        $siswa = $this->siswaModel->getSiswaWithKelas($idSiswa);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/tagihan'))->with('error', 'Siswa tidak ditemukan');
        }
        
        // Get all tagihan
        $tagihan = $this->tagihanModel->getTagihanBySiswa($idSiswa);
        
        // Group by tahun ajaran
        $tagihanByTahun = [];
        foreach ($tagihan as $t) {
            $tahun = $t['nama_tahun_ajaran'];
            if (!isset($tagihanByTahun[$tahun])) {
                $tagihanByTahun[$tahun] = [];
            }
            $tagihanByTahun[$tahun][] = $t;
        }
        
        $data = [
            'title' => 'Detail Tagihan Siswa',
            'siswa' => $siswa,
            'tagihan_by_tahun' => $tagihanByTahun
        ];
        
        return view('admin/tagihan/detail', $data);
    }

    /**
     * Hapus tagihan (single)
     */
    public function delete($id)
    {
        $tagihan = $this->tagihanModel->find($id);
        
        if (!$tagihan) {
            return redirect()->to(base_url('admin/tagihan'))->with('error', 'Tagihan tidak ditemukan');
        }
        
        // Cek apakah sudah ada pembayaran
        if ($tagihan['nominal_dibayar'] > 0) {
            return redirect()->to(base_url('admin/tagihan'))->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran');
        }
        
        // Get detail untuk audit log
        $detail = $this->tagihanModel
                        ->select('tagihan.*, siswa.nama_lengkap, siswa.nis, jenis_tagihan.nama_tagihan')
                        ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan')
                        ->find($id);
        
        $this->tagihanModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'tagihan',
            'data_lama' => json_encode($detail),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus tagihan: ' . $detail['nama_tagihan'] . ' - ' . $detail['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/tagihan'))->with('success', 'Tagihan berhasil dihapus');
    }

    /**
     * Hapus beberapa tagihan sekaligus (bulk delete)
     */
    public function bulkDelete()
    {
        $idTagihan = $this->request->getPost('id_tagihan');
        
        // Validasi apakah ada data yang dipilih
        if (empty($idTagihan) || !is_array($idTagihan)) {
            return redirect()->to(base_url('admin/tagihan'))->with('error', 'Tidak ada tagihan yang dipilih');
        }
        
        $deletedCount = 0;
        $failedCount = 0;
        $deletedItems = [];
        
        foreach ($idTagihan as $id) {
            // Get tagihan detail
            $tagihan = $this->tagihanModel->find($id);
            
            if (!$tagihan) {
                $failedCount++;
                continue;
            }
            
            // Cek apakah sudah ada pembayaran
            if ($tagihan['nominal_dibayar'] > 0) {
                $failedCount++;
                continue;
            }
            
            // Get detail untuk audit log
            $detail = $this->tagihanModel
                            ->select('tagihan.*, siswa.nama_lengkap, siswa.nis, jenis_tagihan.nama_tagihan')
                            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa')
                            ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan')
                            ->find($id);
            
            // Hapus tagihan
            if ($this->tagihanModel->delete($id)) {
                $deletedCount++;
                $deletedItems[] = $detail['nama_tagihan'] . ' - ' . $detail['nama_lengkap'];
            } else {
                $failedCount++;
            }
        }
        
        // Audit log untuk bulk delete
        if ($deletedCount > 0) {
            $this->auditLogModel->insert([
                'id_user' => session()->get('id_user'),
                'aksi' => 'bulk_delete',
                'modul' => 'tagihan',
                'data_lama' => json_encode($deletedItems),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'keterangan' => 'Menghapus ' . $deletedCount . ' tagihan sekaligus'
            ]);
        }
        
        // Response message
        $message = '';
        if ($deletedCount > 0) {
            $message .= $deletedCount . ' tagihan berhasil dihapus. ';
        }
        if ($failedCount > 0) {
            $message .= $failedCount . ' tagihan gagal dihapus (sudah ada pembayaran atau tidak ditemukan).';
        }
        
        $messageType = $deletedCount > 0 ? 'success' : 'error';
        
        return redirect()->to(base_url('admin/tagihan'))->with($messageType, $message);
    }
}