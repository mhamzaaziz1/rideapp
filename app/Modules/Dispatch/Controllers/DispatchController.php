<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Fleet\Models\DriverModel;
use Modules\Dispatch\Models\TripModel;

class DispatchController extends BaseController
{
    public function index()
    {
        $tripModel = new \Modules\Dispatch\Models\TripModel();
        
        // Fetch trips that are NOT completed or cancelled with full details
        $activeTrips = $tripModel->select('
                trips.*, 
                customers.first_name as c_first, 
                customers.last_name as c_last, 
                customers.phone as c_phone, 
                customers.email as c_email,
                customers.rating as c_rating,
                (SELECT COUNT(*) FROM trips t2 WHERE t2.customer_id = trips.customer_id) as c_trip_count,
                
                drivers.first_name as d_first, 
                drivers.last_name as d_last, 
                drivers.phone as d_phone, 
                drivers.vehicle_model as d_vehicle, 
                drivers.license_plate as d_plate,
                drivers.rating as d_rating
            ')
            ->join('customers', 'customers.id = trips.customer_id', 'left')
            ->join('drivers', 'drivers.id = trips.driver_id', 'left')
            ->whereNotIn('trips.status', ['completed', 'cancelled'])
            ->orderBy('trips.created_at', 'DESC')
            ->findAll();

        return view('Modules\Dispatch\Views\dashboard', [
            'title' => 'Dispatch Console',
            'activeTrips' => $activeTrips
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
