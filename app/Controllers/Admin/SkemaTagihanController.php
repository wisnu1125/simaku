<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SkemaTagihanModel;
use App\Models\JenisTagihanModel;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AuditLogModel;

class SkemaTagihanController extends BaseController
{
    protected $skemaTagihanModel;
    protected $jenisTagihanModel;
    protected $tahunAjaranModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->skemaTagihanModel = new SkemaTagihanModel();
        $this->jenisTagihanModel = new JenisTagihanModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List skema tagihan
     * UPDATE: pagination server via AJAX (289+ baris, terus bertambah tiap kali generate
     * dijalankan). Filter Grup baru ditambahkan supaya tetap gampang ditelusuri walau
     * tampilannya sekarang tabel datar (bukan dikelompokkan di halaman seperti sebelumnya --
     * pengelompokan tidak cocok digabung dengan pagination per halaman).
     */
    public function index()
    {
        $filterTahunAjaran = $this->request->getGet('filter_tahun_ajaran');
        $filterGrup = $this->request->getGet('filter_grup');
        $keyword = $this->request->getGet('keyword');
        
        $applyFilters = function ($model) use ($filterTahunAjaran, $filterGrup, $keyword) {
            if ($filterTahunAjaran) $model->where('skema_tagihan.id_tahun_ajaran', $filterTahunAjaran);
            if ($filterGrup) $model->where('jenis_tagihan.grup_tagihan', $filterGrup);
            if ($keyword) {
                $model->groupStart()
                      ->like('siswa.nama_lengkap', $keyword)
                      ->orLike('kelas.nama_kelas', $keyword)
                      ->orLike('jenis_tagihan.nama_tagihan', $keyword)
                      ->groupEnd();
            }
            return $model;
        };
        
        if ($this->request->isAJAX()) {
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 20)));
            
            $listModel = new SkemaTagihanModel();
            $listModel->select('skema_tagihan.*, jenis_tagihan.nama_tagihan, jenis_tagihan.tipe_tagihan, jenis_tagihan.grup_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran, kelas.nama_kelas, siswa.nama_lengkap as nama_siswa, siswa.nis')
                      ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = skema_tagihan.id_tahun_ajaran', 'left')
                      ->join('kelas', 'kelas.id_kelas = skema_tagihan.id_kelas', 'left')
                      ->join('siswa', 'siswa.id_siswa = skema_tagihan.id_siswa', 'left');
            $applyFilters($listModel);
            
            $total = $listModel->countAllResults(false);
            $rows  = $listModel->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                               ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                               ->orderBy('skema_tagihan.bulan_tagihan', 'ASC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->findAll();
            
            return $this->response->setJSON([
                'rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ]);
        }
        
        // Daftar grup unik (untuk dropdown filter) diambil dari jenis_tagihan, bukan skema_tagihan,
        // supaya grup yang belum punya skema pun tetap muncul sebagai pilihan filter.
        $grupList = $this->jenisTagihanModel->select('grup_tagihan')->distinct()->orderBy('grup_tagihan', 'ASC')->findAll();
        
        // Untuk tampilan awal: default ke tahun ajaran AKTIF kalau belum ada filter dipilih
        $initialFilterTahunAjaran = $filterTahunAjaran;
        if (!$initialFilterTahunAjaran) {
            $activeTA = $this->tahunAjaranModel->getActiveTahunAjaran();
            if ($activeTA) $initialFilterTahunAjaran = $activeTA['id_tahun_ajaran'];
        }
        
        $data = [
            'title' => 'Skema Tagihan',
            'jenis_tagihan_grouped' => $this->jenisTagihanModel->getGroupedJenisTagihan(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'grup_list' => $grupList,
            'filter_tahun_ajaran' => $initialFilterTahunAjaran,
            'filter_grup' => $filterGrup,
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/skema_tagihan/index', $data);
    }
    
    /**
     * Form tambah skema tagihan (checklist massal)
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/skema-tagihan#tambah'));
    }
    
    /**
     * Form generate bulk (checklist style)
     * UPDATE: sebelumnya mengarah ke view 'form_bulk' yang filenya tidak pernah ada di
     * proyek ini (akan 500 error kalau diakses) dan tidak ada satupun tautan ke sini di
     * seluruh aplikasi. Sekarang diarahkan ke halaman index yang modalnya sudah mencakup
     * fungsi checklist massal ini.
     */
    public function generateBulk()
    {
        return redirect()->to(base_url('admin/skema-tagihan#tambah'));
    }
    
    /**
     * Proses generate bulk
     */
    public function storeBulk()
    {
        $rules = [
            'id_tahun_ajaran' => 'required|integer',
            'target_skema' => 'required|in_list[kelas,semua_siswa,siswa]',
            'tagihan' => 'required'
        ];
        
        $targetSkema = $this->request->getPost('target_skema');
        if ($targetSkema === 'kelas') {
            $rules['id_kelas'] = 'required|integer';
        } elseif ($targetSkema === 'siswa') {
            $rules['id_siswa'] = 'required|integer';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Lengkapi semua field yang diperlukan!');
        }
        
        $idTahunAjaran = $this->request->getPost('id_tahun_ajaran');
        $tagihanSelected = $this->request->getPost('tagihan'); 
        $nominalArray = $this->request->getPost('nominal'); 
        $bulanArray = $this->request->getPost('bulan'); 
        
        if (empty($tagihanSelected)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 tagihan!');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $successCount = 0;
        $skippedCount = 0;
        
        // Tentukan target list
        $targetList = [];
        
        if ($targetSkema === 'kelas') {
            $targetList[] = [
                'type' => 'kelas',
                'id_kelas' => $this->request->getPost('id_kelas'),
                'id_siswa' => null
            ];
        } elseif ($targetSkema === 'semua_siswa') {
            $allSiswa = $this->siswaModel->where('status_siswa', 'aktif')->findAll();
            foreach ($allSiswa as $siswa) {
                $targetList[] = [
                    'type' => 'siswa',
                    'id_kelas' => null,
                    'id_siswa' => $siswa['id_siswa']
                ];
            }
        } elseif ($targetSkema === 'siswa') {
            $targetList[] = [
                'type' => 'siswa',
                'id_kelas' => null,
                'id_siswa' => $this->request->getPost('id_siswa')
            ];
        }
        
        foreach ($targetList as $target) {
            foreach ($tagihanSelected as $idJenisTagihan) {
                $nominal = $nominalArray[$idJenisTagihan] ?? 0;
                
                if ($nominal <= 0) continue;
                
                $jenisTagihan = $this->jenisTagihanModel->find($idJenisTagihan);
                if (!$jenisTagihan) continue;
                
                if ($jenisTagihan['tipe_tagihan'] === 'bulanan') {
                    $bulan = $bulanArray[$idJenisTagihan] ?? '';
                    
                    if ($bulan && $bulan != '') {
                        $data = [
                            'id_jenis_tagihan' => $idJenisTagihan,
                            'id_tahun_ajaran' => $idTahunAjaran,
                            'id_kelas' => $target['id_kelas'],
                            'id_siswa' => $target['id_siswa'],
                            'nominal' => $nominal,
                            'bulan_tagihan' => $bulan,
                            'keterangan' => null
                        ];
                        
                        if (!$this->skemaTagihanModel->checkDuplicate($data)) {
                            $this->skemaTagihanModel->insert($data);
                            $successCount++;
                        } else {
                            $skippedCount++;
                        }
                    } else {
                        for ($bulan = 1; $bulan <= 12; $bulan++) {
                            $data = [
                                'id_jenis_tagihan' => $idJenisTagihan,
                                'id_tahun_ajaran' => $idTahunAjaran,
                                'id_kelas' => $target['id_kelas'],
                                'id_siswa' => $target['id_siswa'],
                                'nominal' => $nominal,
                                'bulan_tagihan' => $bulan,
                                'keterangan' => null
                            ];
                            
                            if (!$this->skemaTagihanModel->checkDuplicate($data)) {
                                $this->skemaTagihanModel->insert($data);
                                $successCount++;
                            } else {
                                $skippedCount++;
                            }
                        }
                    }
                } else {
                    $data = [
                        'id_jenis_tagihan' => $idJenisTagihan,
                        'id_tahun_ajaran' => $idTahunAjaran,
                        'id_kelas' => $target['id_kelas'],
                        'id_siswa' => $target['id_siswa'],
                        'nominal' => $nominal,
                        'bulan_tagihan' => null,
                        'keterangan' => null
                    ];
                    
                    if (!$this->skemaTagihanModel->checkDuplicate($data)) {
                        $this->skemaTagihanModel->insert($data);
                        $successCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat skema tagihan bulk.');
        }
        
        $message = "✅ Berhasil membuat <strong>$successCount skema tagihan</strong>.";
        if ($skippedCount > 0) {
            $message .= " <br>⚠️ $skippedCount dilewati karena sudah ada.";
        }
        
        return redirect()->to(base_url('admin/skema-tagihan'))->with('success', $message);
    }
    
    /**
     * Proses tambah skema tagihan (Single)
     */
    public function store()
    {
        $tipeSkema = $this->request->getPost('tipe_skema');
        
        if ($tipeSkema === 'grup') {
            return $this->storeBulkByGrup();
        }
        
        $rules = [
            'id_jenis_tagihan' => 'required|integer',
            'id_tahun_ajaran' => 'required|integer',
            'nominal' => 'required|decimal',
            'tipe_skema' => 'required|in_list[kelas,siswa,grup]'
        ];
        
        if ($tipeSkema === 'kelas') {
            $rules['id_kelas'] = 'required|integer';
        } elseif ($tipeSkema === 'siswa') {
            $rules['id_siswa'] = 'required|integer';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id_jenis_tagihan' => $this->request->getPost('id_jenis_tagihan'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'nominal' => $this->request->getPost('nominal'),
            'bulan_tagihan' => $this->request->getPost('bulan_tagihan') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'id_kelas' => $tipeSkema === 'kelas' ? $this->request->getPost('id_kelas') : null,
            'id_siswa' => $tipeSkema === 'siswa' ? $this->request->getPost('id_siswa') : null
        ];
        
        if ($this->skemaTagihanModel->checkDuplicate($data)) {
            return redirect()->back()->withInput()->with('error', 'Skema tagihan sudah ada');
        }
        
        $this->skemaTagihanModel->insert($data);
        
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'skema_tagihan',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah skema tagihan'
        ]);
        
        return redirect()->to(base_url('admin/skema-tagihan'))->with('success', 'Skema tagihan berhasil ditambahkan');
    }
    
    /**
     * Proses tambah skema tagihan BULK by grup (Private)
     */
    private function storeBulkByGrup()
    {
        $rules = [
            'selected_grup' => 'required',
            'id_tahun_ajaran' => 'required|integer',
            'id_kelas_bulk' => 'required|integer',
            'nominal' => 'required|decimal'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $selectedGrup = $this->request->getPost('selected_grup');
        $idTahunAjaran = $this->request->getPost('id_tahun_ajaran');
        $idKelas = $this->request->getPost('id_kelas_bulk');
        $nominal = $this->request->getPost('nominal');
        $keterangan = $this->request->getPost('keterangan');
        
        $jenisTagihanInGrup = $this->jenisTagihanModel
                                   ->where('grup_tagihan', $selectedGrup)
                                   ->where('status', 'aktif')
                                   ->findAll();
        
        if (empty($jenisTagihanInGrup)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada jenis tagihan dalam grup yang dipilih');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $successCount = 0;
        $skippedCount = 0;
        
        foreach ($jenisTagihanInGrup as $jt) {
            if ($jt['tipe_tagihan'] === 'bulanan') {
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $data = [
                        'id_jenis_tagihan' => $jt['id_jenis_tagihan'],
                        'id_tahun_ajaran' => $idTahunAjaran,
                        'id_kelas' => $idKelas,
                        'nominal' => $nominal,
                        'bulan_tagihan' => $bulan,
                        'keterangan' => $keterangan
                    ];
                    
                    if (!$this->skemaTagihanModel->checkDuplicate($data)) {
                        $this->skemaTagihanModel->insert($data);
                        $successCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            } else {
                $data = [
                    'id_jenis_tagihan' => $jt['id_jenis_tagihan'],
                    'id_tahun_ajaran' => $idTahunAjaran,
                    'id_kelas' => $idKelas,
                    'nominal' => $nominal,
                    'bulan_tagihan' => null,
                    'keterangan' => $keterangan
                ];
                
                if (!$this->skemaTagihanModel->checkDuplicate($data)) {
                    $this->skemaTagihanModel->insert($data);
                    $successCount++;
                } else {
                    $skippedCount++;
                }
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal generate bulk grup.');
        }
        
        $message = "✅ Berhasil membuat <strong>$successCount skema</strong> untuk grup <strong>$selectedGrup</strong>.";
        if ($skippedCount > 0) $message .= " ($skippedCount dilewati)";
        
        return redirect()->to(base_url('admin/skema-tagihan'))->with('success', $message);
    }
    
    /**
     * Form edit skema tagihan
     */
    /**
     * Form edit skema tagihan
     * UPDATE PENTING: sebelumnya method ini me-render view yang sama dengan halaman
     * "Generate Bulk" (form.php) -- padahal view itu TIDAK PERNAH memakai variabel $skema
     * sama sekali, dan satu-satunya <form> di dalamnya selalu submit ke store-bulk (bukan
     * update/$id). Akibatnya, mengklik "Edit" pada baris manapun selama ini menampilkan
     * form checklist massal yang kosong, dan submit-nya tidak pernah benar-benar
     * memperbarui baris yang dimaksud. Sekarang diarahkan ke modal edit yang baru,
     * yang benar-benar terhubung ke update($id) di bawah.
     */
    public function edit($id)
    {
        $skema = $this->skemaTagihanModel->find($id);
        
        if (!$skema) {
            return redirect()->to(base_url('admin/skema-tagihan'))->with('error', 'Skema tagihan tidak ditemukan');
        }
        
        return redirect()->to(base_url('admin/skema-tagihan#edit-' . $id));
    }
    
    /**
     * Proses update skema tagihan
     */
    public function update($id)
    {
        $skema = $this->skemaTagihanModel->find($id);
        
        if (!$skema) {
            return redirect()->to(base_url('admin/skema-tagihan'))->with('error', 'Skema tagihan tidak ditemukan');
        }
        
        $rules = [
            'id_jenis_tagihan' => 'required|integer',
            'id_tahun_ajaran' => 'required|integer',
            'nominal' => 'required|decimal',
            'tipe_skema' => 'required|in_list[kelas,siswa]'
        ];
        
        $tipeSkema = $this->request->getPost('tipe_skema');
        if ($tipeSkema === 'kelas') {
            $rules['id_kelas'] = 'required|integer';
        } else {
            $rules['id_siswa'] = 'required|integer';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id_jenis_tagihan' => $this->request->getPost('id_jenis_tagihan'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'nominal' => $this->request->getPost('nominal'),
            'bulan_tagihan' => $this->request->getPost('bulan_tagihan') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'id_kelas' => $tipeSkema === 'kelas' ? $this->request->getPost('id_kelas') : null,
            'id_siswa' => $tipeSkema === 'siswa' ? $this->request->getPost('id_siswa') : null
        ];
        
        if ($this->skemaTagihanModel->checkDuplicate($data, $id)) {
            return redirect()->back()->withInput()->with('error', 'Skema tagihan sudah ada');
        }
        
        $this->skemaTagihanModel->update($id, $data);
        
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'skema_tagihan',
            'data_lama' => json_encode($skema),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate skema tagihan'
        ]);
        
        return redirect()->to(base_url('admin/skema-tagihan'))->with('success', 'Skema tagihan berhasil diupdate');
    }
    
    /**
     * Hapus skema tagihan
     */
    public function delete($id)
    {
        $skema = $this->skemaTagihanModel->find($id);
        
        if (!$skema) {
            return redirect()->to(base_url('admin/skema-tagihan'))->with('error', 'Skema tagihan tidak ditemukan');
        }
        
        $this->skemaTagihanModel->delete($id);
        
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'skema_tagihan',
            'data_lama' => json_encode($skema),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus skema tagihan'
        ]);
        
        return redirect()->to(base_url('admin/skema-tagihan'))->with('success', 'Skema tagihan berhasil dihapus');
    }
}