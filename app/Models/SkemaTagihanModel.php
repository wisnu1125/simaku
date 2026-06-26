<?php

namespace App\Models;

use CodeIgniter\Model;

class SkemaTagihanModel extends Model
{
    protected $table            = 'skema_tagihan';
    protected $primaryKey       = 'id_skema_tagihan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_jenis_tagihan',
        'id_tahun_ajaran',
        'id_kelas',
        'id_siswa',
        'nominal',
        'bulan_tagihan',
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
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_jenis_tagihan' => 'required|integer',
        'id_tahun_ajaran' => 'required|integer',
        'nominal' => 'required|decimal'
    ];
    
    protected $validationMessages = [];
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
     * Get skema with relations
     */
    public function getSkemaWithRelations($idSkema = null)
    {
        $this->select('skema_tagihan.*, 
                       jenis_tagihan.nama_tagihan, 
                       jenis_tagihan.kode_tagihan, 
                       jenis_tagihan.tipe_tagihan,
                       jenis_tagihan.grup_tagihan,
                       tahun_ajaran.nama_tahun_ajaran,
                       tahun_ajaran.status as status_tahun_ajaran,
                       kelas.nama_kelas,
                       siswa.nama_lengkap as nama_siswa,
                       siswa.nis')
             ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
             ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = skema_tagihan.id_tahun_ajaran', 'left')
             ->join('kelas', 'kelas.id_kelas = skema_tagihan.id_kelas', 'left')
             ->join('siswa', 'siswa.id_siswa = skema_tagihan.id_siswa', 'left');
        
        if ($idSkema) {
            return $this->find($idSkema);
        }
        
        return $this->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                    ->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                    ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get skema grouped by grup_tagihan
     */
    public function getSkemaGrouped($idTahunAjaran = null)
    {
        $builder = $this->select('skema_tagihan.*, 
                                 jenis_tagihan.nama_tagihan, 
                                 jenis_tagihan.grup_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran,
                                 kelas.nama_kelas')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = skema_tagihan.id_tahun_ajaran', 'left')
                        ->join('kelas', 'kelas.id_kelas = skema_tagihan.id_kelas', 'left');
        
        if ($idTahunAjaran) {
            $builder->where('skema_tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        $skemaTagihan = $builder->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                               ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                               ->findAll();
        
        // Group by grup_tagihan
        $grouped = [];
        foreach ($skemaTagihan as $st) {
            $grup = $st['grup_tagihan'] ?? 'LAINNYA';
            if (!isset($grouped[$grup])) {
                $grouped[$grup] = [];
            }
            $grouped[$grup][] = $st;
        }
        
        return $grouped;
    }
    
    /**
     * Get skema by tahun ajaran
     */
    public function getByTahunAjaran($idTahunAjaran)
    {
        return $this->select('skema_tagihan.*, 
                             jenis_tagihan.nama_tagihan,
                             jenis_tagihan.grup_tagihan')
                    ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                    ->where('skema_tagihan.id_tahun_ajaran', $idTahunAjaran)
                    ->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                    ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get skema by kelas
     */
    public function getByKelas($idKelas, $idTahunAjaran)
    {
        return $this->select('skema_tagihan.*, 
                             jenis_tagihan.nama_tagihan,
                             jenis_tagihan.grup_tagihan')
                    ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                    ->where('skema_tagihan.id_kelas', $idKelas)
                    ->where('skema_tagihan.id_tahun_ajaran', $idTahunAjaran)
                    ->where('skema_tagihan.id_siswa', null)
                    ->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                    ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get skema by siswa
     */
    public function getBySiswa($idSiswa, $idTahunAjaran)
    {
        return $this->select('skema_tagihan.*, 
                             jenis_tagihan.nama_tagihan,
                             jenis_tagihan.grup_tagihan')
                    ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                    ->where('skema_tagihan.id_siswa', $idSiswa)
                    ->where('skema_tagihan.id_tahun_ajaran', $idTahunAjaran)
                    ->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                    ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get skema by grup
     */
    public function getByGrup($grup, $idTahunAjaran = null)
    {
        $builder = $this->select('skema_tagihan.*, 
                                 jenis_tagihan.nama_tagihan,
                                 jenis_tagihan.grup_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = skema_tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = skema_tagihan.id_tahun_ajaran', 'left')
                        ->where('jenis_tagihan.grup_tagihan', $grup);
        
        if ($idTahunAjaran) {
            $builder->where('skema_tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        return $builder->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                      ->findAll();
    }
    
    /**
     * Check duplicate skema
     */
    public function checkDuplicate($data, $idSkema = null)
    {
        $builder = $this->where('id_jenis_tagihan', $data['id_jenis_tagihan'])
                        ->where('id_tahun_ajaran', $data['id_tahun_ajaran']);
        
        // Check bulan tagihan for bulanan type
        if (isset($data['bulan_tagihan']) && $data['bulan_tagihan']) {
            $builder->where('bulan_tagihan', $data['bulan_tagihan']);
        }
        
        // Check kelas or siswa
        if (isset($data['id_kelas']) && $data['id_kelas']) {
            $builder->where('id_kelas', $data['id_kelas']);
        } elseif (isset($data['id_siswa']) && $data['id_siswa']) {
            $builder->where('id_siswa', $data['id_siswa']);
        }
        
        if ($idSkema) {
            $builder->where('id_skema_tagihan !=', $idSkema);
        }
        
        return $builder->first() !== null;
    }
}