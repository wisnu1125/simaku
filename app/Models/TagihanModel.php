<?php

namespace App\Models;

use CodeIgniter\Model;

class TagihanModel extends Model
{
    protected $table            = 'tagihan';
    protected $primaryKey       = 'id_tagihan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siswa',
        'id_jenis_tagihan',
        'id_tahun_ajaran',
        'id_kelas',
        'nominal_tagihan',
        'nominal_potongan',
        'nominal_akhir',
        'nominal_dibayar',
        'sisa_tagihan',
        'bulan_tagihan',
        'status_tagihan',
        'jatuh_tempo',
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
     * Get tagihan with relations
     */
    public function getTagihanWithRelations($idTagihan = null)
    {
        $this->select('tagihan.*, 
                       siswa.nis,
                       siswa.nama_lengkap as nama_siswa,
                       jenis_tagihan.nama_tagihan,
                       jenis_tagihan.kode_tagihan,
                       jenis_tagihan.tipe_tagihan,
                       jenis_tagihan.grup_tagihan,
                       tahun_ajaran.nama_tahun_ajaran,
                       kelas.nama_kelas')
             ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
             ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
             ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
             ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left');
        
        if ($idTagihan) {
            return $this->find($idTagihan);
        }
        
        return $this->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                    ->orderBy('siswa.nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get tagihan by siswa
     * PERBAIKAN: Menambahkan grup_tagihan dan tipe_tagihan agar grouping di View bekerja
     */
    public function getTagihanBySiswa($idSiswa, $idTahunAjaran = null)
    {
        $builder = $this->select('tagihan.*, 
                                  jenis_tagihan.nama_tagihan,
                                  jenis_tagihan.kode_tagihan,
                                  jenis_tagihan.tipe_tagihan,
                                  jenis_tagihan.grup_tagihan,
                                  tahun_ajaran.nama_tahun_ajaran')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                        ->where('tagihan.id_siswa', $idSiswa);
        
        if ($idTahunAjaran) {
            $builder->where('tagihan.id_tahun_ajaran', $idTahunAjaran);
        }
        
        // Sorting disesuaikan agar grouping rapi (Grup dulu, baru Nama)
        return $builder->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                       ->orderBy('jenis_tagihan.grup_tagihan', 'ASC')
                       ->orderBy('jenis_tagihan.nama_tagihan', 'ASC')
                       ->findAll();
    }
    
    /**
     * Add payment
     */
    public function addPayment($idTagihan, $nominalBayar)
    {
        $tagihan = $this->find($idTagihan);
        
        if (!$tagihan) {
            return false;
        }
        
        $nominalDibayarBaru = $tagihan['nominal_dibayar'] + $nominalBayar;
        $sisaTagihan = max(0, $tagihan['nominal_akhir'] - $nominalDibayarBaru);
        
        // Update status
        if ($sisaTagihan <= 0) {
            $status = 'lunas';
        } elseif ($nominalDibayarBaru > 0) {
            $status = 'cicil';
        } else {
            $status = 'belum_bayar';
        }
        
        return $this->update($idTagihan, [
            'nominal_dibayar' => $nominalDibayarBaru,
            'sisa_tagihan' => $sisaTagihan,
            'status_tagihan' => $status
        ]);
    }
    
    /**
     * Cancel payment
     */
    public function cancelPayment($idTagihan, $nominalBayar)
    {
        $tagihan = $this->find($idTagihan);
        
        if (!$tagihan) {
            return false;
        }
        
        $nominalDibayarBaru = max(0, $tagihan['nominal_dibayar'] - $nominalBayar);
        $sisaTagihan = $tagihan['nominal_akhir'] - $nominalDibayarBaru;
        
        // Update status
        if ($sisaTagihan <= 0) {
            $status = 'lunas';
        } elseif ($nominalDibayarBaru > 0) {
            $status = 'cicil';
        } else {
            $status = 'belum_bayar';
        }
        
        return $this->update($idTagihan, [
            'nominal_dibayar' => $nominalDibayarBaru,
            'sisa_tagihan' => $sisaTagihan,
            'status_tagihan' => $status
        ]);
    }
}