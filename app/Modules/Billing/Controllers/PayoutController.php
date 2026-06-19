<?php

namespace App\Modules\Billing\Controllers;

use App\Controllers\BaseController;
use App\Modules\Fleet\Models\DriverModel;
use App\Modules\Billing\Models\WalletTransactionModel;
use App\Modules\Billing\Services\WalletService;

/**
 * PayoutController
 * 
 * Handles driver payout requests — withdrawal of earned funds.
 * Supports cash payout, cheque, and bank transfer methods.
 */
class PayoutController extends BaseController
{
    /**
     * List all payout requests (admin view)
     * GET /finance/payouts
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $payouts = $db->query(
            "SELECT 
                wt.*, 
                CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                d.phone as driver_phone,
                d.wallet_balance,
                d.commission_rate,
                ba.bank_name, ba.account_number
             FROM wallet_transactions wt
             LEFT JOIN drivers d ON d.id = wt.user_id AND wt.user_type = 'driver'
             LEFT JOIN driver_bank_accounts ba ON ba.id = wt.bank_account_id
             WHERE wt.user_type = 'driver' AND wt.type = 'withdrawal'
             ORDER BY wt.created_at DESC
             LIMIT 100"
        )->getResultArray();

        // Pending payout stats
        $stats = $db->query(
            "SELECT
                COUNT(CASE WHEN description LIKE '%[PENDING]%' THEN 1 END) as pending_count,
                COALESCE(SUM(CASE WHEN description LIKE '%[PENDING]%' THEN amount ELSE 0 END), 0) as pending_amount,
                COUNT(*) as total_count,
                COALESCE(SUM(amount), 0) as total_amount
             FROM wallet_transactions
             WHERE user_type = 'driver' AND type = 'withdrawal'"
        )->getRow();

        return view('App\Modules\Billing\Views\payouts\index', [
            'title'   => 'Driver Payouts',
            'payouts' => $payouts,
            'stats'   => $stats,
        ]);
    }

    /**
     * Request a payout for a driver (admin-initiated)
     * POST /finance/payouts/request
     */
    public function request()
    {
        $driverId      = (int) $this->request->getPost('driver_id');
        $amount        = (float) $this->request->getPost('amount');
        $method        = $this->request->getPost('payment_method') ?? 'cash';
        $bankAccountId = $this->request->getPost('bank_account_id');
        $notes         = $this->request->getPost('notes') ?? '';

        // Validate
        if ($driverId <= 0 || $amount <= 0) {
            return redirect()->back()->with('error', 'Invalid driver or amount.');
        }

        $driverModel = new DriverModel();
        $driver = $driverModel->find($driverId);

        if (!$driver) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        // Recompute fresh balance before payout
        $currentBalance = WalletService::calculateDriverBalance(
            $driverId, 
            (float) ($driver->commission_rate ?? 25.0)
        );

        if ($amount > $currentBalance) {
            return redirect()->back()->with('error', 
                "Insufficient balance. Driver balance is \${$currentBalance}, requested \${$amount}.");
        }

        // Create withdrawal transaction
        $walletTxModel = new WalletTransactionModel();
        $txData = [
            'user_type'      => 'driver',
            'user_id'        => $driverId,
            'type'           => 'withdrawal',
            'amount'         => $amount,
            'payment_method' => $method,
            'description'    => "[PENDING] Payout request via {$method}. {$notes}",
        ];

        if (!empty($bankAccountId)) {
            $txData['bank_account_id'] = (int) $bankAccountId;
        }

        if ($walletTxModel->insert($txData)) {
            // Sync the wallet balance
            WalletService::syncBalance('driver', $driverId, (float) ($driver->commission_rate ?? 25.0));

            return redirect()->back()->with('success', 
                "Payout of \${$amount} requested for {$driver->first_name} {$driver->last_name} via {$method}.");
        }

        return redirect()->back()->with('error', 'Failed to process payout request.');
    }

    /**
     * Mark a pending payout as completed
     * POST /finance/payouts/complete/(:num)
     */
    public function complete($id)
    {
        $walletTxModel = new WalletTransactionModel();
        $tx = $walletTxModel->find($id);

        if (!$tx || $tx['type'] !== 'withdrawal') {
            return redirect()->back()->with('error', 'Payout transaction not found.');
        }

        // Update description to remove [PENDING] marker
        $description = str_replace('[PENDING] ', '[COMPLETED] ', $tx['description']);
        $walletTxModel->update($id, ['description' => $description]);

        return redirect()->back()->with('success', 'Payout marked as completed.');
    }

    /**
     * Cancel a pending payout (reverses the withdrawal)
     * POST /finance/payouts/cancel/(:num)
     */
    public function cancel($id)
    {
        $walletTxModel = new WalletTransactionModel();
        $tx = $walletTxModel->find($id);

        if (!$tx || $tx['type'] !== 'withdrawal') {
            return redirect()->back()->with('error', 'Payout transaction not found.');
        }

        if (strpos($tx['description'], '[PENDING]') === false) {
            return redirect()->back()->with('error', 'Only pending payouts can be cancelled.');
        }

        // Delete the withdrawal to reverse its effect on balance
        $walletTxModel->delete($id);

        // Resync driver balance
        $driverModel = new DriverModel();
        $driver = $driverModel->find($tx['user_id']);
        if ($driver) {
            WalletService::syncBalance('driver', $tx['user_id'], (float) ($driver->commission_rate ?? 25.0));
        }

        return redirect()->back()->with('success', 'Payout cancelled and balance restored.');
    }
}
