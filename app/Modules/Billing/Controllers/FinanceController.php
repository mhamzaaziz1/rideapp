<?php

namespace Modules\Billing\Controllers;

use App\Controllers\BaseController;
use Modules\Billing\Models\InvoiceModel;
use Modules\Billing\Models\WalletTransactionModel;
use Modules\Dispatch\Models\TripModel;
use Modules\Fleet\Models\DriverModel;
use CodeIgniter\I18n\Time;

class FinanceController extends BaseController
{
    protected $invoiceModel;
    protected $tripModel;
    protected $walletModel;
    protected $driverModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->tripModel    = new TripModel();
        $this->walletModel  = new WalletTransactionModel();
        $this->driverModel  = new DriverModel();
    }

    /**
     * Main finance dashboard
     */
    public function index()
    {
        // Get filter period from query string
        $period = $this->request->getGet('period') ?? 'monthly';
        $dateRange = $this->getDateRange($period);

        // ── Core Trip-Based Metrics ──────────────────────────────────────────
        $db = \Config\Database::connect();

        // Current period stats
        $currentStats = $db->query(
            "SELECT 
                COUNT(*) as total_trips,
                COALESCE(SUM(fare_amount), 0) as total_revenue,
                COALESCE(SUM(driver_earnings), 0) as total_driver_earnings,
                COALESCE(SUM(commission_amount), 0) as total_commission,
                COALESCE(SUM(surcharge_amount), 0) as total_surcharges,
                COALESCE(AVG(fare_amount), 0) as avg_fare,
                COALESCE(SUM(distance_miles), 0) as total_miles,
                COALESCE(SUM(CASE WHEN payment_method = 'card' OR payment_method = 'Card' THEN fare_amount ELSE 0 END), 0) as card_revenue,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' OR payment_method = 'Cash' THEN fare_amount ELSE 0 END), 0) as cash_revenue,
                COALESCE(SUM(CASE WHEN payment_method = 'wallet' OR payment_method = 'Wallet' THEN fare_amount ELSE 0 END), 0) as wallet_revenue,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN fare_amount ELSE 0 END), 0) as completed_revenue,
                COALESCE(SUM(CASE WHEN status = 'cancelled' THEN fare_amount ELSE 0 END), 0) as cancelled_revenue,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_trips,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_trips,
                COUNT(CASE WHEN status = 'active' OR status = 'dispatching' THEN 1 END) as active_trips
            FROM trips 
            WHERE deleted_at IS NULL 
            AND created_at >= ? AND created_at <= ?",
            [$dateRange['start'], $dateRange['end']]
        )->getRow();

        // Previous period stats (for growth calculation)
        $prevRange = $this->getPreviousDateRange($period, $dateRange);
        $prevStats = $db->query(
            "SELECT 
                COUNT(*) as total_trips,
                COALESCE(SUM(fare_amount), 0) as total_revenue,
                COALESCE(SUM(commission_amount), 0) as total_commission,
                COALESCE(SUM(driver_earnings), 0) as total_driver_earnings
            FROM trips 
            WHERE deleted_at IS NULL 
            AND created_at >= ? AND created_at <= ?",
            [$prevRange['start'], $prevRange['end']]
        )->getRow();

        // ── Invoice / Payment Stats ─────────────────────────────────────────
        $invoiceStats = $db->query(
            "SELECT 
                COUNT(*) as total_invoices,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN status = 'void' THEN amount ELSE 0 END), 0) as void_amount,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN status = 'unpaid' THEN 1 END) as unpaid_count
            FROM invoices 
            WHERE deleted_at IS NULL 
            AND created_at >= ? AND created_at <= ?",
            [$dateRange['start'], $dateRange['end']]
        )->getRow();

        // ── Ledger / Sanity Check Stats ────────────────────────────────────────
        $ledgerStats = $db->query(
            "SELECT 
                COALESCE(SUM(balance), 0) as system_liability 
             FROM ledgers 
             WHERE owner_type IN ('customer', 'driver')"
        )->getRow();

        // 24-hr daily revenue from Commission ledger transactions
        $dailyRevenue = $db->query(
            "SELECT COALESCE(SUM(amount), 0) as daily_revenue 
             FROM ledger_transactions 
             WHERE transaction_type = 'Commission' 
             AND DATE(created_at) = CURRENT_DATE()"
        )->getRow()->daily_revenue;

        $sanityCheck = $db->query(
            "SELECT 
               DATE(t.created_at) as operation_date,
               SUM(CASE WHEN source.owner_type = 'customer' THEN t.amount ELSE 0 END) as Total_Customer_Debits,
               SUM(CASE WHEN dest.owner_type = 'driver' THEN t.amount ELSE 0 END) as Driver_Credits,
               SUM(CASE WHEN dest.owner_type = 'company_revenue' THEN t.amount ELSE 0 END) as Company_Revenue,
               (SUM(CASE WHEN source.owner_type = 'customer' THEN t.amount ELSE 0 END) - 
                SUM(CASE WHEN dest.owner_type IN ('driver', 'company_revenue') THEN t.amount ELSE 0 END)) as Discrepancy
            FROM ledger_transactions as t
            JOIN ledgers source ON source.id = t.source_ledger_id
            JOIN ledgers dest ON dest.id = t.destination_ledger_id
            WHERE t.transaction_type IN ('Trip','Commission') 
            GROUP BY DATE(t.created_at)
            ORDER BY operation_date DESC
            LIMIT 30"
        )->getResultArray();

        // Previous Wallet stats fallback (for views that rely on it)
        $walletStats = (object)[
            'total_deposits' => 0,
            'total_withdrawals' => 0,
            'total_refunds' => 0,
            'total_commissions_paid' => 0
        ];

        // ── Driver Payout Summary ───────────────────────────────────────────
        $driverPayouts = $db->query(
            "SELECT 
                d.id, d.first_name, d.last_name, d.commission_rate, d.wallet_balance,
                COUNT(t.id) as trip_count,
                COALESCE(SUM(t.fare_amount), 0) as total_fares,
                COALESCE(SUM(t.driver_earnings), 0) as total_earnings,
                COALESCE(SUM(t.commission_amount), 0) as total_commission
            FROM drivers d
            LEFT JOIN trips t ON t.driver_id = d.id 
                AND t.status = 'completed' 
                AND t.deleted_at IS NULL
                AND t.created_at >= ? AND t.created_at <= ?
            WHERE d.deleted_at IS NULL
            GROUP BY d.id
            ORDER BY total_earnings DESC
            LIMIT 20",
            [$dateRange['start'], $dateRange['end']]
        )->getResultArray();

        // ── Monthly Trend Data (last 12 months) ─────────────────────────────
        $monthlyTrend = $db->query(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                DATE_FORMAT(created_at, '%b %Y') as month_label,
                COUNT(*) as trips,
                COALESCE(SUM(fare_amount), 0) as revenue,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earnings), 0) as driver_pay
            FROM trips 
            WHERE deleted_at IS NULL AND status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month_key, month_label
            ORDER BY month_key ASC"
        )->getResultArray();

        // ── Recent Transactions (Trips + Invoices combined) ─────────────────
        $recentTrips = $db->query(
            "SELECT 
                t.id, t.trip_number, t.fare_amount, t.driver_earnings, t.commission_amount,
                t.surcharge_amount, t.payment_method, t.status, t.created_at,
                t.distance_miles, t.duration_minutes, t.vehicle_type,
                CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                d.commission_rate as driver_commission_rate
            FROM trips t
            LEFT JOIN customers c ON c.id = t.customer_id
            LEFT JOIN drivers d ON d.id = t.driver_id
            WHERE t.deleted_at IS NULL
            AND t.created_at >= ? AND t.created_at <= ?
            ORDER BY t.created_at DESC
            LIMIT 100",
            [$dateRange['start'], $dateRange['end']]
        )->getResultArray();

        // ── Growth Calculations ─────────────────────────────────────────────
        $growthRevenue = $this->calculateGrowth($currentStats->total_revenue, $prevStats->total_revenue);
        $growthTrips   = $this->calculateGrowth($currentStats->total_trips, $prevStats->total_trips);
        $growthCommission = $this->calculateGrowth($currentStats->total_commission, $prevStats->total_commission);
        $growthDriverPay  = $this->calculateGrowth($currentStats->total_driver_earnings, $prevStats->total_driver_earnings);

        // ── Company net: commission + surcharges ────────────────────────────
        $companyEarnings = (float)$currentStats->total_commission + (float)$currentStats->total_surcharges;

        $data = [
            'title'            => 'Financial Dashboard',
            'period'           => $period,
            'date_range'       => $dateRange,

            // New Ledger KPIs
            'system_liability' => (float)($ledgerStats->system_liability ?? 0),
            'daily_revenue'    => (float)$dailyRevenue,
            'sanity_checks'    => $sanityCheck,

             // Core KPIs
            'total_revenue'    => (float)$currentStats->total_revenue,
            'company_earnings' => $companyEarnings,
            'driver_payouts'   => (float)$currentStats->total_driver_earnings,
            'pending_amount'   => (float)$invoiceStats->pending_amount,
            'avg_fare'         => (float)$currentStats->avg_fare,
            'total_trips'      => (int)$currentStats->total_trips,
            'completed_trips'  => (int)$currentStats->completed_trips,
            'cancelled_trips'  => (int)$currentStats->cancelled_trips,
            'active_trips'     => (int)$currentStats->active_trips,
            'total_miles'      => (float)$currentStats->total_miles,
            'total_commission' => (float)$currentStats->total_commission,
            'total_surcharges' => (float)$currentStats->total_surcharges,

            // Payment method splits
            'card_revenue'     => (float)$currentStats->card_revenue,
            'cash_revenue'     => (float)$currentStats->cash_revenue,
            'wallet_revenue'   => (float)$currentStats->wallet_revenue,

            // Invoice stats
            'paid_invoices'    => (int)$invoiceStats->paid_count,
            'unpaid_invoices'  => (int)$invoiceStats->unpaid_count,
            'paid_amount'      => (float)$invoiceStats->paid_amount,

            // Wallet stats
            'total_deposits'   => (float)$walletStats->total_deposits,
            'total_withdrawals'=> (float)$walletStats->total_withdrawals,
            'total_refunds'    => (float)$walletStats->total_refunds,

            // Growth
            'growth_revenue'   => $growthRevenue,
            'growth_trips'     => $growthTrips,
            'growth_commission'=> $growthCommission,
            'growth_driver_pay'=> $growthDriverPay,

            // Tables
            'driver_payouts_list' => $driverPayouts,
            'monthly_trend'       => $monthlyTrend,
            'recent_trips'        => $recentTrips,
        ];

        return view('Modules\Billing\Views\finance\index', $data);
    }

    /**
     * AJAX: Get print data for a single trip
     */
    public function printTrip($tripId)
    {
        $db = \Config\Database::connect();
        $trip = $db->query(
            "SELECT 
                t.*, 
                CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone as customer_phone, c.email as customer_email,
                CONCAT(d.first_name, ' ', d.last_name) as driver_name, d.phone as driver_phone, d.license_plate, d.vehicle_type as driver_vehicle_type
            FROM trips t
            LEFT JOIN customers c ON c.id = t.customer_id
            LEFT JOIN drivers d ON d.id = t.driver_id
            WHERE t.id = ?",
            [(int)$tripId]
        )->getRow();

        if (!$trip) {
            return $this->response->setJSON(['error' => 'Trip not found'])->setStatusCode(404);
        }

        return $this->response->setJSON(['trip' => $trip]);
    }

    /**
     * AJAX: Bulk print multiple trips
     */
    public function bulkPrint()
    {
        $ids = $this->request->getJSON(true)['ids'] ?? [];
        if (empty($ids)) {
            return $this->response->setJSON(['error' => 'No IDs provided'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $trips = $db->query(
            "SELECT 
                t.*, 
                CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                CONCAT(d.first_name, ' ', d.last_name) as driver_name
            FROM trips t
            LEFT JOIN customers c ON c.id = t.customer_id
            LEFT JOIN drivers d ON d.id = t.driver_id
            WHERE t.id IN ({$placeholders})
            ORDER BY t.created_at DESC",
            array_map('intval', $ids)
        )->getResultArray();

        return $this->response->setJSON(['trips' => $trips]);
    }

    /**
     * AJAX: Export CSV for the current filter
     */
    public function exportCsv()
    {
        $period = $this->request->getGet('period') ?? 'monthly';
        $dateRange = $this->getDateRange($period);

        $db = \Config\Database::connect();
        $trips = $db->query(
            "SELECT 
                t.trip_number, t.fare_amount, t.driver_earnings, t.commission_amount,
                t.surcharge_amount, t.payment_method, t.status, t.created_at,
                t.distance_miles, t.duration_minutes, t.vehicle_type,
                CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                CONCAT(d.first_name, ' ', d.last_name) as driver_name
            FROM trips t
            LEFT JOIN customers c ON c.id = t.customer_id
            LEFT JOIN drivers d ON d.id = t.driver_id
            WHERE t.deleted_at IS NULL
            AND t.created_at >= ? AND t.created_at <= ?
            ORDER BY t.created_at DESC",
            [$dateRange['start'], $dateRange['end']]
        )->getResultArray();

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="financials_' . $period . '_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Trip #', 'Customer', 'Driver', 'Fare', 'Driver Earnings', 'Commission', 'Surcharge', 'Payment', 'Status', 'Date', 'Distance', 'Duration', 'Vehicle']);
        foreach ($trips as $t) {
            fputcsv($output, [
                $t['trip_number'], $t['customer_name'], $t['driver_name'],
                $t['fare_amount'], $t['driver_earnings'], $t['commission_amount'],
                $t['surcharge_amount'], $t['payment_method'], $t['status'],
                $t['created_at'], $t['distance_miles'], $t['duration_minutes'], $t['vehicle_type']
            ]);
        }
        fclose($output);

        return $this->response;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getDateRange(string $period): array
    {
        $now = Time::now();
        switch ($period) {
            case 'monthly':
                return [
                    'start' => $now->toDateString() === $now->toDateString() ? date('Y-m-01') : $now->format('Y-m-01'),
                    'end'   => date('Y-m-t 23:59:59'),
                    'label' => date('F Y'),
                ];
            case 'quarterly':
                $quarter = ceil(date('n') / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                return [
                    'start' => date('Y') . '-' . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . '-01',
                    'end'   => date('Y-m-t 23:59:59', strtotime(date('Y') . '-' . str_pad($startMonth + 2, 2, '0', STR_PAD_LEFT) . '-01')),
                    'label' => 'Q' . $quarter . ' ' . date('Y'),
                ];
            case 'half_year':
                $half = date('n') <= 6 ? 1 : 2;
                return [
                    'start' => date('Y') . ($half === 1 ? '-01-01' : '-07-01'),
                    'end'   => date('Y') . ($half === 1 ? '-06-30 23:59:59' : '-12-31 23:59:59'),
                    'label' => ($half === 1 ? 'H1' : 'H2') . ' ' . date('Y'),
                ];
            case 'yearly':
                return [
                    'start' => date('Y') . '-01-01',
                    'end'   => date('Y') . '-12-31 23:59:59',
                    'label' => date('Y'),
                ];
            case 'last_year':
                $lastYear = date('Y') - 1;
                return [
                    'start' => $lastYear . '-01-01',
                    'end'   => $lastYear . '-12-31 23:59:59',
                    'label' => (string)$lastYear,
                ];
            case 'all':
                return [
                    'start' => '2000-01-01',
                    'end'   => date('Y-m-d 23:59:59'),
                    'label' => 'All Time',
                ];
            default:
                return [
                    'start' => date('Y-m-01'),
                    'end'   => date('Y-m-t 23:59:59'),
                    'label' => date('F Y'),
                ];
        }
    }

    private function getPreviousDateRange(string $period, array $currentRange): array
    {
        switch ($period) {
            case 'monthly':
                return [
                    'start' => date('Y-m-01', strtotime('-1 month', strtotime($currentRange['start']))),
                    'end'   => date('Y-m-t 23:59:59', strtotime('-1 month', strtotime($currentRange['start']))),
                ];
            case 'quarterly':
                return [
                    'start' => date('Y-m-d', strtotime('-3 months', strtotime($currentRange['start']))),
                    'end'   => date('Y-m-d 23:59:59', strtotime('-1 day', strtotime($currentRange['start']))),
                ];
            case 'half_year':
                return [
                    'start' => date('Y-m-d', strtotime('-6 months', strtotime($currentRange['start']))),
                    'end'   => date('Y-m-d 23:59:59', strtotime('-1 day', strtotime($currentRange['start']))),
                ];
            case 'yearly':
            case 'last_year':
                return [
                    'start' => date('Y-m-d', strtotime('-1 year', strtotime($currentRange['start']))),
                    'end'   => date('Y-m-d 23:59:59', strtotime('-1 day', strtotime($currentRange['start']))),
                ];
            default:
                return [
                    'start' => date('Y-m-01', strtotime('-1 month')),
                    'end'   => date('Y-m-t 23:59:59', strtotime('-1 month')),
                ];
        }
    }

    private function calculateGrowth($current, $previous): array
    {
        $current  = (float)$current;
        $previous = (float)$previous;

        if ($previous == 0) {
            return [
                'percent' => $current > 0 ? 100 : 0,
                'direction' => $current > 0 ? 'up' : 'flat',
                'value' => $current,
            ];
        }

        $percent = round((($current - $previous) / $previous) * 100, 1);
        return [
            'percent'   => abs($percent),
            'direction' => $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat'),
            'value'     => round($current - $previous, 2),
        ];
    }
}
