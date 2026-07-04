<?php

namespace App\Services;

use App\Models\TagihanModel;
use App\Models\SkemaTagihanModel;
use App\Models\BeasiswaModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AuditLogModel;

class TagihanService
{
    protected $tagihanModel;
    protected $skemaTagihanModel;
    protected $beasiswaModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->tagihanModel = new TagihanModel();
        $this->skemaTagihanModel = new SkemaTagihanModel();
        $this->beasiswaModel = new BeasiswaModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * Generate tagihan dari skema
     */
    public function generateTagihan($idTahunAjaran, $idKelas = null, $idSiswa = null, $idUser = null)
    {
        $generatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        
        try {
            // Get skema tagihan
            $builder = $this->skemaTagihanModel->where('id_tahun_ajaran', $idTahunAjaran);
            
            if ($idKelas) {
                $builder->groupStart()
                        ->where('id_kelas', $idKelas)
                        ->orWhere('id_kelas', null)
                        ->groupEnd();
            }
            
            if ($idSiswa) {
                $builder->where('id_siswa', $idSiswa);
            }
            
            $skemas = $builder->findAll();
            
            if (empty($skemas)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada skema tagihan yang ditemukan',
                    'generated' => 0,
                    'skipped' => 0
                ];
            }
            
            // Get siswa yang akan digenerate
            $siswaBuilder = $this->siswaModel->where('status_siswa', 'aktif');
            
            if ($idKelas) {
                $siswaBuilder->where('id_kelas', $idKelas);
            }
            
            if ($idSiswa) {
                $siswaBuilder->where('id_siswa', $idSiswa);
            }
            
            $siswaList = $siswaBuilder->findAll();
            
            if (empty($siswaList)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada siswa aktif yang ditemukan',
                    'generated' => 0,
                    'skipped' => 0
                ];
            }
            
            // Generate tagihan untuk setiap siswa
            foreach ($siswaList as $siswa) {
                foreach ($skemas as $skema) {
                    // Skip jika skema untuk kelas tertentu dan siswa bukan dari kelas tersebut
                    if ($skema['id_kelas'] && $siswa['id_kelas'] != $skema['id_kelas']) {
                        continue;
                    }
                    
                    // Skip jika skema untuk siswa tertentu dan bukan siswa ini
                    if ($skema['id_siswa'] && $siswa['id_siswa'] != $skema['id_siswa']) {
                        continue;
                    }
                    
                    // Check apakah tagihan sudah ada
                    $existing = $this->tagihanModel
                        ->where('id_siswa', $siswa['id_siswa'])
                        ->where('id_jenis_tagihan', $skema['id_jenis_tagihan'])
                        ->where('id_tahun_ajaran', $idTahunAjaran)
                        ->where('bulan_tagihan', $skema['bulan_tagihan'])
                        ->first();
                    
                    if ($existing) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // Calculate beasiswa if any
                    $nominalTagihan = $skema['nominal'];
                    $nominalPotongan = $this->calculateBeasiswa($siswa['id_siswa'], $skema['id_jenis_tagihan'], $idTahunAjaran, $nominalTagihan);
                    $nominalAkhir = max(0, $nominalTagihan - $nominalPotongan);
                    
                    // FIX: sebelumnya status_tagihan selalu di-hardcode 'belum_bayar' saat generate,
                    // padahal kalau beasiswa sudah menanggung 100% (nominal_akhir = 0) seharusnya
                    // langsung 'lunas' karena tidak ada yang perlu dibayar. Tanpa ini, tagihan yang
                    // sudah gratis tetap nyangkut sebagai "belum bayar" di laporan tunggakan.
                    $statusTagihan = $nominalAkhir <= 0 ? 'lunas' : 'belum_bayar';
                    
                    // Insert tagihan
                    $dataTagihan = [
                        'id_siswa' => $siswa['id_siswa'],
                        'id_jenis_tagihan' => $skema['id_jenis_tagihan'],
                        'id_tahun_ajaran' => $idTahunAjaran,
                        'id_kelas' => $siswa['id_kelas'],
                        'nominal_tagihan' => $nominalTagihan,
                        'nominal_potongan' => $nominalPotongan,
                        'nominal_akhir' => $nominalAkhir,
                        'nominal_dibayar' => 0,
                        'sisa_tagihan' => $nominalAkhir,
                        'bulan_tagihan' => $skema['bulan_tagihan'],
                        'status_tagihan' => $statusTagihan,
                        'keterangan' => $skema['keterangan']
                    ];
                    
                    $this->tagihanModel->insert($dataTagihan);
                    $generatedCount++;
                }
            }
            
            // Audit log
            if ($idUser) {
                $this->auditLogModel->insert([
                    'id_user' => $idUser,
                    'aksi' => 'generate',
                    'modul' => 'tagihan',
                    'data_baru' => json_encode([
                        'id_tahun_ajaran' => $idTahunAjaran,
                        'id_kelas' => $idKelas,
                        'id_siswa' => $idSiswa,
                        'generated' => $generatedCount,
                        'skipped' => $skippedCount
                    ]),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                    'keterangan' => "Generate tagihan: {$generatedCount} berhasil, {$skippedCount} dilewati"
                ]);
            }
            
            return [
                'success' => true,
                'message' => "Berhasil generate {$generatedCount} tagihan" . ($skippedCount > 0 ? ", {$skippedCount} tagihan sudah ada" : ""),
                'generated' => $generatedCount,
                'skipped' => $skippedCount
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'generated' => $generatedCount,
                'skipped' => $skippedCount
            ];
        }
    }
    
    /**
     * Calculate beasiswa untuk siswa
     */
    private function calculateBeasiswa($idSiswa, $idJenisTagihan, $idTahunAjaran, $nominalTagihan)
    {
        $beasiswa = $this->beasiswaModel
            ->where('id_siswa', $idSiswa)
            ->where('id_jenis_tagihan', $idJenisTagihan)
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->where('status', 'aktif')
            ->findAll();
        
        $totalPotongan = 0;
        
        foreach ($beasiswa as $b) {
            if ($b['tipe_beasiswa'] === 'nominal') {
                $totalPotongan += $b['nilai_beasiswa'];
            } else { // persentase
                $totalPotongan += ($nominalTagihan * $b['nilai_beasiswa'] / 100);
            }
        }
        
        // Pastikan potongan tidak melebihi nominal
        return min($totalPotongan, $nominalTagihan);
    }
    
    /**
     * Update status tagihan berdasarkan pembayaran
     */
    public function updateStatusTagihan($idTagihan)
    {
        $tagihan = $this->tagihanModel->find($idTagihan);
        
        if (!$tagihan) {
            return false;
        }
        
        $sisaTagihan = $tagihan['nominal_akhir'] - $tagihan['nominal_dibayar'];
        
        if ($sisaTagihan <= 0) {
            $status = 'lunas';
        } elseif ($tagihan['nominal_dibayar'] > 0) {
            $status = 'cicil';
        } else {
            $status = 'belum_bayar';
        }
        
        $this->tagihanModel->update($idTagihan, [
            'sisa_tagihan' => max(0, $sisaTagihan),
            'status_tagihan' => $status
        ]);
        
        return true;
    }
}