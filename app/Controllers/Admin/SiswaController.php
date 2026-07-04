<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AuditLogModel;
use App\Models\TagihanModel;
use App\Services\SiswaService;

class SiswaController extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $auditLogModel;
    protected $siswaService;
    protected $tagihanModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->auditLogModel = new AuditLogModel();
        $this->siswaService = new SiswaService();
        $this->tagihanModel = new TagihanModel();
    }
    
    /**
     * List siswa.
     * UPDATE: sekarang juga menjadi tempat form Tambah/Edit (modal) dan Detail (drawer),
     * jadi tidak perlu lagi pindah halaman untuk 3 aksi itu.
     * UPDATE 2: pencarian/filter/pagination sekarang jalan di server (lewat AJAX),
     * bukan lagi kirim SEMUA baris siswa ke browser sekaligus -- supaya tetap ringan
     * walau data siswa terus bertambah.
     */
    public function index()
    {
        if ($this->request->isAJAX()) {
            return $this->listJson();
        }
        
        $data = [
            'title'      => 'Data Siswa',
            // Kelas dipakai untuk dropdown filter & dropdown di modal form -- datanya kecil,
            // aman selalu di-load langsung (bukan sumber masalah performa).
            'kelas_list' => $this->kelasModel->getKelasWithTahunAjaran(),
            'errors'     => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/siswa/index', $data);
    }
    
    /**
     * Endpoint JSON untuk tabel siswa: dukung ?q=&kelas=&status=&page=&per_page=
     */
    private function listJson()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 12)));
        $q       = trim((string) $this->request->getGet('q'));
        $fKelas  = $this->request->getGet('kelas');
        $fStatus = $this->request->getGet('status');
        
        // Query baris untuk halaman ini
        $listModel = new SiswaModel();
        $listModel->select('siswa.*, kelas.nama_kelas, kelas.tingkat')
                  ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left');
        $this->applySiswaFilters($listModel, $q, $fKelas, $fStatus);
        $listModel->orderBy('siswa.id_siswa', 'DESC');
        
        // false = jangan reset query builder, supaya where/join di atas masih kepakai buat limit() di bawah
        $total = $listModel->countAllResults(false);
        $rows  = $listModel->limit($perPage, ($page - 1) * $perPage)->findAll();
        
        // Statistik ringkas (dihitung dari SELURUH data, bukan cuma halaman ini yang sedang tampil).
        // Pakai instance model baru masing-masing supaya query-nya independen/tidak nyampur.
        $stats = [
            'total' => (new SiswaModel())->countAllResults(),
            'aktif' => (new SiswaModel())->where('status_siswa', 'aktif')->countAllResults(),
            'lulus' => (new SiswaModel())->where('status_siswa', 'lulus')->countAllResults(),
        ];
        
        return $this->response->setJSON([
            'rows'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) max(1, ceil($total / $perPage)),
            'stats'       => $stats,
        ]);
    }
    
    private function applySiswaFilters($builder, string $q, $fKelas, $fStatus)
    {
        if ($q !== '') {
            $builder->groupStart()
                    ->like('siswa.nama_lengkap', $q)
                    ->orLike('siswa.nis', $q)
                    ->orLike('siswa.virtual_account', $q)
                    ->groupEnd();
        }
        if (!empty($fKelas)) {
            $builder->where('kelas.nama_kelas', $fKelas);
        }
        if (!empty($fStatus)) {
            $builder->where('siswa.status_siswa', $fStatus);
        }
    }
    
    /**
     * Form tambah siswa
     * UPDATE: form sudah jadi modal di halaman index, jadi URL lama ini
     * dialihkan ke sana supaya bookmark/tautan lama tetap jalan.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/siswa#tambah'));
    }
    
    /**
     * Proses tambah siswa
     */
    public function store()
    {
        $rules = [
            'nis' => 'required|is_unique[siswa.nis]',
            'nisn' => 'permit_empty|is_unique[siswa.nisn]',
            'nama_lengkap' => 'required|min_length[3]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'id_kelas' => 'permit_empty|integer',
            'virtual_account' => 'permit_empty|is_unique[siswa.virtual_account]|max_length[20]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Generate Virtual Account jika tidak diisi manual
        $virtualAccount = $this->request->getPost('virtual_account');
        if (empty($virtualAccount)) {
            // Auto-generate VA
            $virtualAccount = $this->siswaService->generateVirtualAccount();
        }
        
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nisn' => $this->request->getPost('nisn') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'nama_wali' => $this->request->getPost('nama_wali'),
            'telp_wali' => $this->request->getPost('telp_wali'),
            'id_kelas' => $this->request->getPost('id_kelas') ?: null,
            'virtual_account' => $virtualAccount,
            'status_siswa' => 'aktif'
        ];
        
        $this->siswaModel->insert($data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'siswa',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah siswa: ' . $data['nama_lengkap'] . ' (VA: ' . $virtualAccount . ')'
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil ditambahkan dengan VA: ' . $virtualAccount);
    }
    
    /**
     * Detail siswa
     * UPDATE: sekarang mendukung 2 mode -
     *  - Dipanggil via AJAX (fetch dari drawer di halaman index) -> balas JSON
     *    berisi biodata + ringkasan tagihan, supaya drawer bisa tampil tanpa reload halaman.
     *  - Diakses langsung lewat URL (mis. bookmark lama) -> redirect ke index dengan
     *    hash supaya drawer yang sama otomatis terbuka di sana.
     */
    public function detail($id)
    {
        $siswa = $this->siswaModel->getSiswaWithKelas($id);
        
        if (!$siswa) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Siswa tidak ditemukan']);
            }
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('admin/siswa#detail-' . $id));
        }
        
        // Ringkasan keuangan siswa (dipakai drawer, tidak mengganti halaman Detail Tagihan yang sudah ada)
        $tagihan = $this->tagihanModel->getTagihanBySiswa($id);
        $totalTagihan = array_sum(array_column($tagihan, 'nominal_akhir'));
        $totalDibayar = array_sum(array_column($tagihan, 'nominal_dibayar'));
        $totalSisa    = array_sum(array_column($tagihan, 'sisa_tagihan'));
        
        // 5 tagihan terbaru untuk pratinjau cepat di drawer
        $tagihanTerbaru = array_slice($tagihan, 0, 5);
        
        return $this->response->setJSON([
            'siswa' => $siswa,
            'ringkasan' => [
                'total_tagihan' => $totalTagihan,
                'total_dibayar' => $totalDibayar,
                'total_sisa'    => $totalSisa,
            ],
            'tagihan_terbaru' => $tagihanTerbaru,
        ]);
    }
    
    /**
     * Form edit siswa
     * UPDATE: form sudah jadi modal di halaman index (data siswa sudah tersedia
     * di sana untuk semua baris), jadi URL lama ini tinggal dialihkan dengan hash
     * supaya modal edit-nya otomatis terbuka untuk siswa yang dituju.
     */
    public function edit($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        return redirect()->to(base_url('admin/siswa#edit-' . $id));
    }
    
    /**
     * Proses update siswa
     */
    public function update($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        $rules = [
            'nis' => "required|is_unique[siswa.nis,id_siswa,{$id}]",
            'nisn' => "permit_empty|is_unique[siswa.nisn,id_siswa,{$id}]",
            'nama_lengkap' => 'required|min_length[3]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'status_siswa' => 'required|in_list[aktif,nonaktif,lulus]',
            'id_kelas' => 'permit_empty|integer',
            'virtual_account' => "required|is_unique[siswa.virtual_account,id_siswa,{$id}]|max_length[20]"
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nisn' => $this->request->getPost('nisn') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'nama_wali' => $this->request->getPost('nama_wali'),
            'telp_wali' => $this->request->getPost('telp_wali'),
            'id_kelas' => $this->request->getPost('id_kelas') ?: null,
            'virtual_account' => $this->request->getPost('virtual_account'),
            'status_siswa' => $this->request->getPost('status_siswa')
        ];
        
        $this->siswaModel->update($id, $data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'siswa',
            'data_lama' => json_encode($siswa),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate siswa: ' . $data['nama_lengkap'] . ' (VA: ' . $data['virtual_account'] . ')'
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil diupdate');
    }
    
    /**
     * Hapus siswa
     */
    public function delete($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        // Cek apakah ada tagihan
        $db = \Config\Database::connect();
        $tagihanCount = $db->table('tagihan')->where('id_siswa', $id)->countAllResults();
        
        if ($tagihanCount > 0) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak dapat dihapus karena memiliki data tagihan. Ubah status menjadi nonaktif sebagai gantinya.');
        }
        
        $this->siswaModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'siswa',
            'data_lama' => json_encode($siswa),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus siswa: ' . $siswa['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil dihapus');
    }
    
    /**
     * AJAX Search siswa (untuk pembayaran)
     */
    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        
        if (strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }
        
        $siswa = $this->siswaModel->searchSiswa($keyword, 10);
        
        return $this->response->setJSON($siswa);
    }
}