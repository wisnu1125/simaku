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
     * List skema tagihan - FIXED: Mengirim semua data agar DataTables JS bekerja sempurna
     */
    public function index()
    {
        $filter = $this->request->getGet('filter_tahun_ajaran');
        
        // Mulai Builder
        $builder = $this->skemaTagihanModel
                        ->select('skema_tagihan.*, 
                                  jenis_tagihan.nama_tagihan, 
                                  jenis_tagihan.tipe_tagihan,
                                  jenis_tagihan.grup_tagihan,
                                  tahun_ajaran.nama_tahun_ajaran,
                                  tahun_ajaran.status as status_tahun_ajaran,
                                  kelas.nama_kelas,
                                  siswa.nama_lengkap as nama_siswa,
                                  siswa.nis')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = skema_tagihan.id_tahun_ajaran', 'left')
                        ->join('kelas', 'kelas.id_kelas = skema_tagihan.id_kelas', 'left')
                        ->join('siswa', 'siswa.id_siswa = skema_tagihan.id_siswa', 'left');
        
        // Filter Tahun Ajaran (Wajib ada biar data tidak terlalu berat)
        if ($filter) {
            $builder->where('skema_tagihan.id_tahun_ajaran', $filter);
        }
        
        // Sorting Default
        $builder->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                ->orderBy('jenis_tagihan.nama_tagihan', 'ASC');
        
        // PERBAIKAN UTAMA:
        // Ganti paginate() dengan findAll() agar DataTables JS menerima semua data
        // dan bisa membuat dropdown filter Group dengan lengkap.
        $skema = $builder->findAll();
        
        // Grouping Data (Logic PHP tetap dipertahankan)
        $groupedSkema = [];
        foreach ($skema as $st) {
            // Pastikan nama grup konsisten (trim spasi)
            $grup = trim($st['grup_tagihan'] ?? '');
            if ($grup === '') {
                $grup = 'LAINNYA';
            }
            
            if (!isset($groupedSkema[$grup])) {
                $groupedSkema[$grup] = [];
            }
            $groupedSkema[$grup][] = $st;
        }
        
        // Kirim data ke View
        $data = [
            'title' => 'Skema Tagihan',
            'skema' => $skema,               // Data mentah (full)
            'grouped_skema' => $groupedSkema,// Data terkelompok (full)
            'pager' => null,                 // Pager dimatikan (dihandle JS DataTables)
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'filter_tahun_ajaran' => $filter
        ];
        
        return view('admin/skema_tagihan/index', $data);
    }
    
    /**
     * Form tambah skema tagihan
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Skema Tagihan',
            'jenis_tagihan' => $this->jenisTagihanModel->getActiveJenisTagihan(),
            'jenis_tagihan_grouped' => $this->jenisTagihanModel->getGroupedJenisTagihan(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/skema_tagihan/form', $data);
    }
    
    /**
     * Form generate bulk (checklist style)
     */
    public function generateBulk()
    {
        $data = [
            'title' => 'Generate Skema Tagihan (Bulk)',
            'jenis_tagihan_grouped' => $this->jenisTagihanModel->getGroupedJenisTagihan(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran()
        ];
        
        return view('admin/skema_tagihan/form_bulk', $data);
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
    public function edit($id)
    {
        $skema = $this->skemaTagihanModel->find($id);
        
        if (!$skema) {
            return redirect()->to(base_url('admin/skema-tagihan'))->with('error', 'Skema tagihan tidak ditemukan');
        }
        
        $skema['tipe_skema'] = $skema['id_siswa'] ? 'siswa' : 'kelas';
        
        $data = [
            'title' => 'Edit Skema Tagihan',
            'skema' => $skema,
            'jenis_tagihan' => $this->jenisTagihanModel->getActiveJenisTagihan(),
            'jenis_tagihan_grouped' => $this->jenisTagihanModel->getGroupedJenisTagihan(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/skema_tagihan/form', $data);
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