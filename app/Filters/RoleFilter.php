<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');
        
        // Jika tidak ada argument, berarti semua role boleh akses
        if (empty($arguments)) {
            return;
        }
        
        // Cek apakah role user ada di daftar yang diizinkan
        if (!in_array($userRole, $arguments)) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}