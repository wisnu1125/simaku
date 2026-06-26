<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel;
use App\Models\AuditLogModel;

class KenaikanKelasController extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $tahunAjaranModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * Index kenaikan kelas
     */
    public function index()
    {
        $idTahunAjaran = $this->request->getGet('id_tahun_ajaran');
        $idKelas = $this->request->getGet('id_kelas');
        
        $data = [
            'title' => 'Kenaikan Kelas',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'kelas' => $this->kelasModel->getKelasWithTahunAjaran(),
            'id_tahun_ajaran' => $idTahunAjaran,
            'id_kelas' => $idKelas
        ];
        
        // If filters selected, get siswa list
        if ($idKelas) {
            $kelas = $this->kelasModel
                          ->select('kelas.*, tahun_ajaran.nama_tahun_ajaran')
                          ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'left')
                          ->where('kelas.id_kelas', $idKelas)
                          ->first();
            
            $siswa = $this->siswaModel
                          ->where('id_kelas', $idKelas)
                          ->where('status_siswa', 'aktif')
                          ->orderBy('nama_lengkap', 'ASC')
                          ->findAll();
            
            $data['kelas_detail'] = $kelas;
            $data['siswa'] = $siswa;
        }
        
        return view('admin/kenaikan_kelas/index', $data);
    }
    
    /**
     * Form kenaikan kelas
     */
    public function form()
    {
        $idKelas = $this->request->getGet('id_kelas');
        
        if (!$idKelas) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Pilih kelas terlebih dahulu');
        }
        
        $kelas = $this->kelasModel
                      ->select('kelas.*, tahun_ajaran.nama_tahun_ajaran')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'left')
                      ->where('kelas.id_kelas', $idKelas)
                      ->first();
        
        if (!$kelas) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Kelas tidak ditemukan');
        }
        
        $siswa = $this->siswaModel
                      ->where('id_kelas', $idKelas)
                      ->where('status_siswa', 'aktif')
                      ->orderBy('nama_lengkap', 'ASC')
                      ->findAll();
        
        // Get kelas tujuan (tahun ajaran berikutnya)
        $kelasTujuan = $this->kelasModel->getKelasWithTahunAjaran();
        
        $data = [
            'title' => 'Kenaikan Kelas',
            'kelas' => $kelas,
            'siswa' => $siswa,
            'kelas_tujuan' => $kelasTujuan
        ];
        
        return view('admin/kenaikan_kelas/form', $data);
    }
    
    /**
     * Proses kenaikan kelas
     */
    public function proses()
    {
        $idKelasAsal = $this->request->getPost('id_kelas_asal');
        $idKelasTujuan = $this->request->getPost('id_kelas_tujuan');
        $siswaIds = $this->request->getPost('siswa_ids');
        
        if (!$idKelasAsal || !$idKelasTujuan) {
            return redirect()->back()->with('error', 'Kelas asal dan tujuan harus dipilih');
        }
        
        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 siswa');
        }
        
        // Validate kelas tujuan
        $kelasTujuan = $this->kelasModel->find($idKelasTujuan);
        if (!$kelasTujuan) {
            return redirect()->back()->with('error', 'Kelas tujuan tidak ditemukan');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $jumlahBerhasil = 0;
        $jumlahGagal = 0;
        
        foreach ($siswaIds as $idSiswa) {
            $siswa = $this->siswaModel->find($idSiswa);
            
            if ($siswa && $siswa['id_kelas'] == $idKelasAsal) {
                // Update kelas siswa
                $this->siswaModel->update($idSiswa, [
                    'id_kelas' => $idKelasTujuan
                ]);
                
                // Audit log
                $this->auditLogModel->insert([
                    'id_user' => session()->get('id_user'),
                    'aksi' => 'kenaikan_kelas',
                    'modul' => 'siswa',
                    'data_lama' => json_encode(['id_kelas' => $siswa['id_kelas']]),
                    'data_baru' => json_encode(['id_kelas' => $idKelasTujuan]),
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    'keterangan' => 'Kenaikan kelas siswa: ' . $siswa['nama_lengkap']
                ]);
                
                $jumlahBerhasil++;
            } else {
                $jumlahGagal++;
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Gagal melakukan kenaikan kelas');
        }
        
        return redirect()->to(base_url('admin/kenaikan-kelas'))->with('success', "Kenaikan kelas berhasil! {$jumlahBerhasil} siswa naik kelas" . ($jumlahGagal > 0 ? ", {$jumlahGagal} gagal" : ""));
    }
    
    /**
     * Kelulusan siswa (pindah ke status lulus)
     */
    public function kelulusan()
    {
        $idKelas = $this->request->getGet('id_kelas');
        
        if (!$idKelas) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Pilih kelas terlebih dahulu');
        }
        
        $kelas = $this->kelasModel
                      ->select('kelas.*, tahun_ajaran.nama_tahun_ajaran')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'left')
                      ->where('kelas.id_kelas', $idKelas)
                      ->first();
        
        if (!$kelas) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Kelas tidak ditemukan');
        }
        
        $siswa = $this->siswaModel
                      ->where('id_kelas', $idKelas)
                      ->where('status_siswa', 'aktif')
                      ->orderBy('nama_lengkap', 'ASC')
                      ->findAll();
        
        $data = [
            'title' => 'Kelulusan Siswa',
            'kelas' => $kelas,
            'siswa' => $siswa
        ];
        
        return view('admin/kenaikan_kelas/kelulusan', $data);
    }
    
    /**
     * Proses kelulusan
     */
    public function prosesKelulusan()
    {
        $idKelas = $this->request->getPost('id_kelas');
        $siswaIds = $this->request->getPost('siswa_ids');
        
        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 siswa');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $jumlahBerhasil = 0;
        
        foreach ($siswaIds as $idSiswa) {
            $siswa = $this->siswaModel->find($idSiswa);
            
            if ($siswa && $siswa['id_kelas'] == $idKelas) {
                // Update status siswa ke lulus dan kosongkan kelas
                $this->siswaModel->update($idSiswa, [
                    'status_siswa' => 'lulus',
                    'id_kelas' => null
                ]);
                
                // Audit log
                $this->auditLogModel->insert([
                    'id_user' => session()->get('id_user'),
                    'aksi' => 'kelulusan',
                    'modul' => 'siswa',
                    'data_lama' => json_encode(['status_siswa' => 'aktif', 'id_kelas' => $siswa['id_kelas']]),
                    'data_baru' => json_encode(['status_siswa' => 'lulus', 'id_kelas' => null]),
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    'keterangan' => 'Kelulusan siswa: ' . $siswa['nama_lengkap']
                ]);
                
                $jumlahBerhasil++;
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->to(base_url('admin/kenaikan-kelas'))->with('error', 'Gagal melakukan kelulusan');
        }
        
        return redirect()->to(base_url('admin/kenaikan-kelas'))->with('success', "Kelulusan berhasil! {$jumlahBerhasil} siswa lulus");
    }
}