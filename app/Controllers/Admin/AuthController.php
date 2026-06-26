<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AuthService;

class AuthController extends BaseController
{
    protected $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    /**
     * Halaman login
     */
    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }
        
        return view('admin/auth/login');
    }
    
    /**
     * Proses login
     */
    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Validasi input
        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password harus diisi');
        }
        
        // Proses login
        $result = $this->authService->login($username, $password);
        
        if ($result['success']) {
            return redirect()->to(base_url('admin/dashboard'))->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        $this->authService->logout();
        return redirect()->to(base_url('admin/login'))->with('success', 'Anda berhasil logout');
    }
}