<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\TripModel;
use Modules\Dispatch\Entities\Trip;
use App\Services\FinanceService;

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

        // 1. Get Coordinates
        $trip->pickup_lat = $data['pickup_lat'] ?? null;
        $trip->pickup_lng = $data['pickup_lng'] ?? null;
        $trip->dropoff_lat = $data['dropoff_lat'] ?? null;
        $trip->dropoff_lng = $data['dropoff_lng'] ?? null;

        // 2. Distance, Duration, and Fare
        $vType = $data['vehicle_type'] ?? 'standard';

        // Prefer values calculated by the frontend (so what the dispatcher saw is exactly what is saved)
        if (!empty($data['calculated_fare'])) {
            $distance = (float)($data['distance_miles'] ?? 0);
            $duration = (int)($data['duration_minutes'] ?? 0);
            $fare     = (float)$data['calculated_fare'];
        } else {
            // Fallback to server-side estimation
            $distance = $this->pricingService->calculateDistance(
                $trip->pickup_lat, 
                $trip->pickup_lng, 
                $trip->dropoff_lat, 
                $trip->dropoff_lng
            );
            $duration = $this->pricingService->estimateDuration($distance);
            $fare     = $this->pricingService->calculateFare($distance, $duration, $vType);
        }

        $trip->distance_miles = $distance;
        $trip->fare_amount = $fare;
        $trip->duration_minutes = $duration;
        
        
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
        
        $disputeModel = new \Modules\Dispatch\Models\DisputeModel();
        $data['dispute'] = $disputeModel->where('trip_id', $trip->id)->orderBy('created_at', 'DESC')->first();
        
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

        // Validate status transition if status is changing
        if (isset($data['status']) && strtolower($data['status']) !== strtolower($trip->status)) {
            if (!FinanceService::isValidTransition($trip->status, $data['status'])) {
                return redirect()->back()->withInput()->with('error', 
                    'Invalid status transition: ' . ucfirst($trip->status) . ' → ' . ucfirst($data['status']));
            }
        }

        if (!$this->tripModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update trip');
        }

        // Trigger Finance Ledger Logic if transitioning to completed (idempotent)
        if (isset($data['status']) && strtolower($data['status']) === 'completed' && strtolower($trip->status) !== 'completed') {
             $financeService = new FinanceService();
             $financeService->completeTripFinance($id);
        }

        return redirect()->to('/dispatch/trips')->with('success', 'Trip updated successfully');
    }

    public function delete($id)
    {
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/dispatch/trips')->with('error', 'Trip not found');
        }

        // Prevent deleting completed or active trips (audit trail protection)
        if (in_array(strtolower($trip->status), ['completed', 'active'])) {
            return redirect()->to('/dispatch/trips')->with('error', 'Cannot delete a trip that is ' . $trip->status . '. Cancel it first.');
        }

        if ($this->tripModel->delete($id)) {
            return redirect()->to('/dispatch/trips')->with('success', 'Trip deleted successfully');
        }
        return redirect()->to('/dispatch/trips')->with('error', 'Failed to delete trip');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $newStatus = strtolower($this->request->getPost('status') ?? '');

        if (!$id || !$newStatus) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $trip = $this->tripModel->find($id);
        if (!$trip) {
            return $this->response->setJSON(['success' => false, 'message' => 'Trip not found']);
        }

        $currentStatus = strtolower($trip->status ?? 'pending');

        // STATE MACHINE: Validate the transition
        if (!FinanceService::isValidTransition($currentStatus, $newStatus)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid transition: ' . ucfirst($currentStatus) . ' → ' . ucfirst($newStatus)
            ]);
        }

        $updateData = ['status' => $newStatus];

        // Set timestamps on key transitions
        if ($newStatus === 'active' && empty($trip->started_at)) {
            $updateData['started_at'] = date('Y-m-d H:i:s');
        }
        if ($newStatus === 'completed' && empty($trip->completed_at)) {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->tripModel->update($id, $updateData)) {
             // Trigger Finance Ledger Logic if completed (idempotent — safe to call multiple times)
             if ($newStatus === 'completed' && $currentStatus !== 'completed') {
                  $financeService = new FinanceService();
                  $financeService->completeTripFinance($id);
             }

             return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function index()
    {
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        // --- Filter parameters ---
        $search   = $request->getGet('search');
        $driverId = $request->getGet('driver_id');
        $fromDate = $request->getGet('from_date');
        $toDate   = $request->getGet('to_date');
        $date     = $request->getGet('date');
        $status   = $request->getGet('status');
        $page     = max(1, (int) ($request->getGet('page') ?? 1));
        $perPage  = 50;

        // ── Helper: apply common filters to a builder ───────────────
        $applyFilters = function ($builder) use ($search, $driverId, $fromDate, $toDate, $date, $status) {
            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('trips.trip_number', $search);
                $builder->orLike('trips.pickup_address', $search);
                $builder->orLike('trips.dropoff_address', $search);
                $builder->orLike('c.first_name', $search);
                $builder->orLike('c.last_name', $search);
                $builder->orLike('d.first_name', $search);
                $builder->orLike('d.last_name', $search);
                $builder->groupEnd();
            }
            if (!empty($driverId))  $builder->where('trips.driver_id', $driverId);
            if (!empty($fromDate))  $builder->where('trips.created_at >=', $fromDate . ' 00:00:00');
            if (!empty($toDate))    $builder->where('trips.created_at <=', $toDate . ' 23:59:59');
            if (!empty($date) && empty($fromDate) && empty($toDate)) {
                $builder->like('trips.created_at', $date, 'after');
            }
            if (!empty($status))    $builder->where('trips.status', $status);
        };

        // ── Rating subquery select ──────────────────────────────────
        $ratingSelect = ',
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "customer") as driver_is_rated_by_customer,
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "driver") as customer_is_rated_by_driver,
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "system" AND ratings.ratee_type = "driver") as system_rated_driver,
            (SELECT COUNT(*) FROM ratings WHERE ratings.trip_id = trips.id AND ratings.rater_type = "system" AND ratings.ratee_type = "customer") as system_rated_customer';

        $baseSelect = 'trips.*, c.first_name as c_first, c.last_name as c_last, c.wallet_balance as c_wallet_balance, d.first_name as d_first, d.last_name as d_last, d.vehicle_model, d.rating as d_rating, d.wallet_balance as d_wallet_balance, c.rating as c_rating';

        // ── 1. Queue (pending) — always load ALL (small set) ────────
        $queueBuilder = $db->table('trips');
        $queueBuilder->select($baseSelect . $ratingSelect);
        $queueBuilder->join('customers c', 'c.id = trips.customer_id', 'left');
        $queueBuilder->join('drivers d', 'd.id = trips.driver_id', 'left');
        $queueBuilder->where('trips.deleted_at', null);
        $queueBuilder->whereNotIn('trips.status', ['completed', 'cancelled', 'active', 'dispatching']);
        $applyFilters($queueBuilder);
        $queueBuilder->orderBy('trips.created_at', 'DESC');
        $queue = $queueBuilder->get()->getResult();

        // ── 2. Active (active/dispatching) — always load ALL ────────
        $activeBuilder = $db->table('trips');
        $activeBuilder->select($baseSelect . $ratingSelect);
        $activeBuilder->join('customers c', 'c.id = trips.customer_id', 'left');
        $activeBuilder->join('drivers d', 'd.id = trips.driver_id', 'left');
        $activeBuilder->where('trips.deleted_at', null);
        $activeBuilder->whereIn('trips.status', ['active', 'dispatching']);
        $applyFilters($activeBuilder);
        $activeBuilder->orderBy('trips.created_at', 'DESC');
        $active = $activeBuilder->get()->getResult();

        // ── 3. History (completed/cancelled) — PAGINATED ────────────
        $historyCountBuilder = $db->table('trips');
        $historyCountBuilder->join('customers c', 'c.id = trips.customer_id', 'left');
        $historyCountBuilder->join('drivers d', 'd.id = trips.driver_id', 'left');
        $historyCountBuilder->where('trips.deleted_at', null);
        $historyCountBuilder->whereIn('trips.status', ['completed', 'cancelled']);
        $applyFilters($historyCountBuilder);
        $totalHistory = $historyCountBuilder->countAllResults(); // Default resets the builder state

        $historyBuilder = $db->table('trips');
        $historyBuilder->select($baseSelect . $ratingSelect);
        $historyBuilder->join('customers c', 'c.id = trips.customer_id', 'left');
        $historyBuilder->join('drivers d', 'd.id = trips.driver_id', 'left');
        $historyBuilder->where('trips.deleted_at', null);
        $historyBuilder->whereIn('trips.status', ['completed', 'cancelled']);
        $applyFilters($historyBuilder);
        $historyBuilder->orderBy('trips.created_at', 'DESC');
        $historyBuilder->limit($perPage, ($page - 1) * $perPage);
        $history = $historyBuilder->get()->getResult();

        // ── Combine for "all" tab (queue + active + current history page)
        $allTrips = array_merge($queue, $active, $history);

        // ── Stats (efficient COUNT queries) ─────────────────────────
        $statsRow = $db->query(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('active','dispatching') THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status IN ('completed','cancelled') THEN 1 ELSE 0 END) as history_count,
                COALESCE(SUM(fare_amount), 0) as revenue
             FROM trips WHERE deleted_at IS NULL"
        )->getRow();

        // ── Fetch disputes (only for trip IDs we have) ──────────────
        $tripIds = array_column($allTrips, 'id');
        $disputesByTripId = [];
        $disputesById = [];
        if (!empty($tripIds)) {
            $disputeModel = new \Modules\Dispatch\Models\DisputeModel();
            $disputes = $disputeModel->whereIn('trip_id', $tripIds)->findAll();
            foreach ($disputes as $d) {
                $disputesById[$d->id] = $d;
                if ($d->trip_id) {
                    $disputesByTripId[$d->trip_id] = $d;
                }
            }
        }

        // Attach disputes to trips
        foreach ($allTrips as &$t) {
            $t->dispute = $disputesByTripId[$t->id] ?? null;
            $t->linked_dispute = ($t->linked_dispute_id && isset($disputesById[$t->linked_dispute_id]))
                ? $disputesById[$t->linked_dispute_id] : null;
        }
        // Also attach to bucket arrays (they share object references)
        foreach ($queue as &$t) {
            $t->dispute = $disputesByTripId[$t->id] ?? null;
            $t->linked_dispute = ($t->linked_dispute_id && isset($disputesById[$t->linked_dispute_id]))
                ? $disputesById[$t->linked_dispute_id] : null;
        }
        foreach ($active as &$t) {
            $t->dispute = $disputesByTripId[$t->id] ?? null;
            $t->linked_dispute = ($t->linked_dispute_id && isset($disputesById[$t->linked_dispute_id]))
                ? $disputesById[$t->linked_dispute_id] : null;
        }
        foreach ($history as &$t) {
            $t->dispute = $disputesByTripId[$t->id] ?? null;
            $t->linked_dispute = ($t->linked_dispute_id && isset($disputesById[$t->linked_dispute_id]))
                ? $disputesById[$t->linked_dispute_id] : null;
        }

        // Fetch drivers for sidebar/modal
        $driverModel = new \Modules\Fleet\Models\DriverModel();
        $availableDrivers = $driverModel->where('status', 'active')->findAll();

        // Fetch customers for Quick Dispatch Modal
        $customerModel = new \Modules\Customer\Models\CustomerModel();
        $allCustomers = $customerModel->where('deleted_at', null)->orderBy('first_name', 'ASC')->findAll();

        $data = [
            'trips_queue'   => $queue,
            'trips_active'  => $active,
            'trips_history' => $history,
            'trips_all'     => $allTrips,
            'drivers'       => $availableDrivers,
            'customers'     => $allCustomers,
            'active_tab'    => 'all',
            'filters' => [
                'search'    => $search,
                'driver_id' => $driverId,
                'from_date' => $fromDate,
                'to_date'   => $toDate,
                'date'      => $date,
                'status'    => $status
            ],
            // Pagination
            'page'          => $page,
            'per_page'      => $perPage,
            'total_history' => $totalHistory,
            'total_pages'   => max(1, ceil($totalHistory / $perPage)),
            // Stats
            'total_trips'   => (int) ($statsRow->total ?? 0),
            'in_progress'   => (int) ($statsRow->active_count ?? 0),
            'completed'     => (int) ($statsRow->history_count ?? 0),
            'revenue'       => (float) ($statsRow->revenue ?? 0),
            'title'         => 'Dispatch Board'
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

    /**
     * Print a standalone trip receipt.
     */
    public function printTrip(int $id)
    {
        $db = \Config\Database::connect();

        $trip = $db->table('trips')
            ->select('
                trips.*,
                c.first_name as c_first, c.last_name as c_last, c.phone as c_phone,
                d.first_name as d_first, d.last_name as d_last, d.phone as d_phone
            ')
            ->join('customers c', 'c.id = trips.customer_id', 'left')
            ->join('drivers d',   'd.id   = trips.driver_id',   'left')
            ->where('trips.id', $id)
            ->where('trips.deleted_at', null)
            ->get()->getRow();

        if (!$trip) {
            return redirect()->to('/dispatch/trips')->with('error', 'Trip not found.');
        }

        return view('Modules\Dispatch\Views\trips\print', ['trip' => $trip]);
    }

    /**
     * Bulk print trip receipts.
     */
    public function bulkPrint()
    {
        $ids = $this->request->getVar('ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No trips selected for printing.');
        }

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $db = \Config\Database::connect();
        $trips = $db->table('trips')
            ->select('
                trips.*,
                c.first_name as c_first, c.last_name as c_last, c.phone as c_phone,
                d.first_name as d_first, d.last_name as d_last, d.phone as d_phone
            ')
            ->join('customers c', 'c.id = trips.customer_id', 'left')
            ->join('drivers d',   'd.id   = trips.driver_id',   'left')
            ->whereIn('trips.id', $ids)
             ->where('trips.deleted_at', null)
            ->orderBy('trips.created_at', 'DESC')
            ->get()->getResult();

        if (empty($trips)) {
            return redirect()->back()->with('error', 'No valid trips found for printing.');
        }

        return view('Modules\Dispatch\Views\trips\bulk_print', ['trips' => $trips]);
    }

    /**
     * Print a formal trip statement (summary of selected trips).
     */
    public function tripStatement()
    {
        $ids = $this->request->getVar('ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No trips selected for the statement.');
        }

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $db = \Config\Database::connect();
        $trips = $db->table('trips')
            ->select('
                trips.*,
                c.first_name as c_first, c.last_name as c_last, c.phone as c_phone, c.email as c_email,
                d.first_name as d_first, d.last_name as d_last, d.phone as d_phone
            ')
            ->join('customers c', 'c.id = trips.customer_id', 'left')
            ->join('drivers d',   'd.id   = trips.driver_id',   'left')
            ->whereIn('trips.id', $ids)
            ->where('trips.deleted_at', null)
            ->orderBy('trips.created_at', 'ASC')
            ->get()->getResult();

        if (empty($trips)) {
            return redirect()->back()->with('error', 'No valid trips found.');
        }

        // We assume all selected trips belong to the same customer for a "Statement"
        // If not, we still show the first customer's info in header for simplicity in this context
        $customer = (object)[
            'id' => $trips[0]->customer_id,
            'first_name' => $trips[0]->c_first,
            'last_name' => $trips[0]->c_last,
            'phone' => $trips[0]->c_phone,
            'email' => $trips[0]->c_email
        ];

        return view('Modules\Dispatch\Views\trips\statement', [
            'trips' => $trips,
            'customer' => $customer
        ]);
    }
}
