<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel;
use App\Models\AuditLogModel;

class KelasController extends BaseController
{
    protected $kelasModel;
    protected $tahunAjaranModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List kelas
     */
    public function index()
    {
        $kelas = $this->kelasModel->getKelasWithTahunAjaran();
        
        // Hitung jumlah siswa per kelas
        foreach ($kelas as &$k) {
            $k['jumlah_siswa'] = $this->kelasModel->countSiswa($k['id_kelas']);
        }
        
        $data = [
            'title' => 'Kelas',
            'kelas' => $kelas
        ];
        
        return view('admin/kelas/index', $data);
    }
    
    /**
     * Form tambah/edit kelas
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/kelas/form', $data);
    }
    
    /**
     * Proses tambah kelas
     */
    public function store()
    {
        $rules = [
            'nama_kelas' => 'required|min_length[2]|max_length[50]',
            'tingkat' => 'required|integer|greater_than[0]|less_than[10]',
            'id_tahun_ajaran' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran')
        ];
        
        $this->kelasModel->insert($data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'kelas',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah kelas: ' . $data['nama_kelas']
        ]);
        
        return redirect()->to(base_url('admin/kelas'))->with('success', 'Kelas berhasil ditambahkan');
    }
    
    /**
     * Form edit kelas
     */
    public function edit($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Kelas tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Kelas',
            'kelas' => $kelas,
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/kelas/form', $data);
    }
    
    /**
     * Proses update kelas
     */
    public function update($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Kelas tidak ditemukan');
        }
        
        $rules = [
            'nama_kelas' => 'required|min_length[2]|max_length[50]',
            'tingkat' => 'required|integer|greater_than[0]|less_than[10]',
            'id_tahun_ajaran' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran')
        ];
        
        $this->kelasModel->update($id, $data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'kelas',
            'data_lama' => json_encode($kelas),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate kelas: ' . $data['nama_kelas']
        ]);
        
        return redirect()->to(base_url('admin/kelas'))->with('success', 'Kelas berhasil diupdate');
    }
    
    /**
     * Hapus kelas
     */
    public function delete($id)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Kelas tidak ditemukan');
        }
        
        // Cek apakah ada siswa di kelas ini
        $jumlahSiswa = $this->kelasModel->countSiswa($id);
        
        if ($jumlahSiswa > 0) {
            return redirect()->to(base_url('admin/kelas'))->with('error', "Kelas tidak dapat dihapus karena masih memiliki {$jumlahSiswa} siswa");
        }
        
        $this->kelasModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'kelas',
            'data_lama' => json_encode($kelas),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus kelas: ' . $kelas['nama_kelas']
        ]);
        
        return redirect()->to(base_url('admin/kelas'))->with('success', 'Kelas berhasil dihapus');
    }
}