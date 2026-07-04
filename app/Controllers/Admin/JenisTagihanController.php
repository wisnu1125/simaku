<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisTagihanModel;
use App\Models\AuditLogModel;

class JenisTagihanController extends BaseController
{
    protected $jenisTagihanModel;
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->jenisTagihanModel = new JenisTagihanModel();
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List jenis tagihan
     */
    public function index()
    {
        $data = [
            'title' => 'Jenis Tagihan',
            'jenis_tagihan' => $this->jenisTagihanModel->orderBy('grup_tagihan', 'ASC')
                                                       ->orderBy('nama_tagihan', 'ASC')
                                                       ->findAll(),
            'errors' => session()->getFlashdata('errors') ?? []
        ];
        
        return view('admin/jenis_tagihan/index', $data);
    }
    
    /**
     * Form tambah/edit jenis tagihan
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function create()
    {
        return redirect()->to(base_url('admin/jenis-tagihan#tambah'));
    }
    
    /**
     * Proses tambah jenis tagihan
     */
    public function store()
    {
        $rules = [
            'nama_tagihan' => 'required|min_length[3]|max_length[100]',
            'kode_tagihan' => 'required|alpha_numeric_punct|max_length[20]|is_unique[jenis_tagihan.kode_tagihan]',
            'tipe_tagihan' => 'required|in_list[bulanan,tahunan,sekali]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_tagihan' => $this->request->getPost('nama_tagihan'),
            'grup_tagihan' => $this->request->getPost('grup_tagihan') ?: null,
            'kode_tagihan' => strtoupper($this->request->getPost('kode_tagihan')),
            'tipe_tagihan' => $this->request->getPost('tipe_tagihan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'aktif'
        ];
        
        $this->jenisTagihanModel->insert($data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'create',
            'modul' => 'jenis_tagihan',
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menambah jenis tagihan: ' . $data['nama_tagihan']
        ]);
        
        return redirect()->to(base_url('admin/jenis-tagihan'))->with('success', 'Jenis tagihan berhasil ditambahkan');
    }
    
    /**
     * Form edit jenis tagihan
     * UPDATE: form sudah jadi modal di halaman index, URL lama dialihkan ke sana.
     */
    public function edit($id)
    {
        $jenisTagihan = $this->jenisTagihanModel->find($id);
        
        if (!$jenisTagihan) {
            return redirect()->to(base_url('admin/jenis-tagihan'))->with('error', 'Jenis tagihan tidak ditemukan');
        }
        
        return redirect()->to(base_url('admin/jenis-tagihan#edit-' . $id));
    }
    
    /**
     * Proses update jenis tagihan
     */
    public function update($id)
    {
        $jenisTagihan = $this->jenisTagihanModel->find($id);
        
        if (!$jenisTagihan) {
            return redirect()->to(base_url('admin/jenis-tagihan'))->with('error', 'Jenis tagihan tidak ditemukan');
        }
        
        $rules = [
            'nama_tagihan' => 'required|min_length[3]|max_length[100]',
            'kode_tagihan' => "required|alpha_numeric_punct|max_length[20]|is_unique[jenis_tagihan.kode_tagihan,id_jenis_tagihan,{$id}]",
            'tipe_tagihan' => 'required|in_list[bulanan,tahunan,sekali]',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_tagihan' => $this->request->getPost('nama_tagihan'),
            'grup_tagihan' => $this->request->getPost('grup_tagihan') ?: null,
            'kode_tagihan' => strtoupper($this->request->getPost('kode_tagihan')),
            'tipe_tagihan' => $this->request->getPost('tipe_tagihan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => $this->request->getPost('status')
        ];
        
        $this->jenisTagihanModel->update($id, $data);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'update',
            'modul' => 'jenis_tagihan',
            'data_lama' => json_encode($jenisTagihan),
            'data_baru' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Mengupdate jenis tagihan: ' . $data['nama_tagihan']
        ]);
        
        return redirect()->to(base_url('admin/jenis-tagihan'))->with('success', 'Jenis tagihan berhasil diupdate');
    }
    
    /**
     * Hapus jenis tagihan
     */
    public function delete($id)
    {
        $jenisTagihan = $this->jenisTagihanModel->find($id);
        
        if (!$jenisTagihan) {
            return redirect()->to(base_url('admin/jenis-tagihan'))->with('error', 'Jenis tagihan tidak ditemukan');
        }
        
        // Cek apakah ada skema tagihan atau tagihan yang menggunakan jenis tagihan ini
        $db = \Config\Database::connect();
        $skemaCount = $db->table('skema_tagihan')->where('id_jenis_tagihan', $id)->countAllResults();
        $tagihanCount = $db->table('tagihan')->where('id_jenis_tagihan', $id)->countAllResults();
        
        if ($skemaCount > 0 || $tagihanCount > 0) {
            return redirect()->to(base_url('admin/jenis-tagihan'))->with('error', 'Jenis tagihan tidak dapat dihapus karena sudah digunakan. Ubah status menjadi nonaktif sebagai gantinya.');
        }
        
        $this->jenisTagihanModel->delete($id);
        
        // Audit log
        $this->auditLogModel->insert([
            'id_user' => session()->get('id_user'),
            'aksi' => 'delete',
            'modul' => 'jenis_tagihan',
            'data_lama' => json_encode($jenisTagihan),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'keterangan' => 'Menghapus jenis tagihan: ' . $jenisTagihan['nama_tagihan']
        ]);
        
        return redirect()->to(base_url('admin/jenis-tagihan'))->with('success', 'Jenis tagihan berhasil dihapus');
    }
}