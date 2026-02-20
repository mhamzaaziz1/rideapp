<?php

namespace Modules\Fleet\Controllers;

use App\Controllers\BaseController;
use Modules\Fleet\Models\DriverModel;
use Modules\Billing\Models\WalletTransactionModel;
use Modules\Dispatch\Models\RatingModel;

class DriversController extends BaseController
{
    protected $driverModel;
    protected $ratingModel;

    public function __construct()
    {
        $this->driverModel = new DriverModel();
        $this->ratingModel = new RatingModel();
    }

    public function index()
    {
        $data = [];
        
        try {
            $data['drivers'] = $this->driverModel->findAll();
            $data['total_drivers'] = $this->driverModel->countAll();
            $data['active_drivers'] = $this->driverModel->where('status', 'active')->countAllResults();
            $data['inactive_drivers'] = $this->driverModel->where('status', 'inactive')->countAllResults();
            $data['total_trips'] = $this->driverModel->selectSum('total_trips')->first()->total_trips ?? 0;
        } catch (\Exception $e) {
            // Fallback for when DB table issues exist during dev
            $data['error'] = $e->getMessage();
            $data['drivers'] = [];
            $data['total_drivers'] = 0;
            $data['active_drivers'] = 0;
            $data['inactive_drivers'] = 0;
            $data['total_trips'] = 0;
        }

        $data['title'] = 'Driver Management';
        
        return view('Modules\Fleet\Views\drivers\index', $data);
    }

    public function create()
    {
        $rules = $this->driverModel->getValidationRules();
        
        // Remove 'id' from rules if it exists (usually not for insert, but good practice)
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle File Uploads (KYC + Images)
        $fileFields = ['doc_license_front', 'doc_license_back', 'doc_id_proof', 'avatar', 'vehicle_image'];
        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/drivers', $newName);
                $data[$field] = 'uploads/drivers/' . $newName;
            }
        }

        if ($this->driverModel->save($data)) {
            return redirect()->to('/drivers')->with('success', 'Driver added successfully.');
        }

        return redirect()->back()->withInput()->with('errors', $this->driverModel->errors());
    }

    public function edit($id)
    {
        $driver = $this->driverModel->find($id);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found');
        }

        $data = [
            'driver' => $driver,
            'title' => 'Edit Driver'
        ];

        return view('Modules\Fleet\Views\drivers\form', $data);
    }

    public function update($id)
    {
        $driver = $this->driverModel->find($id);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found');
        }

        $data = $this->request->getPost();
        
        // Handle File Uploads (Optional on update)
        $fileFields = ['doc_license_front', 'doc_license_back', 'doc_id_proof', 'avatar', 'vehicle_image'];
        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/drivers', $newName);
                $data[$field] = 'uploads/drivers/' . $newName;
            }
        }

        if ($this->driverModel->update($id, $data)) {
             return redirect()->to('/drivers')->with('success', 'Driver updated successfully.');
        }

        return redirect()->back()->withInput()->with('errors', $this->driverModel->errors());
    }

    public function delete($id)
    {
        if ($this->driverModel->delete($id)) {
            return redirect()->to('/drivers')->with('success', 'Driver deleted successfully.');
        }
        return redirect()->to('/drivers')->with('error', 'Failed to delete driver.');
    }

    public function updateDocStatus()
    {
        $id = $this->request->getPost('driver_id');
        $field = $this->request->getPost('doc_field'); // e.g., 'doc_license_front_status'
        $status = $this->request->getPost('status');

        if (!$id || !$field || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        // Validate field name to prevent arbitrary column updates
        $allowedFields = ['doc_license_front_status', 'doc_license_back_status', 'doc_id_proof_status'];
        if (!in_array($field, $allowedFields)) {
             return $this->response->setJSON(['success' => false, 'message' => 'Invalid document field']);
        }

        if ($this->driverModel->update($id, [$field => $status])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update']);
    }

    public function profile($id)
    {
        $driver = $this->driverModel->find($id);
        if (!$driver) {
             return redirect()->to('/drivers')->with('error', 'Driver not found');
        }

        // Fetch Wallet Transactions
        $txModel = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'driver')
                                ->where('user_id', $id)
                                ->orderBy('created_at', 'DESC')
                                ->findAll();
        
        // Mock Trip Data (replace with real TripModel if available)
        // Assuming TripModel is in Modules\Dispatch\Models\TripModel
        $trips = [];
        try {
             $tripModel = new \Modules\Dispatch\Models\TripModel();
             $trips = $tripModel->where('driver_id', $id)->orderBy('created_at', 'DESC')->findAll();
        } catch(\Exception $e) {
             // Model invalid or not found
        }

        // Fetch Ratings
        $ratings = $this->ratingModel->where('ratee_type', 'driver')
                                     ->where('ratee_id', $id)
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll();

        // Calculate Stats
        $totalEarnings = 0;
        $cashCollected = 0;
        $cardRefEarnings = 0; // Card or Wallet or Account
        $cardRefEarnings = 0; // Card or Wallet or Account
        $companyCommissionRate = ($driver->commission_rate ?? 25.00) / 100; // Use driver's rate or default 25%

        foreach($trips as $t) {
            if($t->status == 'completed') {
                $totalEarnings += $t->fare_amount;
                if($t->payment_method == 'cash') {
                    $cashCollected += $t->fare_amount;
                } else {
                    $cardRefEarnings += $t->fare_amount;
                }
            }
        }

        $companyShare = $totalEarnings * $companyCommissionRate;
        $driverShare = $totalEarnings - $companyShare;

        // Balance Breakdown
        // Driver owes company for cash trips (company commission on cash)
        // Company owes driver for card trips (driver share on card)
        
        // Actually, if driver takes cash: he has 100% of cash. He owes company 25% of that.
        // If driver takes card: company has 100% of money. Company owes driver 75% of that.
        
        $owedToCompanyFromCash = $cashCollected * $companyCommissionRate;
        $owedToDriverFromCard = $cardRefEarnings * (1 - $companyCommissionRate);

        // Calculate "Already Paid"
        // deposits = driver paying company (reduces debt)
        // withdrawals = company paying driver (increases debt / reduces company debt to driver)
        $totalDeposits = 0;
        $totalWithdrawals = 0;
        foreach($transactions as $tx) {
            if($tx['type'] == 'deposit') $totalDeposits += $tx['amount'];
            if($tx['type'] == 'withdrawal') $totalWithdrawals += $tx['amount'];
        }
        
        $alreadyPaid = $totalWithdrawals - $totalDeposits; // Net paid to driver

        $data = [
            'driver' => $driver,
            'transactions' => $transactions,
            'trips' => $trips,
            'ratings' => $ratings,
            'stats' => [
                'total_earnings' => $totalEarnings,
                'trips_completed' => count(array_filter($trips, fn($t) => $t->status == 'completed')),
                'cash_collected' => $cashCollected,
                'card_earnings' => $cardRefEarnings,
                'company_rate' => $companyCommissionRate * 100, // percentage
                'company_share' => $companyShare,
                'driver_share' => $driverShare,
                'cash_driver_has' => $cashCollected,
                'company_cut_from_cash' => $owedToCompanyFromCash,
                'card_payments_due' => $owedToDriverFromCard, // What company owes driver
                'already_paid' => $alreadyPaid
            ],
            'title' => $driver->first_name . ' ' . $driver->last_name . ' - Profile'
        ];

        return view('Modules\Fleet\Views\drivers\profile', $data);
    }

    public function addFund()
    {
        $rules = [
            'driver_id' => 'required|integer',
            'amount' => 'required|numeric|greater_than[0]',
            'type' => 'required|in_list[deposit,withdrawal]',
            'description' => 'required'
        ];

        if (!$this->validate($rules)) {
             return redirect()->back()->withInput()->with('error', 'Invalid input');
        }

        $driverId = $this->request->getPost('driver_id');
        $amount = $this->request->getPost('amount');
        $type = $this->request->getPost('type');
        $description = $this->request->getPost('description');

        $driver = $this->driverModel->find($driverId);
        if(!$driver) return redirect()->back()->with('error', 'Driver not found');

        $db = \Config\Database::connect();
        $db->transStart();

        $newBalance = $driver->wallet_balance + ($type == 'deposit' ? $amount : -$amount);

        // Update Driver Balance
        $this->driverModel->update($driverId, ['wallet_balance' => $newBalance]);

        // Log Transaction
        $txModel = new WalletTransactionModel();
        $txModel->save([
            'user_type' => 'driver',
            'user_id' => $driverId,
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

    public function updateRate()
    {
        $id = $this->request->getPost('driver_id');
        $rate = $this->request->getPost('commission_rate');
        
        if(!$id || !is_numeric($rate) || $rate < 0 || $rate > 100) {
            return redirect()->back()->with('error', 'Invalid rate');
        }
        
        if($this->driverModel->update($id, ['commission_rate' => $rate])) {
             return redirect()->back()->with('success', 'Commission rate updated');
        }
        
        return redirect()->back()->with('error', 'Failed to update rate');
    }
}
