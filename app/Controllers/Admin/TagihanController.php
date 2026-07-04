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
     * UPDATE: pencarian/filter/pagination sekarang lewat AJAX + server-side (sebelumnya
     * findAll() semua baris yang cocok filter tanpa batas -- dengan 3.274+ baris dan terus
     * bertambah, itu makin berat. Sekarang server cuma kirim halaman yang sedang dilihat.
     */
    public function index()
    {
        $filterTahunAjaran = $this->request->getGet('filter_tahun_ajaran');
        $filterKelas = $this->request->getGet('filter_kelas');
        $filterStatus = $this->request->getGet('filter_status');
        $keyword = $this->request->getGet('keyword');

        $applyFilters = function ($model) use ($filterTahunAjaran, $filterKelas, $filterStatus, $keyword) {
            if ($filterTahunAjaran) $model->where('tagihan.id_tahun_ajaran', $filterTahunAjaran);
            if ($filterKelas) $model->where('tagihan.id_kelas', $filterKelas);
            if ($filterStatus) $model->where('tagihan.status_tagihan', $filterStatus);
            if ($keyword) {
                $model->groupStart()->like('siswa.nis', $keyword)->orLike('siswa.nama_lengkap', $keyword)->groupEnd();
            }
            return $model;
        };

        if ($this->request->isAJAX()) {
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 20)));

            $listModel = new TagihanModel();
            $listModel->select('tagihan.*, siswa.nis, siswa.nama_lengkap as nama_siswa, jenis_tagihan.nama_tagihan,
                                 jenis_tagihan.tipe_tagihan, tahun_ajaran.nama_tahun_ajaran, kelas.nama_kelas')
                      ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                      ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                      ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left');
            $applyFilters($listModel);

            $total = $listModel->countAllResults(false);
            $rows  = $listModel->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                               ->orderBy('siswa.nama_lengkap', 'ASC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->findAll();

            // Ringkasan (total nominal & tunggakan) mengikuti filter yang sama, dihitung di DB
            $statsModel = new TagihanModel();
            $statsModel->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left');
            $applyFilters($statsModel);
            $agg = $statsModel->select('SUM(tagihan.nominal_akhir) as total_nominal, SUM(tagihan.sisa_tagihan) as total_sisa, COUNT(*) as jumlah')->first();

            return $this->response->setJSON([
                'rows' => $rows,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
                'stats' => [
                    'total_nominal' => (float) ($agg['total_nominal'] ?? 0),
                    'total_sisa' => (float) ($agg['total_sisa'] ?? 0),
                    'jumlah' => (int) ($agg['jumlah'] ?? 0),
                ],
            ]);
        }

        $data = [
            'title' => 'Tagihan',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
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
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function generate()
    {
        return redirect()->to(base_url('admin/tagihan#generate'));
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