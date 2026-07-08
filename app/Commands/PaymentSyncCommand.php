<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\XenditTransactionModel;
use App\Services\XenditService;

/**
 * Jalankan manual: php spark payment
 * Direkomendasikan untuk cron job, tiap 5-10 menit:
 *   asterisk_asterisk/5 asterisk asterisk asterisk php /path/ke/project/spark payment >> /path/ke/project/writable/logs/payment-cron.log 2>&1
 * (Ganti asterisk_asterisk jadi bintang saja -- cuma dituliskan begini di sini supaya tidak
 * disalahartikan sebagai format komentar oleh sebagian penampil teks.)
 */
class PaymentSyncCommand extends BaseCommand
{
    protected $group       = 'SIMAKU';
    protected $name        = 'payment';
    protected $description = 'Sinkronkan semua transaksi Xendit yang masih PENDING ke status terbaru.';
    protected $usage       = 'payment [options]';
    protected $options     = [
        '--limit' => 'Maksimal berapa transaksi yang diproses dalam 1x jalan (default: 50).',
        '--older-than' => 'Cuma proses transaksi yang dibuat lebih dari N menit lalu, biar tidak bentrok dengan webhook yang baru saja masuk (default: 2).',
    ];

    protected $xenditTransactionModel;
    protected $xenditService;

    public function run(array $params)
    {
        $this->xenditTransactionModel = new XenditTransactionModel();
        $this->xenditService = new XenditService();

        $limit = (int) (CLI::getOption('limit') ?? 50);
        $olderThanMinutes = (int) (CLI::getOption('older-than') ?? 2);

        CLI::write('=== SIMAKU Payment Sync ===', 'yellow');
        CLI::write('Waktu mulai: ' . date('Y-m-d H:i:s'));

        // Cuma proses yang dibuat LEBIH DARI beberapa menit lalu -- supaya tidak "berebut"
        // dengan webhook yang mungkin baru saja dikirim Xendit untuk transaksi yang sama
        // (transaksi yang baru banget dibuat, kasih waktu webhook datang duluan secara wajar).
        $batasWaktu = date('Y-m-d H:i:s', strtotime("-{$olderThanMinutes} minutes"));

        $pending = $this->xenditTransactionModel
            ->where('status', 'pending')
            ->where('created_at <=', $batasWaktu)
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();

        $total = count($pending);
        CLI::write("Ditemukan {$total} transaksi pending untuk dicek.");

        if ($total === 0) {
            CLI::write('Tidak ada yang perlu disinkronkan. Selesai.', 'green');
            return;
        }

        $jumlahBerubah = 0;
        $jumlahGagal = 0;
        $jumlahTetapPending = 0;

        foreach ($pending as $i => $trx) {
            $nomor = $i + 1;
            CLI::write("[{$nomor}/{$total}] Cek invoice {$trx['xendit_invoice_id']} (external_id: {$trx['external_id']})...", 'white');

            try {
                $result = $this->xenditService->syncInvoice((int) $trx['id_transaction'], 'CRON');

                if (!$result['success']) {
                    $jumlahGagal++;
                    CLI::write("   -> GAGAL: {$result['message']}", 'red');
                } elseif ($result['changed']) {
                    $jumlahBerubah++;
                    CLI::write("   -> BERUBAH: {$result['message']}", 'green');
                } else {
                    $jumlahTetapPending++;
                    CLI::write("   -> Masih pending.", 'yellow');
                }
            } catch (\Throwable $e) {
                // Satu transaksi error TIDAK BOLEH menghentikan seluruh proses -- lanjut ke
                // transaksi berikutnya, catat sebagai gagal.
                $jumlahGagal++;
                CLI::write("   -> ERROR TAK TERDUGA: {$e->getMessage()}", 'red');
                log_message('error', 'PaymentSyncCommand error untuk id_transaction=' . $trx['id_transaction'] . ': ' . $e->getMessage());
            }

            // Jeda kecil antar request supaya tidak membombardir API Xendit sekaligus
            usleep(200000); // 0.2 detik
        }

        CLI::newLine();
        CLI::write('=== Ringkasan ===', 'yellow');
        CLI::write("Total diproses : {$total}");
        CLI::write("Berubah jadi lunas/expired : {$jumlahBerubah}", $jumlahBerubah > 0 ? 'green' : 'white');
        CLI::write("Masih pending : {$jumlahTetapPending}");
        CLI::write("Gagal dicek : {$jumlahGagal}", $jumlahGagal > 0 ? 'red' : 'white');
        CLI::write('Selesai: ' . date('Y-m-d H:i:s'));
    }
}
