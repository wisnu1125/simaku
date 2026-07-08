<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\XenditTransactionModel;
use App\Models\PaymentLogModel;
use App\Services\XenditService;

class RekonsiliasiController extends BaseController
{
    protected $xenditTransactionModel;
    protected $paymentLogModel;
    protected $xenditService;

    public function __construct()
    {
        $this->xenditTransactionModel = new XenditTransactionModel();
        $this->paymentLogModel = new PaymentLogModel();
        $this->xenditService = new XenditService();
    }

    /**
     * Halaman utama Rekonsiliasi Pembayaran -- daftar semua transaksi Xendit
     * (pending/paid/expired/failed) dengan filter status, plus tombol sync.
     */
    public function index()
    {
        $filterStatus = $this->request->getGet('status');

        if ($this->request->isAJAX()) {
            return $this->listJson();
        }

        // Ringkasan cepat untuk kartu statistik di atas halaman
        $ringkasan = [
            'pending' => $this->xenditTransactionModel->where('status', 'pending')->countAllResults(),
            'paid_hari_ini' => $this->xenditTransactionModel->where('status', 'paid')->where('DATE(paid_at)', date('Y-m-d'))->countAllResults(),
            'expired' => $this->xenditTransactionModel->where('status', 'expired')->countAllResults(),
            'failed' => $this->xenditTransactionModel->where('status', 'failed')->countAllResults(),
        ];

        $data = [
            'title' => 'Rekonsiliasi Pembayaran',
            'ringkasan' => $ringkasan,
            'filter_status' => $filterStatus,
        ];

        return view('admin/rekonsiliasi/index', $data);
    }

    private function listJson()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(10, (int) ($this->request->getGet('per_page') ?? 20)));
        $filterStatus = $this->request->getGet('status');

        $model = new XenditTransactionModel();
        $model->select('xendit_transaction.*, siswa.nama_lengkap, siswa.nis')
              ->join('siswa', 'siswa.id_siswa = xendit_transaction.id_siswa', 'left');

        if ($filterStatus) {
            $model->where('xendit_transaction.status', $filterStatus);
        }

        $total = $model->countAllResults(false);
        $rows = $model->orderBy('xendit_transaction.created_at', 'DESC')
                       ->limit($perPage, ($page - 1) * $perPage)
                       ->findAll();

        return $this->response->setJSON([
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'total_pages' => (int) max(1, ceil($total / $perPage)),
        ]);
    }

    /**
     * AJAX: sinkronkan 1 transaksi (tombol "Sinkronkan" di baris tabel).
     */
    public function sync($idTransaction)
    {
        $result = $this->xenditService->syncInvoice((int) $idTransaction, 'MANUAL');
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: sinkronkan SATU transaksi dari daftar "Sinkronkan Semua Pending".
     * Dipanggil berkali-kali oleh JS (1 request per transaksi) supaya progress
     * bisa ditampilkan bertahap di sisi frontend -- bukan 1 request raksasa yang
     * bisa timeout kalau pending-nya banyak.
     */
    public function syncBatch()
    {
        $idTransaction = (int) $this->request->getPost('id_transaction');
        if (!$idTransaction) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID transaksi tidak valid.']);
        }
        $result = $this->xenditService->syncInvoice($idTransaction, 'MANUAL');
        $result['id_transaction'] = $idTransaction;
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: daftar ID transaksi yang statusnya masih 'pending' -- dipanggil sekali
     * di awal saat klik "Sinkronkan Semua Pending", supaya JS tahu apa saja yang
     * perlu di-loop lewat syncBatch().
     */
    public function pendingIds()
    {
        $rows = $this->xenditTransactionModel->select('id_transaction')->where('status', 'pending')->findAll();
        return $this->response->setJSON(['ids' => array_column($rows, 'id_transaction')]);
    }

    /**
     * AJAX: riwayat log (payment_logs) untuk 1 transaksi -- ditampilkan di modal "Lihat Log".
     */
    public function logs($idTransaction)
    {
        $logs = $this->paymentLogModel
                      ->where('id_transaction', $idTransaction)
                      ->orderBy('created_at', 'DESC')
                      ->findAll();
        return $this->response->setJSON(['logs' => $logs]);
    }
}
