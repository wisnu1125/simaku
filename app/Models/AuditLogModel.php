<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_log';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'aksi',
        'modul',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
        'keterangan'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Get logs by user
     */
    public function getLogsByUser(int $idUser, int $limit = 50)
    {
        return $this->where('id_user', $idUser)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get logs by module
     */
    public function getLogsByModule(string $modul, int $limit = 50)
    {
        return $this->where('modul', $modul)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get recent logs
     */
    public function getRecentLogs(int $limit = 100)
    {
        return $this->select('audit_log.*, users.nama_lengkap, users.username')
                    ->join('users', 'users.id_user = audit_log.id_user', 'left')
                    ->orderBy('audit_log.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}