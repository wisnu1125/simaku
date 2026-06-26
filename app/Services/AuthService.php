<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\AuditLogModel;

class AuthService
{
    protected $userModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * Login user
     */
    public function login(string $username, string $password): array
    {
        // Cari user berdasarkan username
        $user = $this->userModel->where('username', $username)->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan'
            ];
        }
        
        // Cek status user
        if ($user['status'] !== 'aktif') {
            return [
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator'
            ];
        }
        
        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Password salah'
            ];
        }
        
        // Update last login
        $this->userModel->update($user['id_user'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);
        
        // Set session
        $sessionData = [
            'logged_in'    => true,
            'id_user'      => $user['id_user'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'email'        => $user['email'],
            'role'         => $user['role']
        ];
        
        session()->set($sessionData);
        
        // Log audit
        $this->auditLogModel->insert([
            'id_user'     => $user['id_user'],
            'aksi'        => 'login',
            'modul'       => 'auth',
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'keterangan'  => 'User berhasil login'
        ]);
        
        return [
            'success' => true,
            'message' => 'Login berhasil',
            'user'    => $user
        ];
    }
    
    /**
     * Logout user
     */
    public function logout(): void
    {
        $idUser = session()->get('id_user');
        
        // Log audit
        if ($idUser) {
            $this->auditLogModel->insert([
                'id_user'     => $idUser,
                'aksi'        => 'logout',
                'modul'       => 'auth',
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'keterangan'  => 'User logout'
            ]);
        }
        
        // Destroy session
        session()->destroy();
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser(): ?array
    {
        $idUser = session()->get('id_user');
        
        if (!$idUser) {
            return null;
        }
        
        return $this->userModel->find($idUser);
    }
    
    /**
     * Check if user has role
     */
    public function hasRole(string $role): bool
    {
        return session()->get('role') === $role;
    }
    
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }
}