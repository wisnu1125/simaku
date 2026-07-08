<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentWebhookLogModel extends Model
{
    protected $table            = 'payment_webhook_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_headers',
        'signature_token',
        'payload',
        'validation_result',
        'response_code',
        'response_body',
        'error_message'
    ];

    protected $useTimestamps = false; // received_at otomatis dari DEFAULT CURRENT_TIMESTAMP
}
