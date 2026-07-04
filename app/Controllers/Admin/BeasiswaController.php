<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BeasiswaModel;
use App\Models\SiswaModel;
use App\Models\JenisTagihanModel;
use App\Models\TahunAjaranModel;
use App\Models\TagihanModel;
use App\Models\AuditLogModel;

class BeasiswaController extends BaseController
{
    protected $beasiswaModel;
    protected $siswaModel;
    protected $jenisTagihanModel;
    protected $tahunAjaranModel;
    protected $tagihanModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->beasiswaModel = new BeasiswaModel();
        $this->siswaModel = new SiswaModel();
        $this->jenisTagihanModel = new JenisTagihanModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->tagihanModel = new TagihanModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List beasiswa
     * UPDATE: pagination server via AJAX (261+ baris & terus bertambah), form Tambah/Edit
     * jadi modal di halaman ini juga.
     */
    public function index()
    {
        $filterTahunAjaran = $this->request->getGet('filter_tahun_ajaran');
        $keyword = $this->request->getGet('keyword');
        
        $applyFilters = function ($model) use ($filterTahunAjaran, $keyword) {
            if ($filterTahunAjaran) $model->where('beasiswa.id_tahun_ajaran', $filterTahunAjaran);
            if ($keyword) {
                $model->groupStart()
                      ->like('siswa.nis', $keyword)
                      ->orLike('siswa.nama_lengkap', $keyword)
                      ->orLike('beasiswa.nama_beasiswa', $keyword)
                      ->groupEnd();
            }
            return $model;
        };
        
        if ($this->request->isAJAX()) {
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 15)));
            
            $listModel = new BeasiswaModel();
            $listModel->select('beasiswa.*, siswa.nis, siswa.nama_lengkap as nama_siswa, jenis_tagihan.nama_tagihan, tahun_ajaran.nama_tahun_ajaran')
                      ->join('siswa', 'siswa.id_siswa = beasiswa.id_siswa', 'left')
                      ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = beasiswa.id_jenis_tagihan', 'left')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = beasiswa.id_tahun_ajaran', 'left');
            $applyFilters($listModel);
            
            $total = $listModel->countAllResults(false);
            $rows  = $listModel->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                               ->orderBy('siswa.nama_lengkap', 'ASC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->findAll();
            
            return $this->response->setJSON([
                'rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ]);
        }
        
        $data = [
            'title' => 'Beasiswa',
            'jenis_tagihan' => $this->jenisTagihanModel->getActiveJenisTagihan(),
            'jenis_tagihan_grouped' => $this->jenisTagihanModel->getGroupedJenisTagihan(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'filter_tahun_ajaran' => $filterTahunAjaran,
            'keyword' => $keyword,
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/beasiswa/index', $data);
    }
    
    /**
     * Form tambah beasiswa
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/beasiswa#tambah'));
    }
    
    /**
     * Proses tambah beasiswa
     */
    public function store()
    {
        $modeBeasiswa = $this->request->getPost('mode_beasiswa');
        
        // HANDLE BULK GENERATE BY GRUP
        if ($modeBeasiswa === 'bulk') {
            return $this->storeBulkByGrup();
        }
        
        // HANDLE SINGLE (NORMAL)
        $rules = [
            'id_siswa' => 'required|integer',
            'id_jenis_tagihan' => 'required|integer',
            'id_tahun_ajaran' => 'required|integer',
            'nama_beasiswa' => 'required|min_length[3]',
            'tipe_beasiswa' => 'required|in_list[nominal,persentase]',
            'nilai_beasiswa' => 'required|decimal'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tipeBeasiswa = $this->request->getPost('tipe_beasiswa');
        $nilaiBeasiswa = $this->request->getPost('nilai_beasiswa');
        
        // Validasi nilai beasiswa
        if ($tipeBeasiswa === 'persentase' && ($nilaiBeasiswa < 0 || $nilaiBeasiswa > 100)) {
            return redirect()->back()->withInput()->with('error', 'Nilai persentase beasiswa harus antara 0-100');
        }
        
        if ($tipeBeasiswa === 'nominal' && $nilaiBeasiswa < 0) {
            return redirect()->back()->withInput()->with('error', 'Nilai nominal beasiswa tidak boleh negatif');
        }
        
        $data = [
            'id_siswa' => $this->request->getPost('id_siswa'),
            'id_jenis_tagihan' => $this->request->getPost('id_jenis_tagihan'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'nama_beasiswa' => $this->request->getPost('nama_beasiswa'),
            'tipe_beasiswa' => $tipeBeasiswa,
            'nilai_beasiswa' => $nilaiBeasiswa,
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'aktif'
        ];
        
        $this->beasiswaModel->insert($data);
        
        // Recalculate tagihan yang terkait
        $this->recalculateTagihan($data['id_siswa'], $data['id_jenis_tagihan'], $data['id_tahun_ajaran']);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'beasiswa',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah beasiswa: ' . $data['nama_beasiswa']
        ]);
        
        return redirect()->to(base_url('admin/beasiswa'))->with('success', 'Beasiswa berhasil ditambahkan dan tagihan telah diupdate');
    }
    
    /**
     * Proses tambah beasiswa BULK by grup
     */
    private function storeBulkByGrup()
    {
        $rules = [
            'id_siswa' => 'required|integer',
            'selected_grup' => 'required',
            'id_tahun_ajaran' => 'required|integer',
            'nama_beasiswa' => 'required|min_length[3]',
            'tipe_beasiswa' => 'required|in_list[nominal,persentase]',
            'nilai_beasiswa' => 'required|decimal'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $selectedGrup = $this->request->getPost('selected_grup');
        $idSiswa = $this->request->getPost('id_siswa');
        $idTahunAjaran = $this->request->getPost('id_tahun_ajaran');
        $namaBeasiswa = $this->request->getPost('nama_beasiswa');
        $tipeBeasiswa = $this->request->getPost('tipe_beasiswa');
        $nilaiBeasiswa = $this->request->getPost('nilai_beasiswa');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validasi nilai beasiswa
        if ($tipeBeasiswa === 'persentase' && ($nilaiBeasiswa < 0 || $nilaiBeasiswa > 100)) {
            return redirect()->back()->withInput()->with('error', 'Nilai persentase beasiswa harus antara 0-100');
        }
        
        if ($tipeBeasiswa === 'nominal' && $nilaiBeasiswa < 0) {
            return redirect()->back()->withInput()->with('error', 'Nilai nominal beasiswa tidak boleh negatif');
        }
        
        // Get all jenis tagihan in grup
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
        $details = [];
        
        foreach ($jenisTagihanInGrup as $jt) {
            // Check duplicate
            $existing = $this->beasiswaModel
                             ->where('id_siswa', $idSiswa)
                             ->where('id_jenis_tagihan', $jt['id_jenis_tagihan'])
                             ->where('id_tahun_ajaran', $idTahunAjaran)
                             ->where('nama_beasiswa', $namaBeasiswa)
                             ->first();
            
            if ($existing) {
                $skippedCount++;
                continue;
            }
            
            $data = [
                'id_siswa' => $idSiswa,
                'id_jenis_tagihan' => $jt['id_jenis_tagihan'],
                'id_tahun_ajaran' => $idTahunAjaran,
                'nama_beasiswa' => $namaBeasiswa,
                'tipe_beasiswa' => $tipeBeasiswa,
                'nilai_beasiswa' => $nilaiBeasiswa,
                'keterangan' => $keterangan,
                'status' => 'aktif'
            ];
            
            $this->beasiswaModel->insert($data);
            
            // Recalculate tagihan
            $this->recalculateTagihan($idSiswa, $jt['id_jenis_tagihan'], $idTahunAjaran);
            
            $successCount++;
            $details[] = $jt['nama_tagihan'];
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat beasiswa bulk. Silakan coba lagi.');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'bulk_create',
            'modul' => 'beasiswa',
            'data_baru' => json_encode([
                'grup' => $selectedGrup,
                'id_siswa' => $idSiswa,
                'id_tahun_ajaran' => $idTahunAjaran,
                'nama_beasiswa' => $namaBeasiswa,
                'tipe_beasiswa' => $tipeBeasiswa,
                'nilai_beasiswa' => $nilaiBeasiswa,
                'success_count' => $successCount,
                'skipped_count' => $skippedCount,
                'details' => $details
            ]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => "Bulk generate beasiswa grup: $selectedGrup - $namaBeasiswa"
        ]);
        
        $message = "✅ Berhasil membuat <strong>$successCount beasiswa</strong> untuk grup <strong>$selectedGrup</strong>.";
        if ($skippedCount > 0) {
            $message .= " <br>⚠️ $skippedCount beasiswa dilewati karena sudah ada.";
        }
        
        return redirect()->to(base_url('admin/beasiswa'))->with('success', $message);
    }
    
    /**
     * Form edit beasiswa
     */
    /**
     * Form edit beasiswa
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function edit($id)
    {
        $beasiswa = $this->beasiswaModel->find($id);
        
        if (!$beasiswa) {
            return redirect()->to(base_url('admin/beasiswa'))->with('error', 'Beasiswa tidak ditemukan');
        }
        
        return redirect()->to(base_url('admin/beasiswa#edit-' . $id));
    }
    
    /**
     * Proses update beasiswa
     */
    public function update($id)
    {
        $beasiswa = $this->beasiswaModel->find($id);
        
        if (!$beasiswa) {
            return redirect()->to(base_url('admin/beasiswa'))->with('error', 'Beasiswa tidak ditemukan');
        }
        
        $rules = [
            'id_siswa' => 'required|integer',
            'id_jenis_tagihan' => 'required|integer',
            'id_tahun_ajaran' => 'required|integer',
            'nama_beasiswa' => 'required|min_length[3]',
            'tipe_beasiswa' => 'required|in_list[nominal,persentase]',
            'nilai_beasiswa' => 'required|decimal',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tipeBeasiswa = $this->request->getPost('tipe_beasiswa');
        $nilaiBeasiswa = $this->request->getPost('nilai_beasiswa');
        
        // Validasi nilai beasiswa
        if ($tipeBeasiswa === 'persentase' && ($nilaiBeasiswa < 0 || $nilaiBeasiswa > 100)) {
            return redirect()->back()->withInput()->with('error', 'Nilai persentase beasiswa harus antara 0-100');
        }
        
        if ($tipeBeasiswa === 'nominal' && $nilaiBeasiswa < 0) {
            return redirect()->back()->withInput()->with('error', 'Nilai nominal beasiswa tidak boleh negatif');
        }
        
        $data = [
            'id_siswa' => $this->request->getPost('id_siswa'),
            'id_jenis_tagihan' => $this->request->getPost('id_jenis_tagihan'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'nama_beasiswa' => $this->request->getPost('nama_beasiswa'),
            'tipe_beasiswa' => $tipeBeasiswa,
            'nilai_beasiswa' => $nilaiBeasiswa,
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => $this->request->getPost('status')
        ];
        
        $this->beasiswaModel->update($id, $data);
        
        // Recalculate tagihan yang terkait
        $this->recalculateTagihan($data['id_siswa'], $data['id_jenis_tagihan'], $data['id_tahun_ajaran']);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'beasiswa',
            'data_lama' => json_encode($beasiswa),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate beasiswa: ' . $data['nama_beasiswa']
        ]);
        
        return redirect()->to(base_url('admin/beasiswa'))->with('success', 'Beasiswa berhasil diupdate dan tagihan telah direcalculate');
    }
    
    /**
     * Hapus beasiswa
     */
    public function delete($id)
    {
        $beasiswa = $this->beasiswaModel->find($id);
        
        if (!$beasiswa) {
            return redirect()->to(base_url('admin/beasiswa'))->with('error', 'Beasiswa tidak ditemukan');
        }
        
        $this->beasiswaModel->delete($id);
        
        // Recalculate tagihan yang terkait
        $this->recalculateTagihan($beasiswa['id_siswa'], $beasiswa['id_jenis_tagihan'], $beasiswa['id_tahun_ajaran']);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'beasiswa',
            'data_lama' => json_encode($beasiswa),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus beasiswa: ' . $beasiswa['nama_beasiswa']
        ]);
        
        return redirect()->to(base_url('admin/beasiswa'))->with('success', 'Beasiswa berhasil dihapus dan tagihan telah direcalculate');
    }
    
    /**
     * Recalculate tagihan setelah beasiswa berubah
     */
    private function recalculateTagihan($idSiswa, $idJenisTagihan, $idTahunAjaran)
    {
        // Get all tagihan yang terkait
        $tagihan = $this->tagihanModel
            ->where('id_siswa', $idSiswa)
            ->where('id_jenis_tagihan', $idJenisTagihan)
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->findAll();
        
        // Get all active beasiswa
        $beasiswa = $this->beasiswaModel
            ->where('id_siswa', $idSiswa)
            ->where('id_jenis_tagihan', $idJenisTagihan)
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->where('status', 'aktif')
            ->findAll();
        
        foreach ($tagihan as $t) {
            $nominalTagihan = $t['nominal_tagihan'];
            $totalPotongan = 0;
            
            // Calculate total potongan
            foreach ($beasiswa as $b) {
                if ($b['tipe_beasiswa'] === 'nominal') {
                    $totalPotongan += $b['nilai_beasiswa'];
                } else { // persentase
                    $totalPotongan += ($nominalTagihan * $b['nilai_beasiswa'] / 100);
                }
            }
            
            // Ensure potongan tidak melebihi nominal
            $totalPotongan = min($totalPotongan, $nominalTagihan);
            
            $nominalAkhir = max(0, $nominalTagihan - $totalPotongan);
            $sisaTagihan = max(0, $nominalAkhir - $t['nominal_dibayar']);
            
            // Update status
            if ($sisaTagihan <= 0) {
                $status = 'lunas';
            } elseif ($t['nominal_dibayar'] > 0) {
                $status = 'cicil';
            } else {
                $status = 'belum_bayar';
            }
            
            // Update tagihan
            $this->tagihanModel->update($t['id_tagihan'], [
                'nominal_potongan' => $totalPotongan,
                'nominal_akhir' => $nominalAkhir,
                'sisa_tagihan' => $sisaTagihan,
                'status_tagihan' => $status
            ]);
        }
    }
}