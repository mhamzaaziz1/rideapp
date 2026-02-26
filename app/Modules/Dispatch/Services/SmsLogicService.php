<?php

namespace Modules\Dispatch\Services;

use Modules\Fleet\Models\DriverModel;
use Modules\Customer\Models\CustomerModel;
use Modules\Dispatch\Models\TripModel;

class SmsLogicService
{
    protected $driverModel;
    protected $customerModel;
    protected $tripModel;
    protected $twilioService;

    public function __construct()
    {
        $this->driverModel = new DriverModel();
        $this->customerModel = new CustomerModel();
        $this->tripModel = new TripModel();
        $this->twilioService = new TwilioService();
    }

    /**
     * Process incoming SMS hit from the webhook
     */
    public function processIncomingSms($from, $body)
    {
        // Clean phone number (e.g., extract last 10 digits for matching)
        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $from), -10);

        // 1. Identify Sender
        $driver = $this->driverModel->like('phone', $cleanPhone, 'before')->first();
        if ($driver) {
            return $this->handleDriverSms($driver, trim($body));
        }

        $customer = $this->customerModel->like('phone', $cleanPhone, 'before')->first();
        if ($customer) {
            return $this->handleCustomerSms($customer, trim($body));
        }

        return "Welcome to RideApp! Please register an account on our website before booking via text.";
    }

    /**
     * Handle logic if sender is a Driver
     */
    private function handleDriverSms($driver, $body)
    {
        $command = strtoupper($body);

        // Find active trip for driver
        $activeTrip = $this->tripModel
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->first();

        // Allow driver to ACCEPT pending trips if they have no active trip
        if ($command === 'ACCEPT' && !$activeTrip) {
            // Find an open pending trip
            $pendingTrip = $this->tripModel->where('status', 'pending')->orderBy('created_at', 'ASC')->first();
            if ($pendingTrip) {
                // Assign to this driver
                $this->tripModel->update($pendingTrip->id, ['driver_id' => $driver->id, 'status' => 'accepted']);
                
                // Notify Customer
                $customer = $this->customerModel->find($pendingTrip->customer_id);
                if ($customer) {
                    $this->twilioService->sendSms($customer->phone, "Good news! Driver {$driver->first_name} is on the way for your trip: {$pendingTrip->trip_number}");
                }
                
                return "Trip {$pendingTrip->trip_number} accepted. Reply ARRIVED when you reach the pickup location: {$pendingTrip->pickup_address}";
            } else {
                return "No pending trips available to accept right now.";
            }
        }

        if (!$activeTrip) {
            return "You currently have no active trips. Reply ACCEPT if you want to take the next available request.";
        }

        // Handle State Transitions
        if ($command === 'ARRIVED' && $activeTrip->status === 'accepted') {
            $this->tripModel->update($activeTrip->id, ['status' => 'arrived']);
            $customer = $this->customerModel->find($activeTrip->customer_id);
            if ($customer) $this->twilioService->sendSms($customer->phone, "Your driver has arrived outside!");
            return "Status updated to ARRIVED. Reply START when customer is in the car.";
        }
        
        if ($command === 'START' && $activeTrip->status === 'arrived') {
            $this->tripModel->update($activeTrip->id, ['status' => 'started', 'started_at' => date('Y-m-d H:i:s')]);
            $customer = $this->customerModel->find($activeTrip->customer_id);
            if ($customer) $this->twilioService->sendSms($customer->phone, "Your trip has started.");
            return "Trip started. Reply DONE when you reach the destination.";
        }
        
        if ($command === 'DONE' && $activeTrip->status === 'started') {
            $this->tripModel->update($activeTrip->id, ['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s')]);
            $customer = $this->customerModel->find($activeTrip->customer_id);
            if ($customer) $this->twilioService->sendSms($customer->phone, "Trip completed. Thank you for riding with us!");
            return "Trip completed successfully. Great job!";
        }

        // Proxy unrecognized message as a chat to the customer
        $customer = $this->customerModel->find($activeTrip->customer_id);
        if ($customer) {
            $this->twilioService->sendSms($customer->phone, "Driver: {$body}");
            return ""; // Empty reply to Twilio so it doesnt double text
        }

        return "Command not recognized. Valid commands: ACCEPT, ARRIVED, START, DONE.";
    }

    /**
     * Handle logic if sender is a Customer
     */
    private function handleCustomerSms($customer, $body)
    {
        $command = strtoupper($body);

        $activeTrip = $this->tripModel
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->first();

        // If they want to cancel
        if ($command === 'CANCEL' && $activeTrip) {
            $this->tripModel->update($activeTrip->id, ['status' => 'cancelled']);
            if ($activeTrip->driver_id) {
                $driver = $this->driverModel->find($activeTrip->driver_id);
                if ($driver) $this->twilioService->sendSms($driver->phone, "Alert: Trip {$activeTrip->trip_number} was cancelled by the customer.");
            }
            return "Your trip has been cancelled.";
        }

        // If no active trip, treat as New booking (Pickup Address)
        if (!$activeTrip) {
            $tripData = [
                'customer_id' => $customer->id,
                'status' => 'pending',
                'pickup_address' => $body,
                'dropoff_address' => 'TBD via Chat or App',
                'vehicle_type' => 'Standard',
            ];
            $trip = new \Modules\Dispatch\Entities\Trip($tripData);
            $trip->generateTripNumber();
            $this->tripModel->insert($trip);
            
            return "Booking requested from: {$body}. Looking for drivers... We will text you when a driver accepts.";
        }

        // If active trip has driver configured, Proxy message to Driver
        if ($activeTrip->driver_id) {
            $driver = $this->driverModel->find($activeTrip->driver_id);
            if ($driver) {
                $this->twilioService->sendSms($driver->phone, "Customer: {$body}");
                return ""; // Don't reply anything back via TwiML to keep it clean
            }
        }

        return "You have an active request. Please wait for a driver to accept.";
    }
}
