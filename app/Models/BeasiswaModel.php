<?php

namespace App\Models;

use CodeIgniter\Model;

class BeasiswaModel extends Model
{
    protected $table            = 'beasiswa';
    protected $primaryKey       = 'id_beasiswa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siswa',
        'id_jenis_tagihan',
        'id_tahun_ajaran',
        'nama_beasiswa',
        'tipe_beasiswa',
        'nilai_beasiswa',
        'keterangan',
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
     * Get beasiswa with relations
     */
    public function getBeasiswaWithRelations($idBeasiswa = null)
    {
        $this->select('beasiswa.*, 
                       siswa.nis, 
                       siswa.nama_lengkap as nama_siswa,
                       jenis_tagihan.nama_tagihan,
                       tahun_ajaran.nama_tahun_ajaran')
             ->join('siswa', 'siswa.id_siswa = beasiswa.id_siswa', 'left')
             ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = beasiswa.id_jenis_tagihan', 'left')
             ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = beasiswa.id_tahun_ajaran', 'left');
        
        if ($idBeasiswa) {
            return $this->find($idBeasiswa);
        }
        
        return $this->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                    ->orderBy('siswa.nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get active beasiswa by siswa
     */
    public function getBeasiswaBySiswa($idSiswa, $idTahunAjaran = null)
    {
        $builder = $this->where('id_siswa', $idSiswa)
                        ->where('status', 'aktif');
        
        if ($idTahunAjaran) {
            $builder->where('id_tahun_ajaran', $idTahunAjaran);
        }
        
        return $builder->findAll();
    }
}