<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\TagihanModel;
use App\Models\SiswaModel;
use App\Models\AuditLogModel;

class PembayaranController extends BaseController
{
    protected $pembayaranModel;
    protected $tagihanModel;
    protected $siswaModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->tagihanModel = new TagihanModel();
        $this->siswaModel = new SiswaModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List pembayaran
     */
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $filterStatus = $this->request->getGet('filter_status');
        $filterMetode = $this->request->getGet('filter_metode');
        
        $applyFilters = function ($model) use ($keyword, $filterStatus, $filterMetode) {
            if ($keyword) {
                $model->groupStart()
                      ->like('siswa.nis', $keyword)
                      ->orLike('siswa.nama_lengkap', $keyword)
                      ->orLike('pembayaran.nomor_kwitansi', $keyword)
                      ->groupEnd();
            }
            if ($filterStatus) {
                $model->where('pembayaran.status_pembayaran', $filterStatus);
            }
            if ($filterMetode) {
                $model->where('pembayaran.metode_pembayaran', $filterMetode);
            }
            return $model;
        };
        
        // Kalau dipanggil lewat AJAX (fetch dari halaman index), balas JSON dengan pagination di server
        // -- jangan kirim SEMUA riwayat pembayaran sekaligus, karena baris ini terus bertambah tiap hari.
        if ($this->request->isAJAX()) {
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 15)));
            
            // Query baris untuk halaman ini (instance model sendiri)
            $listModel = new PembayaranModel();
            $listModel->select('pembayaran.*, 
                                 tagihan.nominal_akhir,
                                 tagihan.sisa_tagihan as sisa_tagihan_sekarang,
                                 siswa.nis,
                                 siswa.nama_lengkap as nama_siswa,
                                 jenis_tagihan.nama_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran,
                                 users.nama_lengkap as nama_petugas')
                      ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                      ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                      ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                      ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                      ->join('users', 'users.id_user = pembayaran.id_user', 'left');
            $applyFilters($listModel);
            
            // false = jangan reset query builder, supaya where/join di atas masih kepakai buat limit() di bawah
            $total = $listModel->countAllResults(false);
            $rows  = $listModel->orderBy('pembayaran.tanggal_bayar', 'DESC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->findAll();
            
            // Statistik mengikuti filter yang sama, tapi lewat query terpisah & independen
            // (bukan numpang di $listModel yang sudah kepakai limit/count di atas), dan
            // dihitung SUM/COUNT langsung di database (bukan fetch semua baris ke PHP).
            $statsModel = new PembayaranModel();
            $statsModel->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                       ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left');
            $applyFilters($statsModel);
            $statsRows = $statsModel
                ->select('pembayaran.status_pembayaran, SUM(pembayaran.nominal_bayar) as total, COUNT(*) as jumlah')
                ->groupBy('pembayaran.status_pembayaran')
                ->findAll();
            
            $totalValid = 0; $countValid = 0; $totalBatal = 0;
            foreach ($statsRows as $r) {
                if ($r['status_pembayaran'] === 'valid') { $totalValid = (float) $r['total']; $countValid = (int) $r['jumlah']; }
                else { $totalBatal += (float) $r['total']; }
            }
            
            return $this->response->setJSON([
                'rows'        => $rows,
                'total'       => $total,
                'page'        => $page,
                'per_page'    => $perPage,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
                'stats'       => [
                    'total_valid' => $totalValid,
                    'count_valid' => $countValid,
                    'total_batal' => $totalBatal,
                ],
            ]);
        }
        
        $data = [
            'title' => 'Pembayaran',
            'keyword' => $keyword,
            'filter_status' => $filterStatus,
            'filter_metode' => $filterMetode
        ];
        
        return view('admin/pembayaran/index', $data);
    }
    
    /**
     * Form input pembayaran.
     * UPDATE: form sudah jadi modal di halaman index (sama seperti Siswa), jadi URL lama
     * ini tinggal dialihkan supaya bookmark/tautan lama (termasuk tombol "Bayar" dari
     * drawer Siswa & shortcut Dashboard) tetap jalan dan modalnya otomatis terbuka.
     */
    public function create()
    {
        $idSiswa = $this->request->getGet('id_siswa');
        $hash = $idSiswa ? '#bayar-' . (int) $idSiswa : '#bayar';
        return redirect()->to(base_url('admin/pembayaran' . $hash));
    }
    
    /**
     * Proses input pembayaran (single)
     */
    public function store()
    {
        $rules = [
            'id_tagihan' => 'required|integer',
            'nominal_bayar' => 'required|decimal',
            'metode_pembayaran' => 'required|in_list[tunai,transfer]',
            'tanggal_bayar' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $idTagihan = $this->request->getPost('id_tagihan');
        $nominalBayar = $this->request->getPost('nominal_bayar');
        
        // Get tagihan
        $tagihan = $this->tagihanModel->find($idTagihan);
        
        if (!$tagihan) {
            return redirect()->back()->withInput()->with('error', 'Tagihan tidak ditemukan');
        }
        
        // Validasi nominal bayar
        if ($nominalBayar <= 0) {
            return redirect()->back()->withInput()->with('error', 'Nominal bayar harus lebih dari 0');
        }
        
        if ($nominalBayar > $tagihan['sisa_tagihan']) {
            return redirect()->back()->withInput()->with('error', 'Nominal bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($tagihan['sisa_tagihan'], 0, ',', '.') . ')');
        }
        
        // Generate nomor kwitansi
        $nomorKwitansi = $this->generateNomorKwitansi();
        
        $data = [
            'id_tagihan' => $idTagihan,
            'nomor_kwitansi' => $nomorKwitansi,
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar') . ' ' . date('H:i:s'),
            'nominal_bayar' => $nominalBayar,
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status_pembayaran' => 'valid',
            'id_user' => session()->get('id_user')
        ];
        
        $this->pembayaranModel->insert($data);
        
        // Update tagihan
        $this->tagihanModel->addPayment($idTagihan, $nominalBayar);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'pembayaran',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Input pembayaran: ' . $nomorKwitansi
        ]);
        
        return redirect()->to(base_url('admin/pembayaran'))->with('success', 'Pembayaran berhasil disimpan dengan nomor kwitansi: ' . $nomorKwitansi);
    }
    
    /**
     * Proses input pembayaran BULK (checklist multiple tagihan) - NEW
     */
    public function storeBulk()
    {
        $rules = [
            'id_siswa' => 'required|integer',
            'metode_pembayaran' => 'required|in_list[tunai,transfer]',
            'nominal' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak lengkap. Pastikan siswa dan metode pembayaran sudah dipilih.');
        }
        
        $idSiswa = $this->request->getPost('id_siswa');
        $metodePembayaran = $this->request->getPost('metode_pembayaran');
        $keterangan = $this->request->getPost('keterangan');
        
        $nominalArray = $this->request->getPost('nominal'); // Array: nominal[id_tagihan] = nominal_bayar
        $tanggalArray = $this->request->getPost('tanggal'); // Array: tanggal[id_tagihan] = tanggal_bayar
        
        // Validasi minimal 1 tagihan dipilih
        if (empty($nominalArray) || !is_array($nominalArray)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 tagihan untuk dibayar!');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $successCount = 0;
        $totalNominal = 0;
        $kwitansiList = [];
        $errors = [];
        
        foreach ($nominalArray as $idTagihan => $nominalBayar) {
            // Skip jika nominal 0 atau tidak valid
            if (!$nominalBayar || $nominalBayar <= 0) {
                continue;
            }
            
            // Get tagihan
            $tagihan = $this->tagihanModel->find($idTagihan);
            
            if (!$tagihan) {
                $errors[] = "Tagihan ID {$idTagihan} tidak ditemukan";
                continue;
            }
            
            // Validasi nominal tidak melebihi sisa
            if ($nominalBayar > $tagihan['sisa_tagihan']) {
                $errors[] = "Nominal untuk tagihan ID {$idTagihan} melebihi sisa tagihan";
                continue;
            }
            
            // Generate nomor kwitansi
            $nomorKwitansi = $this->generateNomorKwitansi();
            
            // Get tanggal bayar untuk tagihan ini
            $tanggalBayar = isset($tanggalArray[$idTagihan]) ? $tanggalArray[$idTagihan] : date('Y-m-d');
            
            $data = [
                'id_tagihan' => $idTagihan,
                'nomor_kwitansi' => $nomorKwitansi,
                'tanggal_bayar' => $tanggalBayar . ' ' . date('H:i:s'),
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => $metodePembayaran,
                'keterangan' => $keterangan,
                'status_pembayaran' => 'valid',
                'id_user' => session()->get('id_user')
            ];
            
            $this->pembayaranModel->insert($data);
            
            // Update status tagihan
            $this->tagihanModel->addPayment($idTagihan, $nominalBayar);
            
            $successCount++;
            $totalNominal += $nominalBayar;
            $kwitansiList[] = $nomorKwitansi;
            
            // Small delay to ensure unique kwitansi number
            usleep(10000); // 0.01 second
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false || $successCount === 0) {
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan pembayaran';
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'bulk_create',
            'modul' => 'pembayaran',
            'data_baru' => json_encode([
                'id_siswa' => $idSiswa,
                'success_count' => $successCount,
                'total_nominal' => $totalNominal,
                'kwitansi_list' => $kwitansiList,
                'metode' => $metodePembayaran
            ]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => "Bulk payment: {$successCount} tagihan, Total: Rp " . number_format($totalNominal, 0, ',', '.')
        ]);
        
        $message = "✅ Berhasil menyimpan <strong>{$successCount} pembayaran</strong> dengan total <strong>Rp " . number_format($totalNominal, 0, ',', '.') . "</strong>";
        
        if (!empty($errors)) {
            $message .= "<br>⚠️ " . count($errors) . " tagihan gagal: " . implode(', ', $errors);
        }
        
        return redirect()->to(base_url('admin/pembayaran'))->with('success', $message);
    }
    
    /**
     * Detail pembayaran
     */
    public function detail($id)
    {
        $pembayaran = $this->pembayaranModel
                           ->select('pembayaran.*, 
                                    tagihan.nominal_akhir,
                                    tagihan.nominal_dibayar,
                                    tagihan.sisa_tagihan,
                                    tagihan.bulan_tagihan,
                                    siswa.nis,
                                    siswa.nama_lengkap as nama_siswa,
                                    siswa.virtual_account,
                                    jenis_tagihan.nama_tagihan,
                                    tahun_ajaran.nama_tahun_ajaran,
                                    kelas.nama_kelas,
                                    users.nama_lengkap as nama_petugas,
                                    pembatal.nama_lengkap as nama_pembatal')
                           ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                           ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                           ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                           ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                           ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                           ->join('users', 'users.id_user = pembayaran.id_user', 'left')
                           ->join('users as pembatal', 'pembatal.id_user = pembayaran.dibatalkan_oleh', 'left')
                           ->where('pembayaran.id_pembayaran', $id)
                           ->first();
        
        if (!$pembayaran) {
            return redirect()->to(base_url('admin/pembayaran'))->with('error', 'Pembayaran tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Pembayaran',
            'pembayaran' => $pembayaran
        ];
        
        return view('admin/pembayaran/detail', $data);
    }

    /**
     * Print kwitansi pembayaran (untuk Windows Print to PDF)
     */
    public function printKwitansi($id)
    {
        $pembayaran = $this->pembayaranModel
                           ->select('pembayaran.*, 
                                    tagihan.nominal_akhir,
                                    tagihan.nominal_dibayar,
                                    tagihan.sisa_tagihan,
                                    tagihan.bulan_tagihan,
                                    siswa.nis,
                                    siswa.nama_lengkap as nama_siswa,
                                    siswa.virtual_account,
                                    jenis_tagihan.nama_tagihan,
                                    tahun_ajaran.nama_tahun_ajaran,
                                    kelas.nama_kelas,
                                    users.nama_lengkap as nama_petugas')
                           ->join('tagihan', 'tagihan.id_tagihan = pembayaran.id_tagihan', 'left')
                           ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
                           ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                           ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                           ->join('kelas', 'kelas.id_kelas = tagihan.id_kelas', 'left')
                           ->join('users', 'users.id_user = pembayaran.id_user', 'left')
                           ->where('pembayaran.id_pembayaran', $id)
                           ->first();
        
        if (!$pembayaran) {
            return redirect()->to(base_url('admin/pembayaran'))->with('error', 'Pembayaran tidak ditemukan');
        }
        
        $data = [
            'pembayaran' => $pembayaran
        ];
        
        // Render view kwitansi_pdf
        return view('admin/pembayaran/kwitansi_pdf', $data);
    }
    
    /**
     * Pembatalan pembayaran
     */
    public function batal($id)
    {
        $pembayaran = $this->pembayaranModel->find($id);
        
        if (!$pembayaran) {
            return redirect()->to(base_url('admin/pembayaran'))->with('error', 'Pembayaran tidak ditemukan');
        }
        
        if ($pembayaran['status_pembayaran'] === 'dibatalkan') {
            return redirect()->to(base_url('admin/pembayaran'))->with('error', 'Pembayaran sudah dibatalkan sebelumnya');
        }
        
        $alasanBatal = $this->request->getPost('alasan_batal');
        
        if (empty($alasanBatal)) {
            return redirect()->back()->with('error', 'Alasan pembatalan harus diisi');
        }
        
        // Update status pembayaran
        $this->pembayaranModel->update($id, [
            'status_pembayaran' => 'dibatalkan',
            'alasan_batal' => $alasanBatal,
            'dibatalkan_oleh' => session()->get('id_user'),
            'tanggal_batal' => date('Y-m-d H:i:s')
        ]);
        
        // Kembalikan nominal ke tagihan
        $this->tagihanModel->cancelPayment($pembayaran['id_tagihan'], $pembayaran['nominal_bayar']);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'cancel',
            'modul' => 'pembayaran',
            'data_lama' => json_encode($pembayaran),
            'data_baru' => json_encode(['alasan_batal' => $alasanBatal]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Membatalkan pembayaran: ' . $pembayaran['nomor_kwitansi']
        ]);
        
        return redirect()->to(base_url('admin/pembayaran'))->with('success', 'Pembayaran berhasil dibatalkan');
    }
    
    /**
     * Generate nomor kwitansi unik
     */
    private function generateNomorKwitansi()
    {
        $prefix = 'KWT';
        $date = date('Ymd');
        
        // Get last number today
        $lastKwitansi = $this->pembayaranModel
                             ->like('nomor_kwitansi', $prefix . $date, 'after')
                             ->orderBy('id_pembayaran', 'DESC')
                             ->first();
        
        if ($lastKwitansi) {
            // Extract number from last kwitansi
            $lastNumber = (int) substr($lastKwitansi['nomor_kwitansi'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get tagihan by siswa (AJAX)
     */
    public function getTagihanBySiswa()
    {
        $idSiswa = $this->request->getGet('id_siswa');
        
        if (!$idSiswa) {
            return $this->response->setJSON([]);
        }
        
        // Get tagihan yang belum lunas dengan informasi lengkap
        $tagihan = $this->tagihanModel
                        ->select('tagihan.id_tagihan,
                                 tagihan.nominal_tagihan,
                                 tagihan.nominal_potongan,
                                 tagihan.nominal_akhir,
                                 tagihan.nominal_dibayar,
                                 tagihan.sisa_tagihan,
                                 tagihan.bulan_tagihan,
                                 tagihan.status_tagihan,
                                 jenis_tagihan.nama_tagihan,
                                 tahun_ajaran.nama_tahun_ajaran')
                        ->join('jenis_tagihan', 'jenis_tagihan.id_jenis_tagihan = tagihan.id_jenis_tagihan', 'left')
                        ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = tagihan.id_tahun_ajaran', 'left')
                        ->where('tagihan.id_siswa', $idSiswa)
                        ->where('tagihan.status_tagihan !=', 'lunas')
                        ->orderBy('tahun_ajaran.nama_tahun_ajaran', 'DESC')
                        ->orderBy('tagihan.bulan_tagihan', 'ASC')
                        ->findAll();
        
        // Format data untuk response
        $result = [];
        foreach ($tagihan as $t) {
            $result[] = [
                'id_tagihan' => $t['id_tagihan'],
                'nama_tagihan' => $t['nama_tagihan'],
                'nama_tahun_ajaran' => $t['nama_tahun_ajaran'],
                'bulan_tagihan' => $t['bulan_tagihan'],
                'nominal_tagihan' => (float)$t['nominal_tagihan'],
                'nominal_potongan' => (float)$t['nominal_potongan'],
                'nominal_akhir' => (float)$t['nominal_akhir'],
                'nominal_dibayar' => (float)$t['nominal_dibayar'],
                'sisa_tagihan' => (float)$t['sisa_tagihan'],
                'status_tagihan' => $t['status_tagihan']
            ];
        }
        
        return $this->response->setJSON($result);
    }
}