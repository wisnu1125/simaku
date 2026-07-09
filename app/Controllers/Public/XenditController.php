<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\XenditService;
use Config\Xendit as XenditConfig;

class XenditController extends BaseController
{
    protected $xenditService;

    public function __construct()
    {
        $this->xenditService = new XenditService();
    }

    /**
     * Dipanggil via AJAX dari halaman "Detail Tagihan" publik saat wali murid klik
     * "Bayar Online" setelah memilih tagihan yang mau dibayar. Sengaja balas JSON
     * (bukan redirect) karena halaman asalnya cuma bisa diakses lewat POST -- kalau
     * pakai redirect biasa, tidak ada cara balik ke sana tanpa submit ulang NIS+tanggal lahir.
     */
    public function bayar()
    {
        $idSiswa = (int) $this->request->getPost('id_siswa');
        $idTagihan = $this->request->getPost('id_tagihan'); // array id_tagihan yang dicentang

        if (!$idSiswa || empty($idTagihan) || !is_array($idTagihan)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal 1 tagihan yang mau dibayar.']);
        }

        $result = $this->xenditService->createInvoiceForSiswa($idSiswa, array_map('intval', $idTagihan));

        return $this->response->setJSON($result);
    }

    /**
     * Dipanggil via AJAX saat halaman Detail Tagihan dimuat -- cek apakah siswa ini
     * punya invoice PENDING yang masih aktif, supaya bisa ditampilkan kartu "Pembayaran
     * Tertunda" (Lanjutkan/Batalkan) alih-alih langsung menawarkan "Bayar Online" biasa.
     * Wali murid tidak perlu tahu istilah invoice/pending sama sekali -- ini murni buat
     * menentukan tampilan mana yang muncul.
     */
    public function cekPending()
    {
        $idSiswa = (int) $this->request->getGet('id_siswa');
        if (!$idSiswa) {
            return $this->response->setJSON(['ada' => false]);
        }

        $trx = $this->xenditService->findActivePendingInvoice($idSiswa);
        if (!$trx) {
            return $this->response->setJSON(['ada' => false]);
        }

        return $this->response->setJSON([
            'ada' => true,
            'id_transaction' => $trx['id_transaction'],
            'invoice_url' => $trx['invoice_url'],
            'total_amount' => $trx['total_amount'],
            'created_at' => $trx['created_at'],
        ]);
    }

    /**
     * Dipanggil via AJAX saat wali murid menekan "Batalkan Pembayaran" (setelah
     * mengonfirmasi lewat dialog konfirmasi di sisi frontend).
     */
    public function batalkan()
    {
        $idTransaction = (int) $this->request->getPost('id_transaction');
        if (!$idTransaction) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak valid.']);
        }

        $result = $this->xenditService->cancelInvoice($idTransaction, 'wali_murid');
        return $this->response->setJSON($result);
    }

    /**
     * Halaman setelah wali murid selesai (atau membatalkan) proses pembayaran di
     * halaman Xendit -- ini yang jadi success_redirect_url saat invoice dibuat.
     * Catatan: status PASTI di sini bisa saja belum "paid" kalau webhook dari Xendit
     * belum sampai duluan -- makanya pesannya dibuat netral, bukan langsung "berhasil".
     */
    public function selesai(string $externalId)
    {
        $trx = $this->xenditService->getTransactionStatus($externalId);

        return view('public/pembayaran_selesai', [
            'title' => 'Status Pembayaran',
            'trx' => $trx,
        ]);
    }

    /**
     * Endpoint webhook -- didaftarkan di Dashboard Xendit > Settings > Webhooks.
     * URL: https://domain-anda.com/xendit/webhook
     *
     * Controller ini SENGAJA dibuat tipis: cuma validasi token + format, panggil
     * Service, dan kembalikan response. Semua logic bisnis (termasuk logging) ada
     * di XenditService, supaya tidak ada duplikasi kalau nanti dipanggil dari
     * tempat lain (mis. testing).
     */
    public function webhook()
    {
        $config = new XenditConfig();
        $tokenDiterima = $this->request->getHeaderLine('X-Callback-Token');
        $rawBody = $this->request->getBody();
        $headers = [];
        foreach ($this->request->headers() as $h) {
            $headers[$h->getName()] = $h->getValueLine();
        }

        // Kalau webhook token belum diatur di .env, tolak semua webhook demi keamanan
        // (daripada diam-diam menerima notifikasi palsu dari pihak yang tidak dikenal).
        if (empty($config->webhookToken)) {
            log_message('error', 'Webhook Xendit ditolak: webhookToken belum diatur di .env');
            $this->xenditService->logWebhookRequest($headers, $tokenDiterima, $rawBody, 'ERROR', 500, 'Webhook belum dikonfigurasi', 'xendit.webhookToken kosong di .env');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Webhook belum dikonfigurasi di server']);
        }

        if (!$tokenDiterima || !hash_equals($config->webhookToken, $tokenDiterima)) {
            log_message('warning', 'Webhook Xendit ditolak: X-Callback-Token tidak cocok.');
            $this->xenditService->logWebhookRequest($headers, $tokenDiterima, $rawBody, 'INVALID_TOKEN', 401, 'Token tidak valid');
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Token tidak valid']);
        }

        $payload = $this->request->getJSON(true);
        if (!$payload) {
            $this->xenditService->logWebhookRequest($headers, $tokenDiterima, $rawBody, 'INVALID_PAYLOAD', 400, 'Payload tidak valid');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Payload tidak valid']);
        }

        $result = $this->xenditService->processWebhookPayload($payload);
        $responseCode = $result['success'] ? 200 : 500;

        // Selalu balas 200 kalau payload berhasil DIPROSES (walau isinya "dilewati karena
        // sudah pernah") -- supaya Xendit tidak terus mencoba mengirim ulang. Cuma balas
        // status lain kalau memang ada error di sisi kita.
        $this->xenditService->logWebhookRequest($headers, $tokenDiterima, $rawBody, 'VALID', $responseCode, json_encode($result));

        return $this->response->setStatusCode($responseCode)->setJSON($result);
    }
}
