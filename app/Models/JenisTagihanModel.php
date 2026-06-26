<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisTagihanModel extends Model
{
    protected $table            = 'jenis_tagihan';
    protected $primaryKey       = 'id_jenis_tagihan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_tagihan',
        'kode_tagihan',
        'tipe_tagihan',
        'grup_tagihan',
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

    // Validation - DISABLED (Validasi dilakukan di Controller)
    protected $validationRules = [];
    
    protected $validationMessages = [];
    
    protected $skipValidation = true;
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
     * Get active jenis tagihan
     */
    public function getActiveJenisTagihan()
    {
        return $this->where('status', 'aktif')
                    ->orderBy('grup_tagihan', 'ASC')
                    ->orderBy('nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get by tipe
     */
    public function getByTipe(string $tipe)
    {
        return $this->where('tipe_tagihan', $tipe)
                    ->where('status', 'aktif')
                    ->orderBy('grup_tagihan', 'ASC')
                    ->orderBy('nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get grouped by grup_tagihan
     */
    public function getGroupedJenisTagihan()
    {
        $jenisTagihan = $this->orderBy('grup_tagihan', 'ASC')
                             ->orderBy('nama_tagihan', 'ASC')
                             ->findAll();
        
        $grouped = [];
        foreach ($jenisTagihan as $jt) {
            $grup = $jt['grup_tagihan'] ?? 'LAINNYA';
            if (!isset($grouped[$grup])) {
                $grouped[$grup] = [];
            }
            $grouped[$grup][] = $jt;
        }
        
        return $grouped;
    }
    
    /**
     * Get by grup
     */
    public function getByGrup(string $grup)
    {
        return $this->where('grup_tagihan', $grup)
                    ->where('status', 'aktif')
                    ->orderBy('nama_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get all grup tagihan (unique)
     */
    public function getAllGrup()
    {
        return $this->select('grup_tagihan')
                    ->where('grup_tagihan IS NOT NULL')
                    ->groupBy('grup_tagihan')
                    ->orderBy('grup_tagihan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Count usage in skema tagihan
     */
    public function countUsageInSkema($idJenisTagihan)
    {
        $db = \Config\Database::connect();
        return $db->table('skema_tagihan')
                  ->where('id_jenis_tagihan', $idJenisTagihan)
                  ->countAllResults();
    }
    
    /**
     * Count usage in tagihan
     */
    public function countUsageInTagihan($idJenisTagihan)
    {
        $db = \Config\Database::connect();
        return $db->table('tagihan')
                  ->where('id_jenis_tagihan', $idJenisTagihan)
                  ->countAllResults();
    }
}