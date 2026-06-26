<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id_siswa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nis',
        'nisn',
        'nama_lengkap',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'nama_wali',
        'telp_wali',
        'id_kelas',
        'virtual_account',
        'status_siswa'
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
    // NOTE: Validasi di model ini tidak dipakai karena validasi dilakukan di Controller
    // Agar bisa handle VA optional saat insert dan required saat update
    protected $validationRules = [];
    
    protected $validationMessages = [];
    protected $skipValidation     = true; // Skip validation di model, pakai di controller
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
     * Get siswa with kelas info
     */
    public function getSiswaWithKelas($idSiswa = null)
    {
        $this->select('siswa.*, kelas.nama_kelas, kelas.tingkat')
             ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left');
        
        if ($idSiswa) {
            return $this->find($idSiswa);
        }
        
        return $this->findAll();
    }
    
    /**
     * Search siswa
     */
    public function searchSiswa($keyword, $limit = 10)
    {
        return $this->select('siswa.*, kelas.nama_kelas')
                    ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
                    ->groupStart()
                        ->like('siswa.nis', $keyword)
                        ->orLike('siswa.nama_lengkap', $keyword)
                        ->orLike('siswa.virtual_account', $keyword)
                    ->groupEnd()
                    ->where('siswa.status_siswa', 'aktif')
                    ->limit($limit)
                    ->findAll();
    }
}