<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id_pembayaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tagihan',
        'nomor_kwitansi',
        'tanggal_bayar',
        'nominal_bayar',
        'metode_pembayaran',
        'payment_channel',
        'xendit_invoice_id',
        'keterangan',
        'status_pembayaran',
        'alasan_batal',
        'tanggal_batal',
        'dibatalkan_oleh',
        'id_user'
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
     * Get pembayaran by tagihan
     */
    public function getPembayaranByTagihan($idTagihan)
    {
        return $this->select('pembayaran.*, users.nama_lengkap as nama_petugas')
                    ->join('users', 'users.id_user = pembayaran.id_user', 'left')
                    ->where('id_tagihan', $idTagihan)
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get pembayaran by siswa
     */
    public function getPembayaranBySiswa($idSiswa)
    {
        return $this->select('pembayaran.*, 
                             tagihan.nominal_akhir,
                             jenis_tagihan.nama_tagihan,
                             tahun_ajaran.nama_tahun_ajaran')
                    ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                    ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                    ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                    ->where('tagihan.id_siswa', $idSiswa)
                    ->orderBy('pembayaran.tanggal_bayar', 'DESC')
                    ->findAll();
    }
}