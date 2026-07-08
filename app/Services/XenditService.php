<?php

namespace App\Services;

use App\Models\SiswaModel;
use App\Models\TagihanModel;
use App\Models\PembayaranModel;
use App\Models\XenditTransactionModel;
use Config\Xendit as XenditConfig;

class XenditService
{
    protected $siswaModel;
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $xenditTransactionModel;
    protected $config;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->tagihanModel = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->xenditTransactionModel = new XenditTransactionModel();
        $this->config = new XenditConfig();
    }

    /**
     * Buat invoice Xendit untuk 1 atau lebih tagihan milik 1 siswa.
     * Dipanggil dari halaman publik saat wali murid pilih tagihan yang mau dibayar online.
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
                continue; // lewati diam-diam -- tagihan tidak valid/sudah lunas/bukan milik siswa ini
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
            'customer' => [
                'given_names' => $siswa['nama_lengkap'],
            ],
            'success_redirect_url' => base_url('cek-tagihan/pembayaran-selesai/' . $externalId),
        ];

        $result = $this->httpPost('/v2/invoices', $payload);

        if (!$result['success']) {
            log_message('error', 'Xendit createInvoice gagal: ' . json_encode($result));
            return ['success' => false, 'message' => 'Gagal membuat tagihan pembayaran online. Silakan coba lagi.'];
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

    /**
     * Proses payload webhook dari Xendit (setelah token-nya divalidasi oleh controller).
     * Aman dipanggil berkali-kali dengan payload yang sama (idempotent) -- Xendit kadang
     * mengirim notifikasi yang sama lebih dari 1x.
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
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan'];
        }

        // Sudah pernah diproses sebelumnya -- jangan diproses dobel (misal Xendit kirim
        // notifikasi yang sama 2x, atau statusnya PAID dikirim lagi setelah kita proses).
        if ($trx['status'] === 'paid') {
            return ['success' => true, 'message' => 'Transaksi sudah pernah diproses sebelumnya, dilewati.'];
        }

        if ($status === 'EXPIRED') {
            $this->xenditTransactionModel->update($trx['id_transaction'], ['status' => 'expired']);
            return ['success' => true, 'message' => 'Invoice kedaluwarsa, status diperbarui.'];
        }

        if ($status !== 'PAID') {
            // Status lain (mis. PENDING) -- tidak ada yang perlu dilakukan
            return ['success' => true, 'message' => "Status {$status} diterima, tidak ada tindakan."];
        }

        // ============= STATUS PAID: catat pembayaran & lunas-kan tagihan =============
        $tagihanIds = json_decode($trx['tagihan_ids'], true) ?: [];
        if (empty($tagihanIds)) {
            return ['success' => false, 'message' => 'Data tagihan pada transaksi kosong/rusak.'];
        }

        $paymentChannel = $payload['payment_channel'] ?? ($payload['payment_method'] ?? 'XENDIT');
        $paidAt = !empty($payload['paid_at']) ? date('Y-m-d H:i:s', strtotime($payload['paid_at'])) : date('Y-m-d H:i:s');

        $db = \Config\Database::connect();
        $db->transStart();

        // Bagi total yang dibayar secara proporsional ke tiap tagihan (biasanya jumlahnya
        // pas sama dengan sisa_tagihan masing-masing karena kita yang set amount invoice-nya,
        // tapi tetap dihitung per tagihan untuk jaga-jaga & supaya rapi di riwayat pembayaran).
        foreach ($tagihanIds as $idTagihan) {
            $tagihan = $this->tagihanModel->find($idTagihan);
            if (!$tagihan || $tagihan['status_tagihan'] === 'lunas') {
                continue; // sudah lunas duluan (mis. dibayar manual di kasir sebelum webhook masuk) -- lewati
            }

            $nominalBayar = (float) $tagihan['sisa_tagihan']; // lunasi penuh tagihan ini

            $this->pembayaranModel->insert([
                'id_tagihan' => $idTagihan,
                'nomor_kwitansi' => $this->generateNomorKwitansi(),
                'tanggal_bayar' => $paidAt,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => 'xendit',
                'payment_channel' => $paymentChannel,
                'xendit_invoice_id' => $trx['xendit_invoice_id'],
                'keterangan' => 'Pembayaran online via Xendit (' . $paymentChannel . ')',
                'status_pembayaran' => 'valid',
                'id_user' => null, // otomatis, tidak ada petugas yang input
            ]);

            $this->tagihanModel->addPayment($idTagihan, $nominalBayar);

            usleep(10000); // jaga nomor kwitansi tetap unik kalau lebih dari 1 tagihan
        }

        $this->xenditTransactionModel->update($trx['id_transaction'], [
            'status' => 'paid',
            'payment_channel' => $paymentChannel,
            'paid_at' => $paidAt,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', "Webhook Xendit: transaksi database gagal untuk external_id={$externalId}");
            return ['success' => false, 'message' => 'Gagal menyimpan data pembayaran ke database.'];
        }

        return ['success' => true, 'message' => 'Pembayaran berhasil dicatat.'];
    }

    /**
     * Cek status transaksi berdasarkan external_id -- dipakai halaman "pembayaran selesai"
     * untuk menampilkan status ke wali murid setelah diarahkan kembali dari Xendit.
     */
    public function getTransactionStatus(string $externalId): ?array
    {
        return $this->xenditTransactionModel->where('external_id', $externalId)->first();
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
     * Panggilan HTTP mentah ke API Xendit (pakai cURL bawaan PHP, tidak perlu SDK tambahan).
     */
    private function httpPost(string $endpoint, array $body): array
    {
        $ch = curl_init($this->config->apiBaseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->config->secretKey . ':'),
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['success' => false, 'error' => 'Koneksi ke Xendit gagal: ' . $curlError];
        }

        $data = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            return ['success' => false, 'http_code' => $httpCode, 'error' => $data['message'] ?? 'Permintaan ke Xendit ditolak', 'raw' => $data];
        }

        return ['success' => true, 'data' => $data];
    }
}
