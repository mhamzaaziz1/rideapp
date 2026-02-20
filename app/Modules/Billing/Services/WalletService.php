<?php

namespace Modules\Billing\Services;

class WalletService
{
    /**
     * Compute the true wallet balance for a customer by summing wallet_transactions.
     *
     * Transaction types that ADD to balance:  deposit, refund
     * Transaction types that SUBTRACT:        payment, withdrawal
     *
     * @param  int $customerId
     * @return float
     */
    public static function calculateCustomerBalance(int $customerId): float
    {
        $db = \Config\Database::connect();

        $row = $db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN type IN ('deposit','refund')     THEN amount ELSE 0 END), 0) AS credits,
                COALESCE(SUM(CASE WHEN type IN ('payment','withdrawal') THEN amount ELSE 0 END), 0) AS debits
             FROM wallet_transactions
             WHERE user_type = 'customer' AND user_id = ?",
            [$customerId]
        )->getRow();

        return round((float)$row->credits - (float)$row->debits, 2);
    }

    /**
     * Compute the true wallet balance for a driver.
     *
     * Built from two sources:
     *
     * 1) Completed trips:
     *    - Cash trips  : driver physically collected cash.
     *                    Company commission OWED by driver → negative.
     *    - Card/wallet : company collected the money.
     *                    Driver's net share DUE to driver → positive.
     *
     * 2) Manual wallet_transactions:
     *    - deposit / refund        → positive
     *    - withdrawal / commission → negative
     *
     * @param  int   $driverId
     * @param  float $commissionRate  e.g. 25.0 for 25%
     * @return float
     */
    public static function calculateDriverBalance(int $driverId, float $commissionRate = 25.0): float
    {
        $db   = \Config\Database::connect();
        $rate = $commissionRate / 100;

        // ── 1. Trip-based component ──────────────────────────────────────────
        $trips = $db->query(
            "SELECT payment_method, fare_amount, driver_earnings
             FROM trips
             WHERE driver_id = ? AND status = 'completed' AND deleted_at IS NULL",
            [$driverId]
        )->getResultArray();

        $tripBalance = 0.0;

        foreach ($trips as $trip) {
            $fare = (float)$trip['fare_amount'];

            // Use stored driver_earnings if > 0, else compute from rate
            $driverNet    = (float)($trip['driver_earnings'] ?? 0);
            if ($driverNet <= 0) {
                $driverNet = $fare * (1 - $rate);
            }
            $companyShare = $fare - $driverNet;

            if (strtolower((string)($trip['payment_method'] ?? 'cash')) === 'cash') {
                // Driver physically received cash; owes company its commission
                $tripBalance -= $companyShare;
            } else {
                // Company received card/wallet payment; owes driver his net share
                $tripBalance += $driverNet;
            }
        }

        // ── 2. Wallet transaction component ─────────────────────────────────
        $row = $db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN type IN ('deposit','refund')        THEN amount ELSE 0 END), 0) AS credits,
                COALESCE(SUM(CASE WHEN type IN ('withdrawal','commission') THEN amount ELSE 0 END), 0) AS debits
             FROM wallet_transactions
             WHERE user_type = 'driver' AND user_id = ?",
            [$driverId]
        )->getRow();

        $credits = (float)$row->credits;
        $debits  = (float)$row->debits;

        return round($tripBalance + $credits - $debits, 2);
    }

    /**
     * Recompute balance and write it back to the stored wallet_balance column.
     * Call this after every wallet transaction to keep the column in sync.
     *
     * @param  string $userType       'customer' | 'driver'
     * @param  int    $userId
     * @param  float  $commissionRate Only used for driver
     * @return float  The newly computed balance
     */
    public static function syncBalance(string $userType, int $userId, float $commissionRate = 25.0): float
    {
        $db = \Config\Database::connect();

        if ($userType === 'customer') {
            $balance = self::calculateCustomerBalance($userId);
            $db->table('customers')->where('id', $userId)->update(['wallet_balance' => $balance]);
        } else {
            $balance = self::calculateDriverBalance($userId, $commissionRate);
            $db->table('drivers')->where('id', $userId)->update(['wallet_balance' => $balance]);
        }

        return $balance;
    }
}
