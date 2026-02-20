<?php

namespace Modules\Billing\Controllers;

use App\Controllers\BaseController;
use Modules\Billing\Models\InvoiceModel;
use CodeIgniter\I18n\Time;

class FinanceController extends BaseController
{
    protected $invoiceModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
    }

    public function index()
    {
        // Auto-seed mock data if empty
        if ($this->invoiceModel->countAllResults() === 0) {
            $this->seedMockData();
        }

        $today = date('Y-m-d');
        
        $data = [
            'invoices' => $this->invoiceModel->orderBy('created_at', 'DESC')->findAll(20), // Recent 20
            'total_revenue' => $this->invoiceModel->where('status', 'paid')->selectSum('amount')->first()->amount ?? 0,
            'pending_amount' => $this->invoiceModel->where('status', 'unpaid')->selectSum('amount')->first()->amount ?? 0,
            'card_revenue' => $this->invoiceModel->where('status', 'paid')->where('payment_method', 'card')->selectSum('amount')->first()->amount ?? 0,
            'cash_revenue' => $this->invoiceModel->where('status', 'paid')->where('payment_method', 'cash')->selectSum('amount')->first()->amount ?? 0,
            'title' => 'Finance Dashboard'
        ];

        return view('Modules\Billing\Views\finance\index', $data);
    }

    private function seedMockData()
    {
        $mock = [
            ['amount' => 52.50, 'status' => 'paid', 'method' => 'card', 'date' => '-1 hour'],
            ['amount' => 28.00, 'status' => 'paid', 'method' => 'cash', 'date' => '-2 hours'],
            ['amount' => 15.75, 'status' => 'paid', 'method' => 'card', 'date' => '-5 hours'],
            ['amount' => 45.00, 'status' => 'unpaid', 'method' => 'card', 'date' => 'now'],
            ['amount' => 120.00, 'status' => 'paid', 'method' => 'wallet', 'date' => '-1 day'],
        ];

        foreach ($mock as $idx => $m) {
            $this->invoiceModel->insert([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad($idx, 4, '0', STR_PAD_LEFT),
                'amount' => $m['amount'],
                'status' => $m['status'],
                'payment_method' => $m['method'],
                'issued_at' => date('Y-m-d H:i:s', strtotime($m['date'])),
                'paid_at' => $m['status'] === 'paid' ? date('Y-m-d H:i:s', strtotime($m['date'])) : null,
                'customer_id' => rand(1, 5),
                'trip_id' => rand(100, 200)
            ]);
        }
    }
}
