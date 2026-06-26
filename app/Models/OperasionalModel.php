<?php

namespace App\Models;

use CodeIgniter\Model;

class OperasionalModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id_pengeluaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanggal',
        'kode',
        'keterangan',
        'jumlah',
        'satuan',
        'harga_satuan',
        'total',
        'id_user'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    
    // ========================================
    // PENGELUARAN METHODS
    // ========================================
    
    /**
     * Get pengeluaran with user info
     */
    public function getPengeluaranWithUser($idPengeluaran = null)
    {
        $this->select('pengeluaran.*, users.nama_lengkap as nama_user')
             ->join('users', 'users.id_user = pengeluaran.id_user', 'left');
        
        if ($idPengeluaran) {
            return $this->find($idPengeluaran);
        }
        
        return $this->orderBy('pengeluaran.tanggal', 'DESC')
                    ->orderBy('pengeluaran.created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get pengeluaran by date range
     */
    public function getPengeluaranByPeriode($start, $end)
    {
        return $this->select('pengeluaran.*, users.nama_lengkap as nama_user')
                    ->join('users', 'users.id_user = pengeluaran.id_user', 'left')
                    ->where('pengeluaran.tanggal >=', $start)
                    ->where('pengeluaran.tanggal <=', $end)
                    ->orderBy('pengeluaran.kode', 'ASC')
                    ->orderBy('pengeluaran.tanggal', 'ASC')
                    ->orderBy('pengeluaran.created_at', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get total pengeluaran by periode
     */
    public function getTotalPengeluaranByPeriode($start, $end)
    {
        $result = $this->selectSum('total')
                       ->where('tanggal >=', $start)
                       ->where('tanggal <=', $end)
                       ->get()
                       ->getRow();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get pengeluaran grouped by kode
     */
    public function getPengeluaranGroupedByKode($start, $end)
    {
        $db = \Config\Database::connect();
        return $db->table('pengeluaran')
                  ->select('kode, COUNT(*) as jumlah_item, SUM(total) as total_pengeluaran')
                  ->where('tanggal >=', $start)
                  ->where('tanggal <=', $end)
                  ->groupBy('kode')
                  ->orderBy('kode', 'ASC')
                  ->get()
                  ->getResultArray();
    }
    
    // ========================================
    // SALDO OPERASIONAL METHODS
    // ========================================
    
    /**
     * Get all saldo
     */
    public function getAllSaldo()
    {
        $db = \Config\Database::connect();
        return $db->table('saldo_operasional')
                  ->orderBy('tanggal_masuk', 'DESC')
                  ->get()
                  ->getResultArray();
    }
    
    /**
     * Get saldo by ID
     */
    public function getSaldoById($id)
    {
        $db = \Config\Database::connect();
        return $db->table('saldo_operasional')
                  ->where('id_saldo', $id)
                  ->get()
                  ->getRowArray();
    }
    
    /**
     * Insert saldo baru
     */
    public function insertSaldo($data)
    {
        $db = \Config\Database::connect();
        return $db->table('saldo_operasional')->insert($data);
    }
    
    /**
     * Delete saldo
     */
    public function deleteSaldo($id)
    {
        $db = \Config\Database::connect();
        return $db->table('saldo_operasional')
                  ->where('id_saldo', $id)
                  ->delete();
    }
    
    /**
     * Update saldo tersisa
     */
    public function updateSaldoTersisa($id, $saldoTersisa)
    {
        $db = \Config\Database::connect();
        return $db->table('saldo_operasional')
                  ->where('id_saldo', $id)
                  ->update(['saldo_tersisa' => $saldoTersisa]);
    }
    
    /**
     * Get total saldo tersisa
     */
    public function getTotalSaldoTersisa()
    {
        $db = \Config\Database::connect();
        $result = $db->table('saldo_operasional')
                     ->selectSum('saldo_tersisa')
                     ->get()
                     ->getRow();
        
        return $result->saldo_tersisa ?? 0;
    }
    
    /**
     * Kurangi saldo berdasarkan pengeluaran (FIFO)
     */
    public function kurangiSaldo($nominal)
    {
        $db = \Config\Database::connect();
        
        // Ambil semua saldo terurut dari yang terlama
        $saldoList = $db->table('saldo_operasional')
                        ->where('saldo_tersisa >', 0)
                        ->orderBy('tanggal_masuk', 'ASC')
                        ->get()
                        ->getResultArray();
        
        $sisaNominal = $nominal;
        
        foreach ($saldoList as $saldo) {
            if ($sisaNominal <= 0) {
                break;
            }
            
            if ($saldo['saldo_tersisa'] >= $sisaNominal) {
                // Saldo cukup
                $db->table('saldo_operasional')
                   ->where('id_saldo', $saldo['id_saldo'])
                   ->update(['saldo_tersisa' => $saldo['saldo_tersisa'] - $sisaNominal]);
                $sisaNominal = 0;
            } else {
                // Saldo tidak cukup, habiskan saldo ini
                $sisaNominal -= $saldo['saldo_tersisa'];
                $db->table('saldo_operasional')
                   ->where('id_saldo', $saldo['id_saldo'])
                   ->update(['saldo_tersisa' => 0]);
            }
        }
        
        return $sisaNominal == 0;
    }
    
    /**
     * Tambah kembali saldo (saat pengeluaran dihapus)
     */
    public function tambahSaldo($nominal)
    {
        $db = \Config\Database::connect();
        
        // Ambil saldo terbaru
        $saldoTerbaru = $db->table('saldo_operasional')
                           ->orderBy('tanggal_masuk', 'DESC')
                           ->get()
                           ->getRowArray();
        
        if ($saldoTerbaru) {
            $db->table('saldo_operasional')
               ->where('id_saldo', $saldoTerbaru['id_saldo'])
               ->update(['saldo_tersisa' => $saldoTerbaru['saldo_tersisa'] + $nominal]);
            return true;
        }
        
        return false;
    }
}