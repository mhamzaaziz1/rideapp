<?php

namespace Modules\Customer\Controllers;

use App\Controllers\BaseController;
use Modules\Customer\Models\CustomerModel;
use Modules\Customer\Models\CustomerAddressModel;
use Modules\Billing\Models\WalletTransactionModel;
use Modules\Billing\Services\WalletService;
use Modules\Dispatch\Models\RatingModel;
use Modules\Customer\Models\CustomerCardModel;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $ratingModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->ratingModel = new RatingModel();
    }

    public function index()
    {
        $data = [
            'customers' => $this->customerModel->orderBy('created_at', 'DESC')->findAll(),
            'total_customers' => $this->customerModel->countAll(),
            'active_customers' => $this->customerModel->where('status', 'active')->countAllResults(),
            'new_this_month' => $this->customerModel->where('created_at >=', date('Y-m-01 00:00:00'))->countAllResults(),
            'total_spent' => $this->customerModel->selectSum('total_spent')->first()->total_spent ?? 0,
            'title' => 'Customer Management'
        ];

        return view('Modules\Customer\Views\index', $data);
    }

    public function new()
    {
        $data = [
            'customer' => new \Modules\Customer\Entities\Customer(),
            'title' => 'Add New Customer'
        ];
        return view('Modules\Customer\Views\form', $data);
    }

    public function create()
    {
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => 'required|valid_email|is_unique[customers.email]',
            'phone'      => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        // Handle Avatar Upload
        $file = $this->request->getFile('avatar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/customers', $newName);
            $data['avatar'] = 'uploads/customers/' . $newName;
        }

        // Default stats
        $data['total_trips'] = 0;
        $data['total_spent'] = 0;
        $data['rating'] = 5.0;

        if (!$this->customerModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to create customer');
        }

        return redirect()->to('/customers')->with('success', 'Customer created successfully');
    }

    public function edit($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        $data = [
            'customer' => $customer,
            'title' => 'Edit Customer'
        ];
        return view('Modules\Customer\Views\form', $data);
    }

    public function update($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => "required|valid_email|is_unique[customers.email,id,{$id}]",
            'phone'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle Avatar Upload
        $file = $this->request->getFile('avatar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/customers', $newName);
            $data['avatar'] = 'uploads/customers/' . $newName;
        }

        if (!$this->customerModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update customer');
        }

        return redirect()->to('/customers')->with('success', 'Customer updated successfully');
    }

    public function delete($id)
    {
        if ($this->customerModel->delete($id)) {
            return redirect()->to('/customers')->with('success', 'Customer deleted successfully');
        }
        return redirect()->to('/customers')->with('error', 'Failed to delete customer');
    }

    // AJAX Endpoint
    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        if ($this->customerModel->update($id, ['status' => $status])) {
             return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function profile($id)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        // Fetch Trips
        // Assuming TripModel is in Modules\Dispatch\Models\TripModel
        $tripModel = new \Modules\Dispatch\Models\TripModel();
        
        // Use builder to get driver names for history
        $builder = $tripModel->builder();
        $builder->select('trips.*, drivers.first_name as d_first, drivers.last_name as d_last');
        $builder->join('drivers', 'drivers.id = trips.driver_id', 'left');
        $builder->where('trips.customer_id', $id);
        $builder->orderBy('trips.created_at', 'DESC');
        $trips = $builder->get()->getResult();

        // Calculate basic stats from trips if needed, or rely on stored stats
        // Let's recalculate total spent just in case
        $totalSpent = 0;
        foreach($trips as $t) {
            if($t->status == 'completed') $totalSpent += $t->fare_amount;
        }

        // Fetch Wallet Transactions
        $txModel = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'customer')
                                ->where('user_id', $id)
                                ->orderBy('created_at', 'DESC')
                                ->findAll();

        // Fetch Ratings
        $ratings = $this->ratingModel->where('ratee_type', 'customer')
                                     ->where('ratee_id', $id)
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll();

        // Fetch Addresses
        $addressModel = new CustomerAddressModel();
        $addresses = $addressModel->where('customer_id', $id)
                                  ->orderBy('is_default', 'DESC')
                                  ->orderBy('created_at', 'DESC')
                                  ->findAll();

        // Calculate Stats
        $completedTrips = array_filter($trips, function($t) { return $t->status == 'completed'; });
        $totalTrips = count($completedTrips);
        $avgSpend = $totalTrips > 0 ? $totalSpent / $totalTrips : 0;
        
        // Calculate Wallet Stats from wallet_transactions (the source of truth)
        $computedWalletBalance = WalletService::calculateCustomerBalance($id);
        $totalDeposits = 0;
        $totalSpentFromWallet = 0;
        foreach($transactions as $tx) {
            if(in_array($tx['type'], ['deposit', 'refund'])) $totalDeposits += $tx['amount'];
            if(in_array($tx['type'], ['payment', 'withdrawal'])) $totalSpentFromWallet += $tx['amount'];
        }

        // Fetch Cards
        $cardModel = new CustomerCardModel();
        $cards = $cardModel->where('customer_id', $id)
                           ->orderBy('is_default', 'DESC')
                           ->orderBy('created_at', 'DESC')
                           ->findAll();

        $data = [
            'customer' => $customer,
            'trips' => $trips,
            'transactions' => $transactions,
            'ratings' => $ratings,
            'addresses' => $addresses,
            'cards' => $cards,
            'stats' => [
                'total_spent' => $totalSpent,
                'total_trips' => $totalTrips,
                'avg_spend' => $avgSpend,
                'wallet_balance' => $computedWalletBalance,
                'total_deposited' => $totalDeposits
            ],
            'title' => $customer->first_name . ' ' . $customer->last_name . ' - Profile'
        ];

        return view('Modules\Customer\Views\profile', $data);
    }

    public function addFund()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'amount' => 'required|numeric|greater_than[0]',
            'type' => 'required|in_list[deposit,withdrawal]',
            'description' => 'required'
        ];

        if (!$this->validate($rules)) {
             return redirect()->back()->withInput()->with('error', 'Invalid input');
        }

        $customerId = $this->request->getPost('customer_id');
        $amount = $this->request->getPost('amount');
        $type = $this->request->getPost('type');
        $description = $this->request->getPost('description');

        $customer = $this->customerModel->find($customerId);
        if(!$customer) return redirect()->back()->with('error', 'Customer not found');

        $db = \Config\Database::connect();
        $db->transStart();

        // Log Transaction
        $txModel = new WalletTransactionModel();
        $txModel->save([
            'user_type'   => 'customer',
            'user_id'     => $customerId,
            'type'        => $type,
            'amount'      => $amount,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Transaction failed');
        }

        // Sync stored wallet_balance column with computed value
        WalletService::syncBalance('customer', $customerId);

        return redirect()->back()->with('success', 'Wallet updated successfully');
    }

    /**
     * JSON API – return a customer's saved addresses for the dispatch modal.
     */
    public function getAddresses(int $customerId)
    {
        $addrModel = new CustomerAddressModel();
        $rows = $addrModel->where('customer_id', $customerId)
                          ->orderBy('is_default', 'DESC')
                          ->orderBy('type', 'ASC')
                          ->findAll();

        $addresses = array_map(fn($a) => [
            'id'         => $a->id,
            'type'       => $a->type,
            'address'    => $a->address,
            'city'       => $a->city,
            'state'      => $a->state,
            'zip_code'   => $a->zip_code,
            'full'       => trim(implode(', ', array_filter([$a->address, $a->city, $a->state, $a->zip_code]))),
            'is_default' => (bool)$a->is_default,
            'latitude'   => $a->latitude,
            'longitude'  => $a->longitude,
        ], $rows);

        return $this->response->setJSON(['addresses' => $addresses]);
    }

    /**
     * Render a printable full wallet statement for a customer.
     */
    public function printStatement(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $txModel      = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'customer')
                                ->where('user_id', $id)
                                ->orderBy('id', 'DESC')
                                ->findAll();

        $walletBalance = WalletService::calculateCustomerBalance($id);

        return view('Modules\Customer\Views\statement', [
            'customer'      => $customer,
            'transactions'  => $transactions,
            'walletBalance' => $walletBalance,
        ]);
    }

    /**
     * Stream a CSV export of all customer wallet transactions.
     */
    public function exportStatement(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $txModel      = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'customer')
                                ->where('user_id', $id)
                                ->orderBy('id', 'ASC')
                                ->findAll();

        $name     = $customer->first_name . ' ' . $customer->last_name;
        $filename = 'wallet_statement_' . str_replace(' ', '_', $name) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        fputcsv($out, ['Customer Wallet Statement']);
        fputcsv($out, ['Customer:', $name]);
        fputcsv($out, ['Account ID:', '#' . $customer->id]);
        fputcsv($out, ['Phone:', $customer->phone]);
        fputcsv($out, ['Email:', $customer->email]);
        fputcsv($out, ['Generated:', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['#', 'Date', 'Ref', 'Type', 'Description', 'Credit (+)', 'Debit (-)', 'Running Balance']);

        $runningBal = 0.0;
        $rowNum     = 0;

        foreach ($transactions as $tx) {
            $rowNum++;
            $isCredit = in_array($tx['type'], ['deposit', 'refund']);
            $amount   = (float)$tx['amount'];

            if ($isCredit) {
                $runningBal += $amount;
                $credit = number_format($amount, 2);
                $debit  = '';
            } else {
                $runningBal -= $amount;
                $credit = '';
                $debit  = number_format($amount, 2);
            }

            fputcsv($out, [
                $rowNum,
                date('Y-m-d', strtotime($tx['created_at'])),
                'TXN-' . str_pad($tx['id'], 6, '0', STR_PAD_LEFT),
                ucfirst($tx['type']),
                $tx['description'] ?? '',
                $credit,
                $debit,
                ($runningBal < 0 ? '-' : '') . number_format(abs($runningBal), 2),
            ]);
        }

        fputcsv($out, []);
        fputcsv($out, ['', '', '', '', 'Closing Balance:', '', '',
            ($runningBal < 0 ? '-' : '') . number_format(abs($runningBal), 2)]);

        fclose($out);
        exit;
    }
}
