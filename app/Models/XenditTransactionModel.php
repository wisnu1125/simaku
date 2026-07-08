<?php

namespace App\Models;

use CodeIgniter\Model;

class XenditTransactionModel extends Model
{
    protected $table            = 'xendit_transaction';
    protected $primaryKey       = 'id_transaction';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'xendit_invoice_id',
        'external_id',
        'id_siswa',
        'tagihan_ids',
        'total_amount',
        'status',
        'invoice_url',
        'payment_channel',
        'xendit_payment_id',
        'paid_at',
        'last_synced_at'
    ];

    protected $useTimestamps = false; // created_at diisi otomatis oleh DEFAULT CURRENT_TIMESTAMP di kolomnya
}
