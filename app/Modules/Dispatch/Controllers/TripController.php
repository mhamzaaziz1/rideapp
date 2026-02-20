<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\TripModel;
use Modules\Dispatch\Entities\Trip;

class TripController extends BaseController
{
    protected $tripModel;
    protected $pricingService;

    public function __construct()
    {
        $this->tripModel = new TripModel();
        // Load the service
        $this->pricingService = new \Modules\Pricing\Services\PricingService();
    }

    /**
     * API Endpoint to create a new trip from the Dashboard
     */
    public function create()
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Exception $e) {
            $json = null;
        }

        $data = $json ?? $this->request->getPost();
        
        $trip = new Trip($data);
        $trip->generateTripNumber();
        $trip->status = 'pending'; // Default status

        // 1. Get Coordinates (or use NY Mock)
        if (empty($data['pickup_lat'])) {
            $trip->pickup_lat = 40.7128; // NY
            $trip->pickup_lng = -74.0060;
            $trip->dropoff_lat = 40.6413; // JFK
            $trip->dropoff_lng = -73.7781;
        }

        // 2. Calculate Distance & Duration
        $distance = $this->pricingService->calculateDistance(
            $trip->pickup_lat, 
            $trip->pickup_lng, 
            $trip->dropoff_lat, 
            $trip->dropoff_lng
        );
        
        $duration = $this->pricingService->estimateDuration($distance);
        
        // 3. Calculate Fare Dynamic
        // For now, assume 'standard' vehicle type unless passed
        $vType = $data['vehicle_type'] ?? 'standard';
        $fare = $this->pricingService->calculateFare($distance, $duration, $vType);

        $trip->distance_miles = $distance;
        $trip->fare_amount = $fare;
        $trip->duration_minutes = $duration; // Add this field if exists (DB strict check might require ensuring column exists)
        
        
        if ($this->tripModel->save($trip)) {
             if ($this->request->isAJAX() || $this->request->header('Content-Type') == 'application/json') {
                 return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Trip created successfully',
                    'trip_number' => $trip->trip_number,
                    'trip_id' => $this->tripModel->getInsertID(),
                    'fare' => $fare,
                    'distance' => $distance
                 ]);
             }
             return redirect()->to('/dispatch')->with('success', 'Trip dispatched successfully: ' . $trip->trip_number);
        }

        if ($this->request->isAJAX()) {
             return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->tripModel->errors()
             ])->setStatusCode(400);
        }
        
        return redirect()->back()->withInput()->with('errors', $this->tripModel->errors());
    }

    public function new()
    {
        $customerModel = new \Modules\Customer\Models\CustomerModel();
        $driverModel = new \Modules\Fleet\Models\DriverModel();

        $data = [
            'trip' => new Trip(),
            'customers' => $customerModel->where('status', 'active')->findAll(),
            'drivers' => $driverModel->where('status', 'active')->findAll(),
            'title' => 'Create New Trip'
        ];
        return view('Modules\Dispatch\Views\trips\form', $data);
    }

    public function view($id)
    {
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/dispatch/trips')->with('error', 'Trip not found');
        }

        // Fetch related data
        $customerModel = new \Modules\Customer\Models\CustomerModel();
        $driverModel = new \Modules\Fleet\Models\DriverModel();
        
        $customer = $customerModel->find($trip->customer_id);
        $driver = $trip->driver_id ? $driverModel->find($trip->driver_id) : null;

        $data = [
            'trip' => $trip,
            'customer' => $customer,
            'driver' => $driver,
            'title' => 'Trip Details - #' . $trip->trip_number
        ];

        // Fetch Ratings for this specific trip
        $ratingModel = new \Modules\Dispatch\Models\RatingModel();
        $ratings = $ratingModel->where('trip_id', $trip->id)->findAll();

        $data['trip_driver_rating'] = null; // Customer -> Driver
        $data['trip_customer_rating'] = null; // Driver -> Customer

        foreach ($ratings as $r) {
            if ($r['rater_type'] == 'customer') {
                $data['trip_driver_rating'] = $r;
            } elseif ($r['rater_type'] == 'driver') {
                $data['trip_customer_rating'] = $r;
            }
        }
        
        return view('Modules\Dispatch\Views\trips\view', $data);
    }

    public function edit($id)
    {
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/dispatch/trips')->with('error', 'Trip not found');
        }

        $customerModel = new \Modules\Customer\Models\CustomerModel();
        $driverModel = new \Modules\Fleet\Models\DriverModel();

        $data = [
            'trip' => $trip,
            'customers' => $customerModel->findAll(), // Show all, even if inactive, for historical editing
            'drivers' => $driverModel->findAll(),
            'title' => 'Edit Trip #' . $trip->trip_number
        ];
        return view('Modules\Dispatch\Views\trips\form', $data);
    }

    public function update($id)
    {
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/dispatch/trips')->with('error', 'Trip not found');
        }

        $data = $this->request->getPost();

        // Check if this is a Quick Assign (partial update) or Full Edit
        if (!isset($data['pickup_address'])) {
            // Quick Assign Mode
            $rules = [
                'driver_id' => 'required',
                'status' => 'required'
            ];
        } else {
            // Full Edit Mode
             $rules = [
                'customer_id' => 'required',
                'pickup_address' => 'required',
                'dropoff_address' => 'required',
                'status' => 'required'
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Handle Driver Assignment Logic
        if (!empty($data['driver_id']) && $data['driver_id'] != $trip->driver_id) {
             // 1. Fetch Driver Commission Rate
             $driverModel = new \Modules\Fleet\Models\DriverModel();
             $driver = $driverModel->find($data['driver_id']);
             
             if ($driver) {
                 $rate = $driver->commission_rate ?? 20.00; // Default 20% if not set
                 $fare = $trip->fare_amount;
                 
                 // Calculate Split
                 $commissionVal = ($fare * $rate) / 100;
                 $earnings = $fare - $commissionVal;
                 
                 $data['driver_earnings'] = $earnings;
                 $data['commission_amount'] = $commissionVal;
                 
                 // Notify Driver
                 $notification = new \Modules\Dispatch\Services\NotificationService();
                 $notification->notifyDriverAssigned($driver->id, $trip->trip_number);
             }
        }

        if (!$this->tripModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update trip');
        }

        return redirect()->to('/dispatch/trips')->with('success', 'Trip updated successfully');
    }

    public function delete($id)
    {
        if ($this->tripModel->delete($id)) {
            return redirect()->to('/dispatch/trips')->with('success', 'Trip deleted successfully');
        }
        return redirect()->to('/dispatch/trips')->with('error', 'Failed to delete trip');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        if ($this->tripModel->update($id, ['status' => $status])) {
             return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function index()
    {
        // 1. Fetch All Trips with Joins
        $builder = $this->tripModel->builder();
        $builder->select('trips.*, customers.first_name as c_first, customers.last_name as c_last, drivers.first_name as d_first, drivers.last_name as d_last, drivers.vehicle_model, drivers.rating as d_rating, customers.rating as c_rating, 
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "customer") as driver_is_rated,
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "driver") as customer_is_rated');
        $builder->join('customers', 'customers.id = trips.customer_id', 'left');
        $builder->join('drivers', 'drivers.id = trips.driver_id', 'left');
        $builder->where('trips.deleted_at', null);

        // --- Filtering Logic ---
        $request = \Config\Services::request();
        $search = $request->getGet('search');
        $driverId = $request->getGet('driver_id');
        $date = $request->getGet('date');
        $status = $request->getGet('status');

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('trips.trip_number', $search);
            $builder->orLike('trips.pickup_address', $search);
            $builder->orLike('trips.dropoff_address', $search);
            $builder->orLike('customers.first_name', $search);
            $builder->orLike('customers.last_name', $search);
            $builder->orLike('drivers.first_name', $search);
            $builder->orLike('drivers.last_name', $search);
            $builder->groupEnd();
        }

        if (!empty($driverId)) {
            $builder->where('trips.driver_id', $driverId);
        }

        if (!empty($date)) {
            $builder->like('trips.created_at', $date, 'after'); // 'YYYY-MM-DD%'
        }

        // If status filter is explicitly provided, use it. Otherwise, default logic applies in view categorization
        if (!empty($status)) {
            $builder->where('trips.status', $status);
        }

        $builder->orderBy('trips.created_at', 'DESC');
        
        $allTrips = $builder->get()->getResult();

        // 2. bucket them
        $queue = [];
        $active = [];
        $history = [];

        foreach ($allTrips as $t) {
            if (in_array($t->status, ['completed', 'cancelled'])) {
                $history[] = $t;
            } elseif (in_array($t->status, ['active', 'dispatching'])) {
                $active[] = $t;
            } else {
                // Pending, or any other status goes to queue
                $queue[] = $t;
            }
        }

        // Fetch drivers for sidebar/modal
        $driverModel = new \Modules\Fleet\Models\DriverModel();
        $availableDrivers = $driverModel->where('status', 'active')->findAll();

        // Fetch customers for Quick Dispatch Modal
        $customerModel = new \Modules\Customer\Models\CustomerModel();
        $allCustomers = $customerModel->where('deleted_at', null)->orderBy('first_name', 'ASC')->findAll();

        $data = [
            'trips_queue' => $queue,
            'trips_active' => $active,
            'trips_history' => $history,
            'trips_all' => $allTrips,
            'drivers' => $availableDrivers,
            'customers' => $allCustomers,
            'active_tab' => 'all', // Default active tab is now All Trips
            'filters' => [
                'search' => $search,
                'driver_id' => $driverId,
                'date' => $date,
                'status' => $status
            ],
            
            // Keep stats for the top bar
            'total_trips' => count($allTrips),
            'in_progress' => count($active),
            'completed' => count($history), // roughly
            'revenue' => $this->tripModel->selectSum('fare_amount')->first()->fare_amount ?? 0,
            'title' => 'Dispatch Board'
        ];

        // Handle AJAX Request for filtering without reload
        if ($this->request->isAJAX()) {
            // Helper function to render list or empty state
            $renderList = function($trips, $type) {
                if (empty($trips)) {
                    $msg = ($type == 'queue') ? 'All caught up! No pending trips.' : 
                           (($type == 'active') ? 'No active trips right now.' : 
                           (($type == 'history') ? 'No history found.' : 'No trips found.'));
                    return '<div class="empty-state" style="text-align:center; padding:3rem; color:var(--text-secondary);"><p>'.$msg.'</p></div>';
                }
                $html = '';
                foreach ($trips as $t) {
                    $html .= view('Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => $type]);
                }
                return $html;
            };

            return $this->response->setJSON([
                'status' => 'success',
                'html_queue' => $renderList($queue, 'queue'),
                'html_active' => $renderList($active, 'active'),
                'html_history' => $renderList($history, 'history'),
                'html_all' => $renderList($allTrips, 'all'),
                'count_queue' => count($queue),
                'count_active' => count($active),
                'count_history' => count($history),
                'count_all' => count($allTrips)
            ]);
        }
        
        return view('Modules\Dispatch\Views\trips\index', $data);
    }
}
