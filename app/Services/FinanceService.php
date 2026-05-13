<?php

namespace App\Services;

use App\Models\LedgerModel;
use App\Models\LedgerTransactionModel;
use Modules\Dispatch\Models\TripModel;
use Modules\Fleet\Models\DriverModel;

class FinanceService
{
    /**
     * Valid status transitions for the trip state machine.
     * Key = current status, Value = array of allowed next statuses.
     */
    public const STATUS_TRANSITIONS = [
        'pending'     => ['active', 'dispatching', 'cancelled'],
        'dispatching' => ['active', 'pending', 'cancelled'],
        'active'      => ['completed', 'cancelled'],
        'completed'   => [],          // Terminal state — no further transitions
        'cancelled'   => ['pending'], // Can be re-queued if needed
    ];

    /**
     * Check if a status transition is valid
     */
    public static function isValidTransition(string $from, string $to): bool
    {
        $from = strtolower($from);
        $to   = strtolower($to);

        if (!isset(self::STATUS_TRANSITIONS[$from])) {
            return false;
        }

        return in_array($to, self::STATUS_TRANSITIONS[$from], true);
    }

    /**
     * Completes the financial action for a trip, calculating double-entry payouts securely
     * using string-based decimal methods (if bcmath available) or float mapping, protecting against rounding errors.
     * 
     * IDEMPOTENT: Will not process the same trip twice. Checks for existing ledger transactions
     * before proceeding.
     * 
     * @param int $tripId
     * @return bool
     */
    public function completeTripFinance(int $tripId): bool
    {
        $db = \Config\Database::connect();
        $tripModel = new TripModel();
        
        $trip = $tripModel->find($tripId);
        if (!$trip) {
            log_message('error', "[FinanceService] Trip #{$tripId} not found.");
            return false;
        }

        // ── IDEMPOTENCY CHECK ─────────────────────────────────────────
        // If ledger transactions already exist for this trip, skip processing.
        $txModel = new LedgerTransactionModel();
        $existingTx = $txModel->where('reference_id', (string) $tripId)
                              ->where('transaction_type', 'Trip')
                              ->where('status', 'Completed')
                              ->countAllResults();

        if ($existingTx > 0) {
            log_message('warning', "[FinanceService] Trip #{$tripId} already processed. Skipping duplicate finance.");
            return true; // Already processed — idempotent success
        }

        $db->transBegin(); // Use transBegin/transCommit for explicit control

        try {
            // Get rate
            $driverModel = new DriverModel();
            $driver = $driverModel->find($trip->driver_id);

            if (!$driver) {
                $db->transRollback();
                log_message('error', "[FinanceService] Driver #{$trip->driver_id} not found for trip #{$tripId}.");
                return false;
            }

            $commissionRate = $driver->commission_rate ?? 20.00; // Default 20%
            
            $grossFare = number_format($trip->fare_amount ?? 0, 4, '.', '');
            $commissionRateStr = number_format($commissionRate, 4, '.', '');
            
            // Decimal safe math
            if (function_exists('bcmul')) {
                $commissionAmount = bcmul($grossFare, bcdiv($commissionRateStr, '100', 4), 4);
                $netPayout = bcsub($grossFare, $commissionAmount, 4);
            } else {
                $commissionAmount = number_format(($grossFare * $commissionRate) / 100, 4, '.', '');
                $netPayout = number_format($grossFare - $commissionAmount, 4, '.', '');
            }

            $ledgerModel = new LedgerModel();

            // Fetch or create ledgers
            $customerLedger = $this->getOrCreateLedger('customer', $trip->customer_id);
            $driverLedger = $this->getOrCreateLedger('driver', $trip->driver_id);
            $companyLedger = $this->getOrCreateLedger('company_revenue', null);

            // Row-level lock using parameterized query (FIXES SQL injection)
            $db->query(
                "SELECT id FROM ledgers WHERE id IN (?, ?, ?) FOR UPDATE",
                [(int) $customerLedger->id, (int) $driverLedger->id, (int) $companyLedger->id]
            );

            // Refetch fresh after lock to avoid overwrite
            $customerLedger = $ledgerModel->find($customerLedger->id);
            $driverLedger = $ledgerModel->find($driverLedger->id);
            $companyLedger = $ledgerModel->find($companyLedger->id);

            // Deduct customer
            $newCustBalance = function_exists('bcsub') ? bcsub($customerLedger->balance, $grossFare, 4) : ($customerLedger->balance - $grossFare);
            $ledgerModel->update($customerLedger->id, ['balance' => $newCustBalance]);

            // Credit driver
            $newDriverBalance = function_exists('bcadd') ? bcadd($driverLedger->balance, $netPayout, 4) : ($driverLedger->balance + $netPayout);
            $ledgerModel->update($driverLedger->id, ['balance' => $newDriverBalance]);

            // Credit company
            $newCompanyBalance = function_exists('bcadd') ? bcadd($companyLedger->balance, $commissionAmount, 4) : ($companyLedger->balance + $commissionAmount);
            $ledgerModel->update($companyLedger->id, ['balance' => $newCompanyBalance]);

            // Record transactions
            $txModel->insert([
                'source_ledger_id' => $customerLedger->id,
                'destination_ledger_id' => $driverLedger->id,
                'transaction_type' => 'Trip',
                'amount' => $netPayout,
                'status' => 'Completed',
                'reference_id' => (string) $tripId
            ]);

            $txModel->insert([
                'source_ledger_id' => $customerLedger->id,
                'destination_ledger_id' => $companyLedger->id,
                'transaction_type' => 'Commission',
                'amount' => $commissionAmount,
                'status' => 'Completed',
                'reference_id' => (string) $tripId
            ]);

            // Update trip with the calculated financial fields
            $tripModel->update($tripId, [
                'driver_earnings'   => $netPayout,
                'commission_amount' => $commissionAmount,
            ]);

            $db->transCommit();

            log_message('info', "[FinanceService] ✓ Trip #{$tripId} finalized: Fare={$grossFare}, Driver={$netPayout}, Commission={$commissionAmount}");
            return true;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[FinanceService] Exception on trip #' . $tripId . ': ' . $e->getMessage());
            return false;
        }
    }

    private function getOrCreateLedger(string $type, ?int $id): object
    {
        $ledgerModel = new LedgerModel();
        
        $where = ['owner_type' => $type];
        if ($id !== null) {
            $where['owner_id'] = $id;
        }

        $ledger = $ledgerModel->where($where)->first();

        if (!$ledger) {
            $ledgerId = $ledgerModel->insert(array_merge($where, [
                'balance' => '0.0000',
                'currency' => 'USD'
            ]));
            $ledger = $ledgerModel->find($ledgerId);
        }

        return $ledger;
    }
}
