<?php

namespace Modules\Billing\Models;

use CodeIgniter\Model;
use Modules\Billing\Entities\Invoice;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Invoice::class;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'invoice_number', 'customer_id', 'trip_id', 'amount',
        'status', 'payment_method', 'issued_at', 'paid_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'invoice_number' => 'required|is_unique[invoices.invoice_number,id,{id}]',
        'amount'         => 'required|numeric',
    ];
}
