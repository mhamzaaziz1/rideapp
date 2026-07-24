<?php

namespace App\Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use App\Modules\Fleet\Models\DriverModel;
use App\Modules\Dispatch\Models\TripModel;

class DispatchController extends BaseController
{
    public function index()
    {
        $tripModel = new \App\Modules\Dispatch\Models\TripModel();
        
        // Fetch trips that are NOT completed or cancelled with full details
        $activeTrips = $tripModel->select('
                trips.*, 
                customers.first_name as c_first, 
                customers.last_name as c_last, 
                customers.phone as c_phone, 
                customers.email as c_email,
                customers.rating as c_rating,
                customers.wallet_balance as c_wallet_balance,
                (SELECT COUNT(*) FROM trips t2 WHERE t2.customer_id = trips.customer_id) as c_trip_count,
                
                drivers.first_name as d_first, 
                drivers.last_name as d_last, 
                drivers.phone as d_phone, 
                drivers.vehicle_model as d_vehicle, 
                drivers.license_plate as d_plate,
                drivers.rating as d_rating,
                drivers.wallet_balance as d_wallet_balance,
                (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "system" AND ratings.ratee_type = "driver") as system_rated_driver,
                (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "system" AND ratings.ratee_type = "customer") as system_rated_customer
            ')
            ->join('customers', 'customers.id = trips.customer_id', 'left')
            ->join('drivers', 'drivers.id = trips.driver_id', 'left')
            ->whereNotIn('trips.status', ['completed', 'cancelled'])
            ->orderBy('trips.created_at', 'DESC')
            ->findAll();

        $driverModel = new \App\Modules\Fleet\Models\DriverModel();
        $drivers = $driverModel->where('status', 'active')->findAll();

        $customerModel = new \App\Modules\Customer\Models\CustomerModel();
        $customers = $customerModel->where('status', 'active')->findAll();

        // Fetch Actual Inbound Calls from CommunicationLogModel
        $commLogModel = new \App\Modules\Dispatch\Models\CommunicationLogModel();
        $recentCalls = $commLogModel->select('communication_logs.*, 
                customers.first_name as c_first, customers.last_name as c_last,
                drivers.first_name as d_first, drivers.last_name as d_last')
            ->join('customers', 'customers.id = communication_logs.user_id AND communication_logs.user_type = "customer"', 'left')
            ->join('drivers', 'drivers.id = communication_logs.user_id AND communication_logs.user_type = "driver"', 'left')
            ->where('communication_logs.type', 'voice')
            ->where('communication_logs.direction', 'inbound')
            ->orderBy('communication_logs.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $calls = [];
        foreach ($recentCalls as $log) {
            $name = 'Unknown Caller';
            if ($log->c_first) $name = $log->c_first . ' ' . $log->c_last;
            elseif ($log->d_first) $name = $log->d_first . ' ' . $log->d_last;

            $calls[] = (object)[
                'id' => $log->user_id, // For selecting customer profile
                'name' => $name,
                'phone' => $log->from_number,
                'status' => 'ring', // Default to ring for display
                'is_vip' => false,
                'time' => date('H:i', strtotime($log->created_at))
            ];
        }

        $settingsFile = WRITEPATH . 'settings.json';
        $settings = [];
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }

        return view('App\Modules\Dispatch\Views\dashboard', [
            'title' => 'Dispatch Console',
            'activeTrips' => $activeTrips,
            'drivers' => $drivers,
            'customers' => $customers,
            'calls' => $calls,
            'telnyxSipUsername' => !empty($settings['telnyx_sip_username']) ? $settings['telnyx_sip_username'] : getenv('TELNYX_SIP_USERNAME'),
            'telnyxSipPassword' => !empty($settings['telnyx_sip_password']) ? $settings['telnyx_sip_password'] : getenv('TELNYX_SIP_PASSWORD')
        ]);
    }

    /**
     * API Endpoint for Real-time Map Data
     */
    public function getMapData()
    {
        $driverModel = new DriverModel();
        $tripModel = new TripModel();

        // 1. Get Active Drivers (Online)
        // Adjust logic based on your actual 'status' values
        $drivers = $driverModel->where('status', 'active')
                               ->select('id, first_name, last_name, current_lat, current_lng, status, vehicle_type, vehicle_model, rating')
                               ->findAll();

        // 2. Get Pending/Active Trips
        $trips = $tripModel->whereIn('status', ['pending', 'dispatching', 'active'])
                           ->select('id, trip_number, pickup_lat, pickup_lng, dropoff_lat, dropoff_lng, status, pickup_address, dropoff_address, driver_id')
                           ->findAll();

        // 3. Format Response
        return $this->response->setJSON([
            'drivers' => $drivers,
            'trips' => $trips,
            'timestamp' => time()
        ]);
    }
}
