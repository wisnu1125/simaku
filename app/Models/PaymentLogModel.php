<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentLogModel extends Model
{
    protected $table            = 'payment_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_transaction',
        'id_pembayaran',
        'invoice_id',
        'external_id',
        'source',
        'old_status',
        'new_status',
        'response_json',
        'message'
    ];

    protected $useTimestamps = false; // created_at otomatis dari DEFAULT CURRENT_TIMESTAMP
}
