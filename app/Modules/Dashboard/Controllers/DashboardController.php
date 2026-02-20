<?php

namespace Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\TripModel;
use Modules\Fleet\Models\DriverModel;
use Modules\Customer\Models\CustomerModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $tripModel = new TripModel();
        $driverModel = new DriverModel();
        $customerModel = new CustomerModel();

        // --- 1. Key Performance Indicators (KPIs) ---
        $totalTrips = $tripModel->countAllResults();
        $completedTrips = $tripModel->where('status', 'completed')->countAllResults();
        $cancelledTrips = $tripModel->groupStart()->where('status', 'cancelled')->orWhere('status', 'cancelled_driver')->orWhere('status', 'cancelled_customer')->groupEnd()->countAllResults();
        
        $activeTrips = $tripModel->whereIn('status', ['active', 'dispatching', 'arrived', 'started'])->countAllResults();
        $pendingRequests = $tripModel->where('status', 'pending')->countAllResults();
        
        $totalDrivers = $driverModel->countAllResults();
        $onlineDrivers = $driverModel->where('status', 'active')->countAllResults(); 
        
        $totalCustomers = $customerModel->where('deleted_at', null)->countAllResults();
        $newCustomersThisMonth = $customerModel->where('created_at >=', date('Y-m-01 00:00:00'))->countAllResults();
        
        // Revenue Calculation
        $revenueData = $tripModel->where('status', 'completed')->selectSum('fare_amount')->get()->getRow();
        $totalRevenue = $revenueData->fare_amount ?? 0;
        
        // Average Fare
        $avgFare = $completedTrips > 0 ? ($totalRevenue / $completedTrips) : 0;

        // --- 2. Chart Data Generation (Last 30 Days) ---
        $db = \Config\Database::connect();
        
        // Daily Activity (Trips & Revenue)
        $dailyActivityQuery = $db->query("
            SELECT 
                DATE(created_at) as date, 
                COUNT(*) as trip_count,
                SUM(CASE WHEN status = 'completed' THEN fare_amount ELSE 0 END) as revenue
            FROM trips 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at) 
            ORDER BY DATE(created_at) ASC
        ");
        $dailyActivity = $dailyActivityQuery->getResultArray();

        // Customer Growth (Last 30 Days)
        $customerGrowthQuery = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM customers 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
            GROUP BY DATE(created_at) 
            ORDER BY DATE(created_at) ASC
        ");
        $customerGrowth = $customerGrowthQuery->getResultArray();

        // Trip Status Distribution
        $statusDistributionQuery = $db->query("
            SELECT status, COUNT(*) as count 
            FROM trips 
            GROUP BY status
        ");
        $statusDistribution = $statusDistributionQuery->getResultArray();

        // Vehicle Type Usage (Based on Trips)
        $vehicleUsageQuery = $db->query("
            SELECT vehicle_type, COUNT(*) as count 
            FROM trips 
            WHERE vehicle_type IS NOT NULL AND vehicle_type != ''
            GROUP BY vehicle_type
            ORDER BY count DESC
            LIMIT 5
        ");
        $vehicleUsage = $vehicleUsageQuery->getResultArray();

        // --- 3. Recent & Top Lists ---
        $recentTrips = $tripModel->orderBy('created_at', 'DESC')->limit(5)->find();
        $topDrivers = $driverModel->orderBy('rating', 'DESC')->limit(5)->find();
        $recentCustomers = $customerModel->orderBy('created_at', 'DESC')->limit(5)->find();

        $data = [
            'metrics' => [
                'total_trips' => $totalTrips,
                'completed_trips' => $completedTrips,
                'cancelled_trips' => $cancelledTrips,
                'active_trips' => $activeTrips,
                'pending_requests' => $pendingRequests,
                'total_revenue' => $totalRevenue,
                'avg_fare' => $avgFare,
                'total_drivers' => $totalDrivers,
                'online_drivers' => $onlineDrivers,
                'total_customers' => $totalCustomers,
                'new_customers_month' => $newCustomersThisMonth,
            ],
            'charts' => [
                'daily_activity' => $dailyActivity,
                'customer_growth' => $customerGrowth,
                'status_distribution' => $statusDistribution,
                'vehicle_usage' => $vehicleUsage,
            ],
            'lists' => [
                'recent_trips' => $recentTrips,
                'top_drivers' => $topDrivers,
                'recent_customers' => $recentCustomers,
            ],
            'title' => 'Dashboard Overview'
        ];

        return view('Modules\Dashboard\Views\dashboard\index', $data);
    }
}
