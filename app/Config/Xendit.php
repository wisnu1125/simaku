<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi integrasi Xendit.
 *
 * PENTING: JANGAN isi nilai rahasia (secretKey, webhookToken) langsung di file ini.
 * Isi semuanya lewat file .env di root project, contoh:
 *
 *   xendit.secretKey = xnd_development_xxxxxxxxxxxxx
 *   xendit.webhookToken = xxxxxxxxxxxxxxxxxxxxx
 *
 * Cara dapat nilainya:
 * - secretKey      : Dashboard Xendit > Settings > API Keys > Generate Secret Key
 *                    (pastikan izin "Money-in" nya Write, minimal untuk produk Invoice)
 * - webhookToken   : Dashboard Xendit > Settings > Webhooks > Verification Token
 *                    (token ini dipakai untuk memastikan notifikasi pembayaran yang masuk
 *                    memang benar-benar dari Xendit, bukan dari pihak lain yang menyamar)
 * - Jangan lupa daftarkan URL webhook Anda di Dashboard Xendit > Settings > Webhooks:
 *     https://domain-anda.com/xendit/webhook
 */
class Xendit extends BaseConfig
{
    /**
     * Secret API Key dari Dashboard Xendit. Diawali "xnd_development_" untuk mode
     * uji coba (test/sandbox) atau "xnd_production_" untuk mode aktif (uang sungguhan).
     */
    public string $secretKey = '';

    /**
     * Verification Token untuk memvalidasi webhook yang masuk dari Xendit
     * (dicocokkan dengan header X-Callback-Token).
     */
    public string $webhookToken = '';

    /**
     * Berapa lama (detik) invoice yang dibuat masih bisa dibayar sebelum kedaluwarsa.
     * Default 24 jam.
     */
    public int $invoiceDurationSeconds = 86400;

    /**
     * Base URL API Xendit. Tidak perlu diubah kecuali untuk keperluan testing khusus.
     */
    public string $apiBaseUrl = 'https://api.xendit.co';
}
