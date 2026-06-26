<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table            = 'tahun_ajaran';
    protected $primaryKey       = 'id_tahun_ajaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
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
        'nama_tahun_ajaran' => 'required|is_unique[tahun_ajaran.nama_tahun_ajaran,id_tahun_ajaran,{id_tahun_ajaran}]',
        'tanggal_mulai'     => 'required|valid_date',
        'tanggal_selesai'   => 'required|valid_date',
        'status'            => 'permit_empty|in_list[aktif,selesai,belum_aktif]'
    ];
    
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
     * Get active tahun ajaran
     */
    public function getActiveTahunAjaran()
    {
        return $this->where('status', 'aktif')->first();
    }
}