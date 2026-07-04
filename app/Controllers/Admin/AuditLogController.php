<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogController extends BaseController
{
    protected $auditLogModel;
    
    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }
    
    /**
     * List audit log
     * UPDATE: pagination sekarang lewat AJAX (fetch), bukan reload halaman penuh tiap
     * ganti halaman/filter. Log ini tidak pernah berhenti bertambah, jadi tetap dibatasi
     * per halaman di server seperti sebelumnya -- cuma cara mengambilnya yang berubah.
     */
    public function index()
    {
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $modul = $this->request->getGet('modul');
        $aksi = $this->request->getGet('aksi');
        $keyword = $this->request->getGet('keyword');
        
        $applyFilters = function ($model) use ($tanggalMulai, $tanggalSelesai, $modul, $aksi, $keyword) {
            if ($tanggalMulai) $model->where('DATE(audit_log.created_at) >=', $tanggalMulai);
            if ($tanggalSelesai) $model->where('DATE(audit_log.created_at) <=', $tanggalSelesai);
            if ($modul) $model->where('audit_log.modul', $modul);
            if ($aksi) $model->where('audit_log.aksi', $aksi);
            if ($keyword) {
                $model->groupStart()
                      ->like('users.nama_lengkap', $keyword)
                      ->orLike('users.username', $keyword)
                      ->orLike('audit_log.keterangan', $keyword)
                      ->groupEnd();
            }
            return $model;
        };
        
        if ($this->request->isAJAX()) {
            $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(100, max(10, (int) ($this->request->getGet('per_page') ?? 30)));
            
            $listModel = new AuditLogModel();
            $listModel->select('audit_log.*, users.nama_lengkap, users.username')
                      ->join('users', 'users.id_user = audit_log.id_user', 'left');
            $applyFilters($listModel);
            
            $total = $listModel->countAllResults(false);
            $rows  = $listModel->orderBy('audit_log.created_at', 'DESC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->findAll();
            
            return $this->response->setJSON([
                'rows' => $rows,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ]);
        }
        
        // Get available modules
        $modulesResult = $this->auditLogModel->select('modul')
                                             ->distinct()
                                             ->orderBy('modul', 'ASC')
                                             ->findAll();
        
        // Get available actions
        $actionsResult = $this->auditLogModel->select('aksi')
                                             ->distinct()
                                             ->orderBy('aksi', 'ASC')
                                             ->findAll();
        
        $data = [
            'title' => 'Audit Log',
            'modules' => $modulesResult,
            'actions' => $actionsResult,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'modul' => $modul,
            'aksi' => $aksi,
            'keyword' => $keyword
        ];
        
        return view('admin/audit_log/index', $data);
    }
    
    /**
     * Detail audit log
     * UPDATE: mendukung AJAX JSON (dipakai drawer di halaman index), tetap redirect
     * biasa kalau diakses langsung lewat URL (bookmark lama).
     */
    public function detail($id)
    {
        $log = $this->auditLogModel
                    ->select('audit_log.*, users.nama_lengkap, users.username, users.email')
                    ->join('users', 'users.id_user = audit_log.id_user', 'left')
                    ->where('audit_log.id_log', $id)
                    ->first();
        
        if (!$log) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Log tidak ditemukan']);
            }
            return redirect()->to(base_url('admin/audit-log'))->with('error', 'Log tidak ditemukan');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['log' => $log]);
        }
        
        return redirect()->to(base_url('admin/audit-log#detail-' . $id));
    }
}