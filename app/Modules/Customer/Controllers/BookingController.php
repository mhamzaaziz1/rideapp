<?php

namespace Modules\Customer\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\TripModel;
use Modules\Dispatch\Entities\Trip;
use Modules\Pricing\Services\PricingService;

class BookingController extends BaseController
{
    protected $tripModel;
    protected $pricingService;

    public function __construct()
    {
        $this->tripModel = new TripModel();
        $this->pricingService = new PricingService();
    }

    /**
     * Show Booking Form
     */
    public function new()
    {
        // In a real app, you'd get the logged-in customer ID.
        // For this demo, we might need to mock it or ask the user to select logic.
        // Assuming current user context or a generic booking page.
        
        $data = [
            'title' => 'Book a Ride',
            // Default vehicle types
            'vehicle_types' => [
                'standard' => 'Standard (Sedan)',
                'suv'      => 'SUV',
                'luxury'   => 'Luxury',
                'van'      => 'Van'
            ]
        ];
        return view('Modules\Customer\Views\booking\new', $data);
    }

    /**
     * AJAX: Get Fare Estimate
     */
    public function estimate()
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Invalid JSON data'])->setStatusCode(400);
        }

        if (!$json) {
            // Fallback to POST if JSON is empty (for form submissions)
             $json = $this->request->getPost();
        }
        
        // Mock coordinates if addresses provided but no coords (for demo)
        // In prod, frontend sends coords from Google Autocomplete
        $pickupLat = $json['pickup_lat'] ?? 40.7128;
        $pickupLng = $json['pickup_lng'] ?? -74.0060;
        $dropoffLat = $json['dropoff_lat'] ?? 40.6413;
        $dropoffLng = $json['dropoff_lng'] ?? -73.7781;
        
        $dist = $this->pricingService->calculateDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
        $duration = $this->pricingService->estimateDuration($dist);
        
        // Calculate for ALL types
        $fares = [];
        $vehicleTypes = ['standard', 'suv', 'luxury', 'van'];
        
        foreach($vehicleTypes as $type) {
            $cost = $this->pricingService->calculateFare($dist, $duration, $type);
            $fares[$type] = '$' . number_format($cost, 2);
        }

        return $this->response->setJSON([
            'distance_miles' => $dist,
            'duration_minutes' => $duration,
            'fares' => $fares,
            'base_fare' => $fares['standard'] // Fallback
        ]);
    }

    /**
     * Submit Booking
     */
    public function create()
    {
        $data = $this->request->getPost();
        
        // Validation
        if (!$this->validate([
            'pickup_address' => 'required',
            'dropoff_address' => 'required',
            'pickup_lat' => 'required', // Hidden field
            'pickup_lng' => 'required',
            'dropoff_lat' => 'required',
            'dropoff_lng' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create Trip
        $trip = new Trip($data);
        $trip->generateTripNumber();
        $trip->status = 'pending';
        // Use a default customer for now if not logged in
        $trip->customer_id = 1; // HARDCODED for Demo
        
        // Re-calc fare (don't trust frontend)
        $dist = $this->pricingService->calculateDistance($trip->pickup_lat, $trip->pickup_lng, $trip->dropoff_lat, $trip->dropoff_lng);
        $duration = $this->pricingService->estimateDuration($dist);
        $fare = $this->pricingService->calculateFare($dist, $duration, $data['vehicle_type'] ?? 'standard');
        
        $trip->distance_miles = $dist;
        $trip->duration_minutes = $duration;
        $trip->fare_amount = $fare;
        
        if ($this->tripModel->save($trip)) {
            return redirect()->to('/customer/trips')->with('success', 'Ride booked! Your driver assumes shortly.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to book ride.');
    }

    public function history()
    {
        // Show trips for hardcoded customer 1
        $trips = $this->tripModel->where('customer_id', 1)->orderBy('created_at', 'DESC')->findAll();
        
        return view('Modules\Customer\Views\booking\history', [
            'trips' => $trips,
            'title' => 'My Trips'
        ]);
    }
}
