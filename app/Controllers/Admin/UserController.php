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
            'users' => $users
        ];
        
        return view('admin/users/index', $data);
    }
    
    /**
     * Form tambah user
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah User',
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/users/form', $data);
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
            'role' => 'required|in_list[super_admin,admin]'
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
        
        $this->userModel->insert($data);
        
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
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/users/form', $data);
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
            'role' => 'required|in_list[super_admin,admin]',
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
        
        $this->userModel->update($id, $data);
        
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