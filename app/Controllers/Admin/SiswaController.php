<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AuditLogModel;
use App\Services\SiswaService;

class SiswaController extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $auditLogModel;
    protected $siswaService;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->auditLogModel = new AuditLogModel();
        $this->siswaService = new SiswaService();
    }
    
    /**
     * List siswa - FIXED for DataTables Client-Side
     */
    public function index()
    {
        // 1. Query Builder
        $builder = $this->siswaModel->select('siswa.*, kelas.nama_kelas, kelas.tingkat')
                                    ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left');
        
        // 2. Order By (Terbaru di atas)
        $builder->orderBy('siswa.id_siswa', 'DESC');
        
        // 3. GET ALL DATA (PENTING: Jangan pakai paginate() agar DataTables JS yang handle pagination)
        $siswa = $builder->findAll();
        
        // 4. Ambil data kelas untuk dropdown filter
        $kelasList = $this->kelasModel->orderBy('nama_kelas', 'ASC')->findAll();
        
        $data = [
            'title'      => 'Data Siswa',
            'siswa'      => $siswa,       // Kirim semua data
            'kelas_list' => $kelasList    // Kirim list kelas untuk filter dropdown
        ];
        
        return view('admin/siswa/index', $data);
    }
    
    /**
     * Form tambah siswa
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Siswa',
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/siswa/form', $data);
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
     */
    public function detail($id)
    {
        $siswa = $this->siswaModel->getSiswaWithKelas($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Siswa',
            'siswa' => $siswa
        ];
        
        return view('admin/siswa/detail', $data);
    }
    
    /**
     * Form edit siswa
     */
    public function edit($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Siswa',
            'siswa' => $siswa,
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/siswa/form', $data);
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