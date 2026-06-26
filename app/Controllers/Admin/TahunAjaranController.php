<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\AuditLogModel;

class TahunAjaranController extends BaseController
{
    protected $tahunAjaranModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List tahun ajaran
     */
    public function index()
    {
        $data = [
            'title' => 'Tahun Ajaran',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll()
        ];
        
        return view('admin/tahun_ajaran/index', $data);
    }
    
    /**
     * Form tambah tahun ajaran
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Tahun Ajaran',
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/tahun_ajaran/form', $data);
    }
    
    /**
     * Proses tambah tahun ajaran
     */
    public function store()
    {
        $rules = [
            'nama_tahun_ajaran' => 'required|is_unique[tahun_ajaran.nama_tahun_ajaran]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Cek apakah checkbox "set_aktif" dicentang
        $setAktif = $this->request->getPost('set_aktif');
        
        $data = [
            'nama_tahun_ajaran' => $this->request->getPost('nama_tahun_ajaran'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status' => $setAktif === '1' ? 'aktif' : 'belum_aktif'
        ];
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Jika set aktif, nonaktifkan tahun ajaran yang aktif sebelumnya
        if ($setAktif === '1') {
            $this->tahunAjaranModel->where('status', 'aktif')
                                   ->set(['status' => 'selesai'])
                                   ->update();
        }
        
        // Insert tahun ajaran baru
        $this->tahunAjaranModel->insert($data);
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan tahun ajaran');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'tahun_ajaran',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah tahun ajaran: ' . $data['nama_tahun_ajaran']
        ]);
        
        return redirect()->to(base_url('admin/tahun-ajaran'))->with('success', 'Tahun ajaran berhasil ditambahkan');
    }
    
    /**
     * Form edit tahun ajaran
     */
    public function edit($id)
    {
        $tahunAjaran = $this->tahunAjaranModel->find($id);
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Tahun Ajaran',
            'tahun_ajaran' => $tahunAjaran,
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/tahun_ajaran/form', $data);
    }
    
    /**
     * Proses update tahun ajaran
     */
    public function update($id)
    {
        $tahunAjaran = $this->tahunAjaranModel->find($id);
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        $rules = [
            'nama_tahun_ajaran' => 'required|min_length[7]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Data yang akan diupdate (tanpa status dulu)
        $data = [
            'nama_tahun_ajaran' => $this->request->getPost('nama_tahun_ajaran'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai')
        ];
        
        // Cek apakah user mencentang "Aktifkan tahun ajaran ini"
        $setAktif = $this->request->getPost('set_aktif');
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        if ($setAktif === '1') {
            // Set semua tahun ajaran aktif lain jadi 'selesai'
            $this->tahunAjaranModel->where('id_tahun_ajaran !=', $id)
                                   ->where('status', 'aktif')
                                   ->set(['status' => 'selesai'])
                                   ->update();
            
            // Set tahun ajaran ini jadi 'aktif'
            $data['status'] = 'aktif';
            
            $keterangan = 'Mengupdate dan mengaktifkan tahun ajaran: ' . $data['nama_tahun_ajaran'];
            $aksi = 'activate';
        } else {
            // Jika checkbox tidak dicentang, JANGAN ubah status
            // Status tetap pakai nilai lama
            $keterangan = 'Mengupdate tahun ajaran: ' . $data['nama_tahun_ajaran'];
            $aksi = 'update';
        }
        
        // Update tahun ajaran
        $this->tahunAjaranModel->update($id, $data);
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate tahun ajaran');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => $aksi,
            'modul' => 'tahun_ajaran',
            'data_lama' => json_encode($tahunAjaran),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => $keterangan
        ]);
        
        $successMessage = ($setAktif === '1') ? 'Tahun ajaran berhasil diupdate dan diaktifkan' : 'Tahun ajaran berhasil diupdate';
        
        return redirect()->to(base_url('admin/tahun-ajaran'))->with('success', $successMessage);
    }
    
    /**
     * Hapus tahun ajaran
     */
    public function delete($id)
    {
        $tahunAjaran = $this->tahunAjaranModel->find($id);
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        // Cek apakah ada data terkait (kelas, tagihan, dll)
        $db = \Config\Database::connect();
        $kelasCount = $db->table('kelas')->where('id_tahun_ajaran', $id)->countAllResults();
        
        if ($kelasCount > 0) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak dapat dihapus karena masih memiliki data kelas');
        }
        
        $this->tahunAjaranModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'tahun_ajaran',
            'data_lama' => json_encode($tahunAjaran),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus tahun ajaran: ' . $tahunAjaran['nama_tahun_ajaran']
        ]);
        
        return redirect()->to(base_url('admin/tahun-ajaran'))->with('success', 'Tahun ajaran berhasil dihapus');
    }
    
    /**
     * Activate tahun ajaran (dari tombol Aktifkan di index)
     */
    public function activate($id)
    {
        $tahunAjaran = $this->tahunAjaranModel->find($id);
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Set semua tahun ajaran aktif jadi 'selesai'
        $this->tahunAjaranModel->where('status', 'aktif')
                               ->set(['status' => 'selesai'])
                               ->update();
        
        // Aktifkan tahun ajaran yang dipilih
        $this->tahunAjaranModel->update($id, ['status' => 'aktif']);
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Gagal mengaktifkan tahun ajaran');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'activate',
            'modul' => 'tahun_ajaran',
            'data_lama' => json_encode(['status' => $tahunAjaran['status']]),
            'data_baru' => json_encode(['status' => 'aktif']),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengaktifkan tahun ajaran: ' . $tahunAjaran['nama_tahun_ajaran']
        ]);
        
        return redirect()->to(base_url('admin/tahun-ajaran'))->with('success', 'Tahun ajaran berhasil diaktifkan');
    }
    
    /**
     * Close tahun ajaran (Lock/Tutup)
     */
    public function close($id)
    {
        // Hanya super admin yang bisa close
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Anda tidak memiliki akses untuk menutup tahun ajaran');
        }
        
        $tahunAjaran = $this->tahunAjaranModel->find($id);
        
        if (!$tahunAjaran) {
            return redirect()->to(base_url('admin/tahun-ajaran'))->with('error', 'Tahun ajaran tidak ditemukan');
        }
        
        $this->tahunAjaranModel->update($id, ['status' => 'selesai']);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'tahun_ajaran',
            'data_lama' => json_encode($tahunAjaran),
            'data_baru' => json_encode(['status' => 'selesai']),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menutup tahun ajaran: ' . $tahunAjaran['nama_tahun_ajaran']
        ]);
        
        return redirect()->to(base_url('admin/tahun-ajaran'))->with('success', 'Tahun ajaran berhasil ditutup');
    }
}