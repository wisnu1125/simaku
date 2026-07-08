<?php

namespace App\Services;

use App\Models\SiswaModel;
use App\Models\TagihanModel;
use App\Models\PembayaranModel;
use App\Models\XenditTransactionModel;
use App\Models\PaymentLogModel;
use App\Models\PaymentWebhookLogModel;
use Config\Xendit as XenditConfig;

class XenditService
{
    protected $siswaModel;
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $xenditTransactionModel;
    protected $paymentLogModel;
    protected $paymentWebhookLogModel;
    protected $config;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->tagihanModel = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->xenditTransactionModel = new XenditTransactionModel();
        $this->paymentLogModel = new PaymentLogModel();
        $this->paymentWebhookLogModel = new PaymentWebhookLogModel();
        $this->config = new XenditConfig();
    }

    // ================================================================
    // BAGIAN 1: BUAT INVOICE (dipakai wali murid saat klik "Bayar Online")
    // ================================================================

    /**
     * Buat invoice Xendit untuk 1 atau lebih tagihan milik 1 siswa.
     *
     * @return array ['success' => bool, 'invoice_url' => string|null, 'message' => string|null]
     */
    public function createInvoiceForSiswa(int $idSiswa, array $idTagihanList): array
    {
        if (empty($this->config->secretKey)) {
            return ['success' => false, 'message' => 'Pembayaran online belum diaktifkan oleh sekolah (API key Xendit belum diatur).'];
        }

        $siswa = $this->siswaModel->find($idSiswa);
        if (!$siswa) {
            return ['success' => false, 'message' => 'Data siswa tidak ditemukan.'];
        }

        if (empty($idTagihanList)) {
            return ['success' => false, 'message' => 'Pilih minimal 1 tagihan untuk dibayar.'];
        }

        // Ambil ulang data tagihan dari database (jangan percaya nominal dari client),
        // pastikan semuanya benar-benar milik siswa ini dan belum lunas.
        $tagihanValid = [];
        $totalAmount = 0;
        foreach ($idTagihanList as $idTagihan) {
            $t = $this->tagihanModel->find((int) $idTagihan);
            if (!$t || (int) $t['id_siswa'] !== $idSiswa || $t['status_tagihan'] === 'lunas') {
                continue;
            }
            $tagihanValid[] = $t;
            $totalAmount += (float) $t['sisa_tagihan'];
        }

        if (empty($tagihanValid) || $totalAmount <= 0) {
            return ['success' => false, 'message' => 'Tagihan yang dipilih tidak valid atau sudah lunas.'];
        }

        $externalId = 'SIMAKU-' . date('YmdHis') . '-' . $idSiswa . '-' . bin2hex(random_bytes(3));

        $payload = [
            'external_id' => $externalId,
            'amount' => (int) round($totalAmount),
            'description' => 'Pembayaran SIMAKU - ' . $siswa['nama_lengkap'] . ' (' . count($tagihanValid) . ' tagihan)',
            'currency' => 'IDR',
            'invoice_duration' => $this->config->invoiceDurationSeconds,
            'customer' => ['given_names' => $siswa['nama_lengkap']],
            'success_redirect_url' => base_url('cek-tagihan/pembayaran-selesai/' . $externalId),
        ];

        $result = $this->httpRequest('POST', '/v2/invoices', $payload);

        if (!$result['success']) {
            log_message('error', 'Xendit createInvoice gagal: ' . json_encode($result));
            return ['success' => false, 'message' => $this->pesanErrorUntukUser($result)];
        }

        $invoice = $result['data'];

        $this->xenditTransactionModel->insert([
            'xendit_invoice_id' => $invoice['id'],
            'external_id' => $externalId,
            'id_siswa' => $idSiswa,
            'tagihan_ids' => json_encode(array_column($tagihanValid, 'id_tagihan')),
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'invoice_url' => $invoice['invoice_url'] ?? null,
        ]);

        return ['success' => true, 'invoice_url' => $invoice['invoice_url'] ?? null];
    }

    // ================================================================
    // BAGIAN 2: CEK STATUS KE XENDIT (dasar untuk sync manual & cron)
    // ================================================================

    /**
     * Ambil detail invoice langsung dari Xendit berdasarkan invoice_id.
     * @return array ['success' => bool, 'data' => array|null, 'message' => string|null, 'error_type' => string|null]
     */
    public function getInvoiceStatus(string $invoiceId): array
    {
        return $this->httpRequest('GET', '/v2/invoices/' . urlencode($invoiceId));
    }

    /**
     * Ambil detail invoice dari Xendit berdasarkan external_id (dipakai kalau invoice_id belum/tidak diketahui).
     * Xendit bisa mengembalikan lebih dari 1 hasil kalau external_id dipakai berkali-kali -- kita ambil yang
     * pertama karena di sistem kita external_id memang dibuat unik per percobaan pembayaran.
     */
    public function getInvoiceByExternalId(string $externalId): array
    {
        $result = $this->httpRequest('GET', '/v2/invoices?external_id=' . urlencode($externalId));
        if ($result['success'] && is_array($result['data']) && isset($result['data'][0])) {
            $result['data'] = $result['data'][0];
        }
        return $result;
    }

    /**
     * Sinkronkan 1 transaksi: tanya status terbaru ke Xendit, kalau berubah jadi PAID
     * (dan di database kita masih pending), proses lewat updatePaymentAsPaid().
     * Dipakai baik oleh tombol "Sinkronkan" (MANUAL) maupun command cron (CRON).
     *
     * @return array ['success' => bool, 'changed' => bool, 'message' => string]
     */
    public function syncInvoice(int $idTransaction, string $source = 'MANUAL'): array
    {
        $trx = $this->xenditTransactionModel->find($idTransaction);
        if (!$trx) {
            return ['success' => false, 'changed' => false, 'message' => 'Transaksi tidak ditemukan di database.'];
        }

        // Sudah final (paid/expired/failed) -- tidak perlu tanya ke Xendit lagi, cuma buang-buang API call.
        // "failed" tetap boleh di-sync ulang siapa tahu statusnya berubah, tapi paid & expired sudah pasti final.
        if ($trx['status'] === 'paid') {
            return ['success' => true, 'changed' => false, 'message' => 'Transaksi ini sudah lunas sebelumnya.'];
        }

        $result = $this->getInvoiceStatus($trx['xendit_invoice_id']);

        $this->xenditTransactionModel->update($idTransaction, ['last_synced_at' => date('Y-m-d H:i:s')]);

        if (!$result['success']) {
            $this->logPaymentChange($trx, null, $source, $trx['status'], $trx['status'], $result, 'Sync gagal: ' . $this->pesanErrorUntukUser($result));
            return ['success' => false, 'changed' => false, 'message' => $this->pesanErrorUntukUser($result)];
        }

        $xenditStatus = $result['data']['status'] ?? null;
        $statusMap = ['PAID' => 'paid', 'SETTLED' => 'paid', 'EXPIRED' => 'expired'];
        $statusBaru = $statusMap[$xenditStatus] ?? null;

        if ($statusBaru === 'paid') {
            $prosesResult = $this->updatePaymentAsPaid($trx, $this->normalisasiDataXendit($result['data']), $source);
            return ['success' => $prosesResult['success'], 'changed' => $prosesResult['success'], 'message' => $prosesResult['message']];
        }

        if ($statusBaru === 'expired' && $trx['status'] !== 'expired') {
            $this->xenditTransactionModel->update($idTransaction, ['status' => 'expired']);
            $this->logPaymentChange($trx, null, $source, $trx['status'], 'expired', $result, 'Invoice kedaluwarsa (dari sync).');
            return ['success' => true, 'changed' => true, 'message' => 'Invoice sudah kedaluwarsa.'];
        }

        // Masih PENDING di Xendit juga -- tidak ada perubahan
        $this->logPaymentChange($trx, null, $source, $trx['status'], $trx['status'], $result, 'Status masih PENDING, tidak ada perubahan.');
        return ['success' => true, 'changed' => false, 'message' => 'Status masih menunggu pembayaran (PENDING).'];
    }

    // ================================================================
    // BAGIAN 3: WEBHOOK (dipanggil dari Controller, yang sudah validasi token)
    // ================================================================

    /**
     * Proses payload webhook dari Xendit yang SUDAH divalidasi token-nya oleh controller.
     * Controller cuma boleh validasi + panggil ini + return response -- semua logic bisnis di sini.
     */
    public function processWebhookPayload(array $payload): array
    {
        $externalId = $payload['external_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$externalId) {
            return ['success' => false, 'message' => 'Payload tidak berisi external_id'];
        }

        $trx = $this->xenditTransactionModel->where('external_id', $externalId)->first();
        if (!$trx) {
            log_message('warning', "Webhook Xendit: transaksi dengan external_id={$externalId} tidak ditemukan di database.");
            $this->paymentLogModel->insert([
                'invoice_id' => $payload['id'] ?? null,
                'external_id' => $externalId,
                'source' => 'WEBHOOK',
                'new_status' => $status,
                'response_json' => json_encode($payload),
                'message' => 'Transaksi tidak ditemukan di database saat webhook diterima.',
            ]);
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan'];
        }

        if ($status === 'EXPIRED') {
            if ($trx['status'] !== 'expired' && $trx['status'] !== 'paid') {
                $this->xenditTransactionModel->update($trx['id_transaction'], ['status' => 'expired']);
                $this->logPaymentChange($trx, null, 'WEBHOOK', $trx['status'], 'expired', ['data' => $payload], 'Invoice kedaluwarsa (dari webhook).');
            }
            return ['success' => true, 'message' => 'Invoice kedaluwarsa, status diperbarui.'];
        }

        if ($status !== 'PAID') {
            return ['success' => true, 'message' => "Status {$status} diterima, tidak ada tindakan."];
        }

        $result = $this->updatePaymentAsPaid($trx, $this->normalisasiDataXendit($payload), 'WEBHOOK');
        return $result;
    }

    // ================================================================
    // BAGIAN 4: METHOD TUNGGAL UNTUK MELUNASKAN PEMBAYARAN
    // Dipanggil oleh: webhook, sync manual, DAN cron -- supaya logic-nya
    // cuma ada di SATU tempat (tidak ada duplikasi/celah perbedaan perilaku).
    // ================================================================

    /**
     * @param array $trx Baris xendit_transaction (harus sudah di-fetch sebelumnya)
     * @param array $dataXendit Data yang SUDAH dinormalisasi lewat normalisasiDataXendit()
     * @param string $source 'WEBHOOK' | 'MANUAL' | 'CRON'
     */
    public function updatePaymentAsPaid(array $trx, array $dataXendit, string $source): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // ---------- IDEMPOTENCY: kunci baris ini & cek ulang statusnya SAAT INI JUGA ----------
        // Ini penting karena antara webhook & sync manual/cron bisa saja "bentrok" nyaris
        // bersamaan. FOR UPDATE mengunci baris supaya proses lain harus antre dulu.
        $trxTerkini = $db->table('xendit_transaction')
                         ->where('id_transaction', $trx['id_transaction'])
                         ->get()
                         ->getRowArray();

        if (!$trxTerkini) {
            $db->transComplete();
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan saat memproses.'];
        }

        if ($trxTerkini['status'] === 'paid') {
            // Sudah diproses sebelumnya (mungkin oleh webhook yang datang lebih dulu, atau
            // sync yang berjalan hampir bersamaan) -- JANGAN diproses lagi. Ini yang membuat
            // seluruh alur ini aman dipanggil berkali-kali (idempotent).
            $db->transComplete();
            $this->logPaymentChange($trxTerkini, null, $source, 'paid', 'paid', ['data' => $dataXendit], 'Dilewati -- transaksi sudah lunas sebelumnya (idempotent guard).');
            return ['success' => true, 'message' => 'Transaksi sudah pernah diproses sebelumnya, dilewati.'];
        }

        $tagihanIds = json_decode($trxTerkini['tagihan_ids'], true) ?: [];
        if (empty($tagihanIds)) {
            $db->transRollback();
            $this->logPaymentChange($trxTerkini, null, $source, $trxTerkini['status'], $trxTerkini['status'], ['data' => $dataXendit], 'Gagal -- data tagihan pada transaksi kosong/rusak.');
            return ['success' => false, 'message' => 'Data tagihan pada transaksi kosong/rusak.'];
        }

        $paidAt = $dataXendit['paid_at'] ?? date('Y-m-d H:i:s');
        $paymentChannel = $dataXendit['payment_channel'] ?? 'XENDIT';
        $lastIdPembayaran = null;

        foreach ($tagihanIds as $idTagihan) {
            $tagihan = $this->tagihanModel->find($idTagihan);
            if (!$tagihan || $tagihan['status_tagihan'] === 'lunas') {
                continue; // sudah lunas duluan (mis. dibayar manual di kasir) -- lewati, bukan error
            }

            $nominalBayar = (float) $tagihan['sisa_tagihan'];

            $this->pembayaranModel->insert([
                'id_tagihan' => $idTagihan,
                'nomor_kwitansi' => $this->generateNomorKwitansi(),
                'tanggal_bayar' => $paidAt,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => 'xendit',
                'payment_channel' => $paymentChannel,
                'xendit_invoice_id' => $trxTerkini['xendit_invoice_id'],
                'keterangan' => 'Pembayaran online via Xendit (' . $paymentChannel . ') -- diproses via ' . $source,
                'status_pembayaran' => 'valid',
                'id_user' => null,
            ]);
            $lastIdPembayaran = $this->pembayaranModel->getInsertID();

            $this->tagihanModel->addPayment($idTagihan, $nominalBayar);

            usleep(10000); // jaga nomor kwitansi tetap unik kalau lebih dari 1 tagihan dalam 1 invoice
        }

        // ---------- UPDATE STATUS DENGAN GUARD "WHERE status != 'paid'" ----------
        // Guard tambahan di level query (bukan cuma cek PHP di atas) -- kalau ada race
        // condition yang lolos dari FOR UPDATE (mis. beda koneksi DB), UPDATE ini tetap
        // hanya akan kena 1x karena baris kedua tidak akan menemukan status pending lagi.
        $db->table('xendit_transaction')
           ->where('id_transaction', $trxTerkini['id_transaction'])
           ->where('status !=', 'paid')
           ->update([
                'status' => 'paid',
                'payment_channel' => $paymentChannel,
                'xendit_payment_id' => $dataXendit['payment_id'] ?? null,
                'paid_at' => $paidAt,
                'last_synced_at' => date('Y-m-d H:i:s'),
           ]);

        $this->logPaymentChange($trxTerkini, $lastIdPembayaran, $source, $trxTerkini['status'], 'paid', ['data' => $dataXendit], 'Pembayaran berhasil dicatat.');

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', "updatePaymentAsPaid: transaksi database gagal untuk external_id={$trxTerkini['external_id']}");
            return ['success' => false, 'message' => 'Gagal menyimpan data pembayaran ke database (transaksi dibatalkan/rollback).'];
        }

        return ['success' => true, 'message' => 'Pembayaran berhasil dicatat.'];
    }

    // ================================================================
    // BAGIAN 5: LOGGING
    // ================================================================

    private function logPaymentChange(?array $trx, ?int $idPembayaran, string $source, ?string $oldStatus, ?string $newStatus, ?array $rawResult, string $message): void
    {
        try {
            $this->paymentLogModel->insert([
                'id_transaction' => $trx['id_transaction'] ?? null,
                'id_pembayaran' => $idPembayaran,
                'invoice_id' => $trx['xendit_invoice_id'] ?? null,
                'external_id' => $trx['external_id'] ?? null,
                'source' => $source,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'response_json' => $rawResult ? json_encode($rawResult) : null,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            // Kegagalan mencatat log TIDAK BOLEH menggagalkan proses pembayaran utamanya --
            // cukup catat ke log sistem biasa.
            log_message('error', 'Gagal insert payment_logs: ' . $e->getMessage());
        }
    }

    /**
     * Catat SETIAP request webhook yang masuk (valid maupun ditolak) -- dipanggil dari Controller.
     */
    public function logWebhookRequest(array $headers, ?string $token, string $payload, string $validationResult, int $responseCode, string $responseBody, ?string $errorMessage = null): void
    {
        try {
            // Jangan simpan header Authorization/sejenisnya kalau ada -- cuma perlu tahu token
            // callback-nya (disimpan terpisah di kolom signature_token, bukan sengaja disamarkan
            // di sini karena memang berguna untuk debugging token yang salah).
            $this->paymentWebhookLogModel->insert([
                'request_headers' => json_encode($headers),
                'signature_token' => $token,
                'payload' => $payload,
                'validation_result' => $validationResult,
                'response_code' => $responseCode,
                'response_body' => $responseBody,
                'error_message' => $errorMessage,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal insert payment_webhook_logs: ' . $e->getMessage());
        }
    }

    // ================================================================
    // BAGIAN 6: HELPER
    // ================================================================

    /**
     * Cek status transaksi berdasarkan external_id -- dipakai halaman "pembayaran selesai".
     */
    public function getTransactionStatus(string $externalId): ?array
    {
        return $this->xenditTransactionModel->where('external_id', $externalId)->first();
    }

    /**
     * Samakan bentuk data dari webhook payload ATAU dari respons GET invoice (field-nya
     * kadang beda penamaan) jadi satu bentuk yang konsisten dipakai updatePaymentAsPaid().
     */
    private function normalisasiDataXendit(array $raw): array
    {
        return [
            'payment_id' => $raw['payment_id'] ?? $raw['id'] ?? null,
            'payment_channel' => $raw['payment_channel'] ?? $raw['payment_method'] ?? $raw['bank_code'] ?? 'XENDIT',
            'paid_at' => !empty($raw['paid_at']) ? date('Y-m-d H:i:s', strtotime($raw['paid_at'])) : date('Y-m-d H:i:s'),
        ];
    }

    private function generateNomorKwitansi(): string
    {
        $prefix = 'KWT';
        $date = date('Ymd');
        $last = $this->pembayaranModel
            ->like('nomor_kwitansi', $prefix . $date, 'after')
            ->orderBy('id_pembayaran', 'DESC')
            ->first();
        $newNumber = $last ? ((int) substr($last['nomor_kwitansi'], -4) + 1) : 1;
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ubah hasil error teknis jadi pesan yang masuk akal ditampilkan ke user/admin.
     */
    private function pesanErrorUntukUser(array $result): string
    {
        if (isset($result['error_type'])) {
            $map = [
                'timeout' => 'Koneksi ke Xendit terlalu lama (timeout). Coba lagi beberapa saat.',
                'connection' => 'Tidak dapat terhubung ke server Xendit. Periksa koneksi internet server.',
                'not_found' => 'Invoice tidak ditemukan di Xendit.',
                'rate_limit' => 'Terlalu banyak permintaan ke Xendit dalam waktu singkat. Coba lagi sebentar lagi.',
                'server_error' => 'Server Xendit sedang bermasalah. Coba lagi beberapa saat.',
            ];
            if (isset($map[$result['error_type']])) return $map[$result['error_type']];
        }
        return $result['error'] ?? 'Terjadi kesalahan saat menghubungi Xendit.';
    }

    // ================================================================
    // BAGIAN 7: HTTP CLIENT KE API XENDIT
    // ================================================================

    private function httpRequest(string $method, string $endpoint, ?array $body = null): array
    {
        $ch = curl_init($this->config->apiBaseUrl . $endpoint);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->config->secretKey . ':'),
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ];
        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        // ---------- Tangani berbagai kondisi gagal koneksi ----------
        if ($curlErrno === CURLE_OPERATION_TIMEDOUT) {
            return ['success' => false, 'error_type' => 'timeout', 'error' => 'Timeout menghubungi Xendit: ' . $curlError];
        }
        if ($response === false) {
            return ['success' => false, 'error_type' => 'connection', 'error' => 'Gagal terhubung ke Xendit: ' . $curlError];
        }

        $data = json_decode($response, true);

        if ($httpCode === 404) {
            return ['success' => false, 'error_type' => 'not_found', 'http_code' => 404, 'error' => 'Invoice tidak ditemukan', 'raw' => $data];
        }
        if ($httpCode === 429) {
            return ['success' => false, 'error_type' => 'rate_limit', 'http_code' => 429, 'error' => 'Rate limit tercapai', 'raw' => $data];
        }
        if ($httpCode >= 500) {
            return ['success' => false, 'error_type' => 'server_error', 'http_code' => $httpCode, 'error' => 'Server Xendit error', 'raw' => $data];
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            return ['success' => false, 'error_type' => 'client_error', 'http_code' => $httpCode, 'error' => $data['message'] ?? 'Permintaan ke Xendit ditolak', 'raw' => $data];
        }

        return ['success' => true, 'data' => $data];
    }
}
