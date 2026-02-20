<?php

namespace Modules\Fleet\Controllers;

use App\Controllers\BaseController;
use Modules\Fleet\Models\DriverModel;
use Modules\Billing\Models\WalletTransactionModel;
use Modules\Billing\Services\WalletService;
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
        $companyCommissionRate = ($driver->commission_rate ?? 25.00);
        $rateDecimal = $companyCommissionRate / 100;

        // Calculate trip stats (for display only — wallet balance uses WalletService)
        $totalEarnings  = 0;
        $cashCollected  = 0;
        $cardRefEarnings = 0;

        foreach ($trips as $t) {
            if ($t->status == 'completed') {
                $fare = (float)$t->fare_amount;
                $totalEarnings += $fare;
                if ($t->payment_method == 'cash') {
                    $cashCollected += $fare;
                } else {
                    $cardRefEarnings += $fare;
                }
            }
        }

        $companyShare = $totalEarnings * $rateDecimal;
        $driverShare  = $totalEarnings - $companyShare;

        $owedToCompanyFromCash  = $cashCollected  * $rateDecimal;
        $owedToDriverFromCard   = $cardRefEarnings * (1 - $rateDecimal);

        // Transaction sums for display
        $totalDeposits    = 0;
        $totalWithdrawals = 0;
        foreach ($transactions as $tx) {
            if (in_array($tx['type'], ['deposit', 'refund'])) $totalDeposits    += $tx['amount'];
            if (in_array($tx['type'], ['withdrawal', 'commission'])) $totalWithdrawals += $tx['amount'];
        }

        // Wallet balance computed from trips + wallet transactions
        $computedWalletBalance = WalletService::calculateDriverBalance((int)$id, $companyCommissionRate);

        $data = [
            'driver'       => $driver,
            'transactions' => $transactions,
            'trips'        => $trips,
            'ratings'      => $ratings,
            'stats' => [
                'total_earnings'       => $totalEarnings,
                'trips_completed'      => count(array_filter($trips, fn($t) => $t->status == 'completed')),
                'cash_collected'       => $cashCollected,
                'card_earnings'        => $cardRefEarnings,
                'company_rate'         => $companyCommissionRate,
                'company_share'        => $companyShare,
                'driver_share'         => $driverShare,
                'cash_driver_has'      => $cashCollected,
                'company_cut_from_cash'=> $owedToCompanyFromCash,
                'card_payments_due'    => $owedToDriverFromCard,
                'already_paid'         => $totalWithdrawals - $totalDeposits,
                'wallet_balance'       => $computedWalletBalance,
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

        // Log Transaction
        $txModel = new WalletTransactionModel();
        $txModel->save([
            'user_type'   => 'driver',
            'user_id'     => $driverId,
            'type'        => $type,
            'amount'      => $amount,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Transaction failed');
        }

        // Sync stored wallet_balance column with computed value (trips + transactions)
        $commissionRate = $driver->commission_rate ?? 25.00;
        WalletService::syncBalance('driver', (int)$driverId, (float)$commissionRate);

        // For withdrawals, redirect to the printable cheque page
        if ($type === 'withdrawal') {
            $txId = (new WalletTransactionModel())->db->insertID();
            // Fallback: get the latest tx for this driver
            $latestTx = (new WalletTransactionModel())
                ->where('user_type', 'driver')
                ->where('user_id', $driverId)
                ->orderBy('id', 'DESC')
                ->first();
            if ($latestTx) {
                return redirect()->to(base_url('drivers/cheque/' . $latestTx['id']) . '?autoprint=1');
            }
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

    /**
     * Show a printable bank cheque for a driver wallet_transaction.
     */
    public function printCheque(int $txId)
    {
        $txModel = new WalletTransactionModel();
        $tx = $txModel->where('user_type', 'driver')
                      ->where('id', $txId)
                      ->first();

        if (!$tx) {
            return redirect()->to('/drivers')->with('error', 'Transaction not found.');
        }

        $driver = $this->driverModel->find($tx['user_id']);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        return view('Modules\Fleet\Views\drivers\cheque', [
            'driver' => $driver,
            'tx'     => $tx,
        ]);
    }

    /**
     * Render a printable full wallet statement for a driver.
     */
    public function printStatement(int $driverId)
    {
        $driver = $this->driverModel->find($driverId);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        $txModel      = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'driver')
                                ->where('user_id', $driverId)
                                ->orderBy('id', 'DESC')
                                ->findAll();

        $commissionRate    = $driver->commission_rate ?? 25.00;
        $walletBalance     = \Modules\Billing\Services\WalletService::calculateDriverBalance(
            (int)$driverId,
            (float)$commissionRate
        );

        return view('Modules\Fleet\Views\drivers\statement', [
            'driver'        => $driver,
            'transactions'  => $transactions,
            'walletBalance' => $walletBalance,
        ]);
    }

    /**
     * Stream a CSV export of all driver wallet transactions.
     */
    public function exportStatement(int $driverId)
    {
        $driver = $this->driverModel->find($driverId);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        $txModel      = new WalletTransactionModel();
        $transactions = $txModel->where('user_type', 'driver')
                                ->where('user_id', $driverId)
                                ->orderBy('id', 'ASC')
                                ->findAll();

        $driverName = $driver->first_name . ' ' . $driver->last_name;
        $filename   = 'wallet_statement_' . str_replace(' ', '_', $driverName) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fputs($out, "\xEF\xBB\xBF");

        // Info rows
        fputcsv($out, ['Driver Wallet Statement']);
        fputcsv($out, ['Driver:', $driverName]);
        fputcsv($out, ['Driver ID:', $driver->id]);
        fputcsv($out, ['Phone:', $driver->phone]);
        fputcsv($out, ['Commission Rate:', ($driver->commission_rate ?? 25) . '%']);
        fputcsv($out, ['Generated:', date('Y-m-d H:i:s')]);
        fputcsv($out, []); // blank line

        // Column headers
        fputcsv($out, ['#', 'Date', 'Ref', 'Type', 'Description', 'Credit (+)', 'Debit (-)', 'Running Balance']);

        $runningBal = 0.0;
        $rowNum     = 0;

        foreach ($transactions as $tx) {
            $rowNum++;
            $isCredit   = in_array($tx['type'], ['deposit', 'refund']);
            $amount     = (float)$tx['amount'];

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

        // Totals row
        fputcsv($out, []);
        fputcsv($out, ['', '', '', '', 'Closing Balance:', '', '', ($runningBal < 0 ? '-' : '') . number_format(abs($runningBal), 2)]);

        fclose($out);
        exit;
    }
}
