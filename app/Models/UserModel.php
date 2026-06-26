<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'role',
        'status',
        'last_login'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username'     => 'required|min_length[3]|max_length[50]|is_unique[users.username,id_user,{id_user}]',
        'password'     => 'required|min_length[6]',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'email'        => 'permit_empty|valid_email|is_unique[users.email,id_user,{id_user}]',
        'role'         => 'required|in_list[super_admin,tu_bendahara]',
        'status'       => 'required|in_list[aktif,nonaktif]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required'   => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'is_unique'  => 'Username sudah digunakan'
        ],
        'password' => [
            'required'   => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap harus diisi'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
            'is_unique'   => 'Email sudah digunakan'
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list'  => 'Role tidak valid'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers()
    {
        return $this->where('status', 'aktif')->findAll();
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole(string $role)
    {
        return $this->where('role', $role)->findAll();
    }
}