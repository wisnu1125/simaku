<?php

namespace App\Services;

use App\Models\SiswaModel;

class SiswaService
{
    protected $siswaModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }
    
    /**
     * Generate Virtual Account unik
     * Format: VA + 12 digit angka
     */
    public function generateVirtualAccount(): string
    {
        do {
            // Generate 12 digit random number
            $vaNumber = 'VA' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
            
            // Cek apakah VA sudah ada
            $exists = $this->siswaModel->where('virtual_account', $vaNumber)->first();
        } while ($exists);
        
        return $vaNumber;
    }
    
    /**
     * Validate NIS unique
     */
    public function isNISUnique(string $nis, $idSiswa = null): bool
    {
        $query = $this->siswaModel->where('nis', $nis);
        
        if ($idSiswa) {
            $query->where('id_siswa !=', $idSiswa);
        }
        
        return $query->first() === null;
    }
    
    /**
     * Validate NISN unique
     */
    public function isNISNUnique(string $nisn, $idSiswa = null): bool
    {
        if (empty($nisn)) {
            return true;
        }
        
        $query = $this->siswaModel->where('nisn', $nisn);
        
        if ($idSiswa) {
            $query->where('id_siswa !=', $idSiswa);
        }
        
        return $query->first() === null;
    }
}