<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AuditLogModel;
use App\Models\TagihanModel;
use App\Models\TahunAjaranModel;
use App\Services\SiswaService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SiswaController extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $auditLogModel;
    protected $siswaService;
    protected $tagihanModel;
    protected $tahunAjaranModel;
    
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->auditLogModel = new AuditLogModel();
        $this->siswaService = new SiswaService();
        $this->tagihanModel = new TagihanModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
    }
    
    /**
     * List siswa.
     * UPDATE: sekarang juga menjadi tempat form Tambah/Edit (modal) dan Detail (drawer),
     * jadi tidak perlu lagi pindah halaman untuk 3 aksi itu.
     * UPDATE 2: pencarian/filter/pagination sekarang jalan di server (lewat AJAX),
     * bukan lagi kirim SEMUA baris siswa ke browser sekaligus -- supaya tetap ringan
     * walau data siswa terus bertambah.
     */
    public function index()
    {
        if ($this->request->isAJAX()) {
            return $this->listJson();
        }
        
        $data = [
            'title'      => 'Data Siswa',
            // Kelas dipakai untuk dropdown filter & dropdown di modal form -- datanya kecil,
            // aman selalu di-load langsung (bukan sumber masalah performa).
            'kelas_list' => $this->kelasModel->getKelasWithTahunAjaran(),
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama_tahun_ajaran', 'DESC')->findAll(),
            'errors'     => session()->getFlashdata('errors') ?? [],
            'import_result' => session()->getFlashdata('import_result') ?? null
        ];
        
        return view('admin/siswa/index', $data);
    }
    
    /**
     * Endpoint JSON untuk tabel siswa: dukung ?q=&kelas=&status=&page=&per_page=
     */
    private function listJson()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 12)));
        $q       = trim((string) $this->request->getGet('q'));
        $fKelas  = $this->request->getGet('kelas');
        $fStatus = $this->request->getGet('status');
        $fTA     = $this->request->getGet('ta');
        
        // Query baris untuk halaman ini
        $listModel = new SiswaModel();
        $listModel->select('siswa.*, kelas.nama_kelas, kelas.tingkat')
                  ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left');
        $this->applySiswaFilters($listModel, $q, $fKelas, $fStatus, $fTA);
        $listModel->orderBy('siswa.id_siswa', 'DESC');
        
        // false = jangan reset query builder, supaya where/join di atas masih kepakai buat limit() di bawah
        $total = $listModel->countAllResults(false);
        $rows  = $listModel->limit($perPage, ($page - 1) * $perPage)->findAll();
        
        // Statistik ringkas -- ikut disaring tahun ajaran yang sama (bukan status/kelas/pencarian),
        // supaya angka di kartu ringkasan konsisten dengan tahun ajaran yang sedang dilihat.
        $statsBase = function () use ($fTA) {
            $m = new SiswaModel();
            if ($fTA) $m->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')->where('kelas.id_tahun_ajaran', $fTA);
            return $m;
        };
        $stats = [
            'total' => $statsBase()->countAllResults(),
            'aktif' => $statsBase()->where('status_siswa', 'aktif')->countAllResults(),
            'lulus' => $statsBase()->where('status_siswa', 'lulus')->countAllResults(),
        ];
        
        return $this->response->setJSON([
            'rows'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) max(1, ceil($total / $perPage)),
            'stats'       => $stats,
        ]);
    }
    
    private function applySiswaFilters($builder, string $q, $fKelas, $fStatus, $fTA = null)
    {
        if ($q !== '') {
            $builder->groupStart()
                    ->like('siswa.nama_lengkap', $q)
                    ->orLike('siswa.nis', $q)
                    ->orLike('siswa.virtual_account', $q)
                    ->groupEnd();
        }
        if (!empty($fKelas)) {
            $builder->where('kelas.nama_kelas', $fKelas);
        }
        if (!empty($fStatus)) {
            $builder->where('siswa.status_siswa', $fStatus);
        }
        if (!empty($fTA)) {
            $builder->where('kelas.id_tahun_ajaran', $fTA);
        }
    }
    
    /**
     * Form tambah siswa
     * UPDATE: form sudah jadi modal di halaman index, jadi URL lama ini
     * dialihkan ke sana supaya bookmark/tautan lama tetap jalan.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/siswa#tambah'));
    }
    
    /**
     * Proses tambah siswa
     */
    public function store()
    {
        $rules = [
            'nis' => 'required|is_unique[siswa.nis]',
            'nisn' => 'permit_empty|is_unique[siswa.nisn]',
            'nama_lengkap' => 'required|min_length[3]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'id_kelas' => 'permit_empty|integer',
            'virtual_account' => 'permit_empty|max_length[20]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Generate Virtual Account jika tidak diisi manual
        $virtualAccount = $this->request->getPost('virtual_account');
        if (empty($virtualAccount)) {
            // Auto-generate VA
            $virtualAccount = $this->siswaService->generateVirtualAccount();
        }
        
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nisn' => $this->request->getPost('nisn') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'nama_wali' => $this->request->getPost('nama_wali'),
            'telp_wali' => $this->request->getPost('telp_wali'),
            'id_kelas' => $this->request->getPost('id_kelas') ?: null,
            'virtual_account' => $virtualAccount,
            'status_siswa' => 'aktif'
        ];
        
        $this->siswaModel->insert($data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'siswa',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah siswa: ' . $data['nama_lengkap'] . ' (VA: ' . $virtualAccount . ')'
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil ditambahkan dengan VA: ' . $virtualAccount);
    }
    
    /**
     * Detail siswa
     * UPDATE: sekarang mendukung 2 mode -
     *  - Dipanggil via AJAX (fetch dari drawer di halaman index) -> balas JSON
     *    berisi biodata + ringkasan tagihan, supaya drawer bisa tampil tanpa reload halaman.
     *  - Diakses langsung lewat URL (mis. bookmark lama) -> redirect ke index dengan
     *    hash supaya drawer yang sama otomatis terbuka di sana.
     */
    public function detail($id)
    {
        $siswa = $this->siswaModel->getSiswaWithKelas($id);
        
        if (!$siswa) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Siswa tidak ditemukan']);
            }
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('admin/siswa#detail-' . $id));
        }
        
        // Ringkasan keuangan siswa (dipakai drawer, tidak mengganti halaman Detail Tagihan yang sudah ada)
        $tagihan = $this->tagihanModel->getTagihanBySiswa($id);
        $totalTagihan = array_sum(array_column($tagihan, 'nominal_akhir'));
        $totalDibayar = array_sum(array_column($tagihan, 'nominal_dibayar'));
        $totalSisa    = array_sum(array_column($tagihan, 'sisa_tagihan'));
        
        // 5 tagihan terbaru untuk pratinjau cepat di drawer
        $tagihanTerbaru = array_slice($tagihan, 0, 5);
        
        return $this->response->setJSON([
            'siswa' => $siswa,
            'ringkasan' => [
                'total_tagihan' => $totalTagihan,
                'total_dibayar' => $totalDibayar,
                'total_sisa'    => $totalSisa,
            ],
            'tagihan_terbaru' => $tagihanTerbaru,
        ]);
    }
    
    /**
     * Form edit siswa
     * UPDATE: form sudah jadi modal di halaman index (data siswa sudah tersedia
     * di sana untuk semua baris), jadi URL lama ini tinggal dialihkan dengan hash
     * supaya modal edit-nya otomatis terbuka untuk siswa yang dituju.
     */
    public function edit($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        return redirect()->to(base_url('admin/siswa#edit-' . $id));
    }
    
    /**
     * Proses update siswa
     */
    public function update($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        $rules = [
            'nis' => "required|is_unique[siswa.nis,id_siswa,{$id}]",
            'nisn' => "permit_empty|is_unique[siswa.nisn,id_siswa,{$id}]",
            'nama_lengkap' => 'required|min_length[3]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'status_siswa' => 'required|in_list[aktif,nonaktif,lulus]',
            'id_kelas' => 'permit_empty|integer',
            'virtual_account' => "required|max_length[20]"
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nisn' => $this->request->getPost('nisn') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'nama_wali' => $this->request->getPost('nama_wali'),
            'telp_wali' => $this->request->getPost('telp_wali'),
            'id_kelas' => $this->request->getPost('id_kelas') ?: null,
            'virtual_account' => $this->request->getPost('virtual_account'),
            'status_siswa' => $this->request->getPost('status_siswa')
        ];
        
        $this->siswaModel->update($id, $data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'siswa',
            'data_lama' => json_encode($siswa),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate siswa: ' . $data['nama_lengkap'] . ' (VA: ' . $data['virtual_account'] . ')'
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil diupdate');
    }
    
    /**
     * Hapus siswa
     */
    public function delete($id)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak ditemukan');
        }
        
        // Cek apakah ada tagihan
        $db = \Config\Database::connect();
        $tagihanCount = $db->table('tagihan')->where('id_siswa', $id)->countAllResults();
        
        if ($tagihanCount > 0) {
            return redirect()->to(base_url('admin/siswa'))->with('error', 'Siswa tidak dapat dihapus karena memiliki data tagihan. Ubah status menjadi nonaktif sebagai gantinya.');
        }
        
        $this->siswaModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'siswa',
            'data_lama' => json_encode($siswa),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus siswa: ' . $siswa['nama_lengkap']
        ]);
        
        return redirect()->to(base_url('admin/siswa'))->with('success', 'Siswa berhasil dihapus');
    }
    
    /**
     * AJAX Search siswa (untuk pembayaran)
     */
    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        
        if (strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }
        
        $siswa = $this->siswaModel->searchSiswa($keyword, 10);
        
        return $this->response->setJSON($siswa);
    }
    
    /**
     * Unduh template Excel untuk impor siswa.
     * Dibuat dinamis (bukan file statis) supaya daftar kelas yang muncul di dropdown
     * validasi selalu sesuai kelas yang benar-benar ada saat ini.
     */
    public function importTemplate()
    {
        $kelasList = $this->kelasModel->getKelasWithTahunAjaran();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');
        
        $headers = ['NIS*', 'NISN', 'Nama Lengkap*', 'Tanggal Lahir* (YYYY-MM-DD)', 'Jenis Kelamin* (L/P)', 'Kelas', 'Nama Wali', 'No. Telepon Wali', 'Alamat', 'Virtual Account'];
        $lastColIndex = count($headers) - 1;
        $lastCol = chr(65 + $lastColIndex);
        
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D9488');
        $sheet->getStyle('A1:' . $lastCol . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(32);
        
        // Baris contoh (ditandai jelas supaya dihapus sebelum benar-benar diimpor)
        $contohKelas = $kelasList[0]['nama_kelas'] ?? '';
        $sheet->fromArray(['2024999', '0012345678', 'Contoh Nama Siswa', '2012-05-14', 'L', $contohKelas, 'Nama Wali Contoh', '081234567890', 'Alamat contoh', ''], null, 'A2');
        $sheet->getStyle('A2:' . $lastCol . '2')->getFont()->setItalic(true)->getColor()->setRGB('94A3B8');
        
        // Kolom yang rawan salah format kalau dianggap angka oleh Excel (NIS/NISN/telepon
        // bisa kehilangan angka 0 di depan) -- paksa jadi format Teks.
        foreach (['A', 'B', 'H', 'J'] as $col) {
            $sheet->getStyle($col . '2:' . $col . '1000')->getNumberFormat()->setFormatCode('@');
        }
        
        // Sheet tersembunyi berisi daftar nama kelas valid, dipakai sebagai sumber dropdown
        // (bukan daftar inline, karena nama kelas bisa panjang & banyak -- lebih aman pakai referensi sheet).
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('_ReferensiKelas');
        $refSheet->setCellValue('A1', 'Daftar Kelas Valid');
        $refSheet->getStyle('A1')->getFont()->setBold(true);
        $kelasNames = array_values(array_unique(array_column($kelasList, 'nama_kelas')));
        foreach ($kelasNames as $i => $nama) {
            $refSheet->setCellValue('A' . ($i + 2), $nama);
        }
        $refSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        $lastKelasRow = count($kelasNames) + 1;
        
        // Data validation: Jenis Kelamin (dropdown L/P)
        for ($r = 2; $r <= 500; $r++) {
            $dv = $sheet->getCell('E' . $r)->getDataValidation();
            $dv->setType(DataValidation::TYPE_LIST);
            $dv->setErrorStyle(DataValidation::STYLE_STOP);
            $dv->setAllowBlank(false);
            $dv->setShowDropDown(true);
            $dv->setShowErrorMessage(true);
            $dv->setErrorTitle('Pilihan tidak valid');
            $dv->setError('Isi hanya dengan L atau P.');
            $dv->setFormula1('"L,P"');
            
            // Data validation: Kelas (dropdown dari sheet referensi), kalau ada datanya
            if ($lastKelasRow >= 2) {
                $dvKelas = $sheet->getCell('F' . $r)->getDataValidation();
                $dvKelas->setType(DataValidation::TYPE_LIST);
                $dvKelas->setErrorStyle(DataValidation::STYLE_WARNING);
                $dvKelas->setAllowBlank(true);
                $dvKelas->setShowDropDown(true);
                $dvKelas->setShowErrorMessage(true);
                $dvKelas->setErrorTitle('Nama kelas tidak dikenali');
                $dvKelas->setError('Nama kelas tidak ada di daftar. Boleh dikosongkan kalau belum ditentukan.');
                $dvKelas->setFormula1('_ReferensiKelas!$A$2:$A$' . $lastKelasRow);
            }
        }
        
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->freezePane('A2');
        $sheet->getStyle('A1:' . $lastCol . '2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $spreadsheet->setActiveSheetIndex(0);
        
        $filename = 'Template_Import_Siswa.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Proses impor siswa dari file Excel yang diunggah.
     * Baris valid langsung disimpan; baris bermasalah dilewati dan dilaporkan (bukan
     * gagal semua-atau-tidak-sama-sekali), supaya satu baris salah tidak menggagalkan
     * ratusan baris lain yang sudah benar.
     */
    public function import()
    {
        $file = $this->request->getFile('file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->to(base_url('admin/siswa#impor'))->with('error', 'File tidak ditemukan atau gagal diunggah.');
        }
        
        $ext = strtolower($file->getClientExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->to(base_url('admin/siswa#impor'))->with('error', 'File harus berformat Excel (.xlsx atau .xls).');
        }
        
        try {
            $reader = IOFactory::createReaderForFile($file->getTempName());
            $reader->setReadDataOnly(false); // tetap false supaya bisa deteksi format tanggal asli
            $spreadsheet = $reader->load($file->getTempName());
            $sheet = $spreadsheet->getSheetByName('Data Siswa') ?: $spreadsheet->getSheet(0);
        } catch (\Throwable $e) {
            return redirect()->to(base_url('admin/siswa#impor'))->with('error', 'Gagal membaca file Excel. Pastikan menggunakan template yang disediakan.');
        }
        
        $highestRow = $sheet->getHighestRow();
        
        // Data pembanding untuk cek duplikat tanpa query berulang per baris
        $kelasList = $this->kelasModel->findAll();
        $kelasMap = [];
        foreach ($kelasList as $k) {
            $kelasMap[strtolower(trim($k['nama_kelas']))] = $k['id_kelas'];
        }
        $existingNis = array_flip(array_map('strval', array_column($this->siswaModel->select('nis')->findAll(), 'nis')));
        $existingNisn = array_flip(array_filter(array_map('strval', array_column($this->siswaModel->select('nisn')->findAll(), 'nisn'))));
        $seenNisInFile = [];
        
        $successCount = 0;
        $errors = [];
        $totalDataRows = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $nis = trim((string) $sheet->getCell('A' . $row)->getCalculatedValue());
            $nisn = trim((string) $sheet->getCell('B' . $row)->getCalculatedValue());
            $namaLengkap = trim((string) $sheet->getCell('C' . $row)->getCalculatedValue());
            $jenisKelamin = strtoupper(trim((string) $sheet->getCell('E' . $row)->getCalculatedValue()));
            $namaKelas = trim((string) $sheet->getCell('F' . $row)->getCalculatedValue());
            $namaWali = trim((string) $sheet->getCell('G' . $row)->getCalculatedValue());
            $telpWali = trim((string) $sheet->getCell('H' . $row)->getCalculatedValue());
            $alamat = trim((string) $sheet->getCell('I' . $row)->getCalculatedValue());
            $virtualAccount = trim((string) $sheet->getCell('J' . $row)->getCalculatedValue());
            
            // Baris kosong total (bukan bagian dari data, biasanya sisa baris kosong di akhir file) -> lewati diam-diam
            if ($nis === '' && $namaLengkap === '') {
                continue;
            }
            $totalDataRows++;
            
            // Bersihkan angka yang kebawa desimal ".0" kalau Excel sempat membacanya sebagai angka
            $nis = preg_replace('/\.0$/', '', $nis);
            $nisn = preg_replace('/\.0$/', '', $nisn);
            
            $tanggalLahirCell = $sheet->getCell('D' . $row);
            $tanggalLahir = $this->parseExcelDate($tanggalLahirCell);
            
            // ---- Validasi ----
            if ($nis === '') { $errors[] = "Baris $row: NIS kosong, baris dilewati."; continue; }
            if ($namaLengkap === '') { $errors[] = "Baris $row (NIS $nis): Nama Lengkap kosong, baris dilewati."; continue; }
            if (!in_array($jenisKelamin, ['L', 'P'])) { $errors[] = "Baris $row (NIS $nis): Jenis Kelamin harus L atau P, baris dilewati."; continue; }
            if (!$tanggalLahir) { $errors[] = "Baris $row (NIS $nis): Tanggal Lahir tidak valid, baris dilewati."; continue; }
            if (isset($existingNis[$nis]) || isset($seenNisInFile[$nis])) { $errors[] = "Baris $row: NIS $nis sudah terdaftar" . (isset($seenNisInFile[$nis]) ? ' (duplikat di dalam file ini)' : ' di database') . ", baris dilewati."; continue; }
            if ($nisn !== '' && isset($existingNisn[$nisn])) { $errors[] = "Baris $row (NIS $nis): NISN $nisn sudah dipakai siswa lain, baris dilewati."; continue; }
            
            $idKelas = null;
            if ($namaKelas !== '') {
                if (isset($kelasMap[strtolower($namaKelas)])) {
                    $idKelas = $kelasMap[strtolower($namaKelas)];
                } else {
                    $errors[] = "Baris $row (NIS $nis): Kelas \"$namaKelas\" tidak ditemukan, siswa tetap ditambahkan tanpa kelas.";
                }
            }
            
            // Virtual Account boleh sama antar siswa (memang disengaja, bukan bug) -- jadi di sini
            // tidak dicek duplikat sama sekali, cukup dibersihkan formatnya saja.
            if ($virtualAccount !== '') {
                $virtualAccount = preg_replace('/\.0$/', '', $virtualAccount);
            } else {
                $virtualAccount = $this->siswaService->generateVirtualAccount();
            }
            
            $data = [
                'nis' => $nis,
                'nisn' => $nisn ?: null,
                'nama_lengkap' => $namaLengkap,
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat ?: null,
                'nama_wali' => $namaWali ?: null,
                'telp_wali' => $telpWali ?: null,
                'id_kelas' => $idKelas,
                'virtual_account' => $virtualAccount,
                'status_siswa' => 'aktif'
            ];
            
            if ($this->siswaModel->insert($data)) {
                $successCount++;
                $existingNis[$nis] = true;
                $seenNisInFile[$nis] = true;
                if ($nisn) $existingNisn[$nisn] = true;
            } else {
                $rowErrors = $this->siswaModel->errors();
                $errors[] = "Baris $row (NIS $nis): " . (reset($rowErrors) ?: 'Gagal disimpan') . ", baris dilewati.";
            }
        }
        
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'import',
            'modul' => 'siswa',
            'data_baru' => json_encode(['file' => $file->getClientName(), 'berhasil' => $successCount, 'gagal' => count($errors)]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => "Impor Excel: {$successCount} siswa berhasil ditambahkan dari {$totalDataRows} baris ({$file->getClientName()})"
        ]);
        
        return redirect()->to(base_url('admin/siswa#impor'))->with('import_result', [
            'success_count' => $successCount,
            'total_rows' => $totalDataRows,
            'errors' => $errors,
        ]);
    }
    
    /**
     * Baca tanggal dari sel Excel dengan aman, baik saat sel diformat sebagai
     * tanggal asli (serial number) maupun diisi sebagai teks biasa.
     */
    private function parseExcelDate($cell)
    {
        $value = $cell->getCalculatedValue();
        if (empty($value) && $value !== 0) return null;
        
        if (is_numeric($value) && ExcelDate::isDateTime($cell)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        
        $value = trim((string) $value);
        if ($value === '') return null;
        
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'] as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt !== false && $dt->format($format) === $value) {
                return $dt->format('Y-m-d');
            }
        }
        
        $ts = strtotime($value);
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }
}