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
     */
    public function index()
    {
        $perPage = 50;
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $modul = $this->request->getGet('modul');
        $aksi = $this->request->getGet('aksi');
        $keyword = $this->request->getGet('keyword');
        
        $builder = $this->auditLogModel
                        ->select('audit_log.*, users.nama_lengkap, users.username')
                        ->join('users', 'users.id_user = audit_log.id_user', 'left');
        
        if ($tanggalMulai) {
            $builder->where('DATE(audit_log.created_at) >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('DATE(audit_log.created_at) <=', $tanggalSelesai);
        }
        
        if ($modul) {
            $builder->where('audit_log.modul', $modul);
        }
        
        if ($aksi) {
            $builder->where('audit_log.aksi', $aksi);
        }
        
        if ($keyword) {
            $builder->groupStart()
                    ->like('users.nama_lengkap', $keyword)
                    ->orLike('users.username', $keyword)
                    ->orLike('audit_log.keterangan', $keyword)
                    ->groupEnd();
        }
        
        $logs = $builder->orderBy('audit_log.created_at', 'DESC')
                       ->paginate($perPage);
        
        $pager = $this->auditLogModel->pager;
        
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
            'logs' => $logs,
            'pager' => $pager,
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
     */
    public function detail($id)
    {
        $log = $this->auditLogModel
                    ->select('audit_log.*, users.nama_lengkap, users.username, users.email')
                    ->join('users', 'users.id_user = audit_log.id_user', 'left')
                    ->where('audit_log.id_log', $id)
                    ->first();
        
        if (!$log) {
            return redirect()->to(base_url('admin/audit-log'))->with('error', 'Log tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Audit Log',
            'log' => $log
        ];
        
        return view('admin/audit_log/detail', $data);
    }
}