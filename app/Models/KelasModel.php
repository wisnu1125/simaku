<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id_kelas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kelas',
        'tingkat',
        'id_tahun_ajaran'
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
        'nama_kelas' => 'required|min_length[2]|max_length[50]',
        'tingkat' => 'required|integer|greater_than[0]|less_than[10]',
        'id_tahun_ajaran' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'nama_kelas' => [
            'required' => 'Nama kelas harus diisi',
            'min_length' => 'Nama kelas minimal 2 karakter'
        ],
        'tingkat' => [
            'required' => 'Tingkat harus diisi',
            'integer' => 'Tingkat harus berupa angka',
            'greater_than' => 'Tingkat minimal 1',
            'less_than' => 'Tingkat maksimal 9'
        ],
        'id_tahun_ajaran' => [
            'required' => 'Tahun ajaran harus dipilih'
        ]
    ];
    
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
     * Get kelas with tahun ajaran
     */
    public function getKelasWithTahunAjaran($idKelas = null)
    {
        $this->select('kelas.*, tahun_ajaran.nama_tahun_ajaran, tahun_ajaran.status as status_tahun_ajaran')
             ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'left');
        
        if ($idKelas) {
            return $this->find($idKelas);
        }
        
        return $this->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                    ->orderBy('kelas.tingkat', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get kelas by tahun ajaran
     */
    public function getKelasByTahunAjaran($idTahunAjaran)
    {
        return $this->where('id_tahun_ajaran', $idTahunAjaran)
                    ->orderBy('tingkat', 'ASC')
                    ->findAll();
    }
    
    /**
     * Count siswa in kelas
     */
    public function countSiswa($idKelas)
    {
        $db = \Config\Database::connect();
        return $db->table('siswa')
                  ->where('id_kelas', $idKelas)
                  ->countAllResults();
    }
}