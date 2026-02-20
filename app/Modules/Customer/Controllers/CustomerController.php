<?php

namespace Modules\Customer\Controllers;

use App\Controllers\BaseController;
use Modules\Customer\Models\CustomerModel;
use Modules\Customer\Models\CustomerAddressModel;
use Modules\Billing\Models\WalletTransactionModel;
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
        
        // Calculate Wallet Stats
        $totalDeposits = 0;
        $totalSpentFromWallet = 0;
        foreach($transactions as $tx) {
            if($tx['type'] == 'deposit') $totalDeposits += $tx['amount'];
            if($tx['type'] == 'payment') $totalSpentFromWallet += $tx['amount'];
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
                'wallet_balance' => $customer->wallet_balance,
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

        $newBalance = $customer->wallet_balance + ($type == 'deposit' ? $amount : -$amount);

        // Update Customer Balance
        $this->customerModel->update($customerId, ['wallet_balance' => $newBalance]);

        // Log Transaction
        $txModel = new WalletTransactionModel();
        $txModel->save([
            'user_type' => 'customer',
            'user_id' => $customerId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Transaction failed');
        }

        return redirect()->back()->with('success', 'Wallet updated successfully');
    }
}
