<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List users
     */
    public function index()
    {
        $users = $this->userModel->orderBy('nama_lengkap', 'ASC')->findAll();
        
        $data = [
            'title' => 'User Management',
            'users' => $users,
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/users/index', $data);
    }
    
    /**
     * Form tambah user
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/users#tambah'));
    }
    
    /**
     * Proses tambah user
     */
    public function store()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'nama_lengkap' => 'required|min_length[3]',
            // FIX: nilai 'admin' tidak pernah ada di kolom enum `role` pada database
            // (yang valid hanya 'super_admin' dan 'tu_bendahara'). Sebelumnya validasi di sini
            // meloloskan 'admin', lalu proses insert() di bawah gagal secara diam-diam karena
            // validasi bawaan UserModel menolaknya duluan sebelum menyentuh DB.
            'role' => 'required|in_list[super_admin,tu_bendahara]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role'),
            'status' => 'aktif'
        ];
        
        // FIX: cek hasil insert(). Sebelumnya hasil ini diabaikan, jadi kalau gagal
        // (misalnya validasi model menolak), user tetap diarahkan ke pesan "berhasil"
        // padahal tidak ada data yang tersimpan.
        if (!$this->userModel->insert($data)) {
            $modelErrors = $this->userModel->errors();
            if ($modelErrors) {
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan user, silakan coba lagi.');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'users',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah user: ' . $data['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil ditambahkan');
    }
    
    /**
     * Form edit user
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan');
        }
        
        // Prevent editing super_admin by non-super_admin
        if ($user['role'] === 'super_admin' && session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('admin/users'))->with('error', 'Tidak memiliki akses');
        }
        
        return redirect()->to(base_url('admin/users#edit-' . $id));
    }
    
    /**
     * Proses update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan');
        }
        
        // Prevent editing super_admin by non-super_admin
        if ($user['role'] === 'super_admin' && session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('admin/users'))->with('error', 'Tidak memiliki akses');
        }
        
        $rules = [
            'username' => 'required|min_length[4]',
            'email' => 'required|valid_email',
            'nama_lengkap' => 'required|min_length[3]',
            // FIX: samakan dengan enum database ('super_admin', 'tu_bendahara'), lihat catatan di store()
            'role' => 'required|in_list[super_admin,tu_bendahara]',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];
        
        // Check unique username (except current user)
        if ($this->request->getPost('username') !== $user['username']) {
            $rules['username'] .= '|is_unique[users.username]';
        }
        
        // Check unique email (except current user)
        if ($this->request->getPost('email') !== $user['email']) {
            $rules['email'] .= '|is_unique[users.email]';
        }
        
        // If password filled, validate
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];
        
        // Update password if filled
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // FIX: cek hasil update(), lihat catatan yang sama di store()
        if (!$this->userModel->update($id, $data)) {
            $modelErrors = $this->userModel->errors();
            if ($modelErrors) {
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user, silakan coba lagi.');
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'users',
            'data_lama' => json_encode($user),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate user: ' . $data['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil diupdate');
    }
    
    /**
     * Hapus user
     */
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan');
        }
        
        // Prevent deleting super_admin
        if ($user['role'] === 'super_admin') {
            return redirect()->to(base_url('admin/users'))->with('error', 'Super Admin tidak dapat dihapus');
        }
        
        // Prevent deleting self
        if ($id == session()->get('id_user')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Tidak dapat menghapus user sendiri');
        }
        
        $this->userModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'users',
            'data_lama' => json_encode($user),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus user: ' . $user['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil dihapus');
    }
}