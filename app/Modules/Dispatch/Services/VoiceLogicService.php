<?php

namespace Modules\Dispatch\Services;

use Modules\Fleet\Models\DriverModel;
use Modules\Customer\Models\CustomerModel;
use Modules\Dispatch\Models\TripModel;
use Twilio\TwiML\VoiceResponse;
use Config\Twilio;

class VoiceLogicService
{
    protected $driverModel;
    protected $customerModel;
    protected $tripModel;
    protected $twilioNumber;

    public function __construct()
    {
        $this->driverModel = new DriverModel();
        $this->customerModel = new CustomerModel();
        $this->tripModel = new TripModel();
        
        $config = new Twilio();
        $this->twilioNumber = $config->number;
    }

    /**
     * Parse inbound call and deliver the correct voice menu
     */
    public function processInboundCall($from)
    {
        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $from), -10);
        $response = new VoiceResponse();

        // Check if Caller is Driver
        $driver = $this->driverModel->like('phone', $cleanPhone, 'before')->first();
        if ($driver) {
            return $this->buildDriverMenu($driver, $response);
        }

        // Check if Caller is Customer
        $customer = $this->customerModel->like('phone', $cleanPhone, 'before')->first();
        if ($customer) {
            return $this->buildCustomerMenu($customer, $response);
        }

        // Unknown Caller
        $response->say("Welcome to RideApp! Looks like your number isn't registered yet. Head to our website to sign up.");
        return $response->asXML();
    }

    /**
     * Builds the interactive Voice Menu for a Driver
     */
    private function buildDriverMenu($driver, VoiceResponse $response)
    {
        $activeTrip = $this->tripModel
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->first();

        if (!$activeTrip) {
            $response->say("Hi {$driver->first_name}. You have no active trips right now. We will text you when one is available. Goodbye.");
            return $response->asXML();
        }

        $gather = $response->gather([
            'numDigits' => 1,
            'action' => base_url('api/webhooks/twilio/voice/gather-driver'),
            'method' => 'POST'
        ]);

        $menuText = "Welcome back, {$driver->first_name}. ";
        
        if ($activeTrip->status === 'accepted') {
            $menuText .= "Press 1 to announce you have arrived. ";
        } elseif ($activeTrip->status === 'arrived') {
            $menuText .= "Press 2 to start the trip. ";
        } elseif ($activeTrip->status === 'started') {
            $menuText .= "Press 3 to complete the trip. ";
        }

        $menuText .= "Press 0 to call your customer directly.";
        
        $gather->say($menuText);
        // Fallback if they don't press anything
        $response->say("We didn't receive any input. Goodbye.");
        
        return $response->asXML();
    }

    /**
     * Builds the interactive Voice Menu for a Customer
     */
    private function buildCustomerMenu($customer, VoiceResponse $response)
    {
        $activeTrip = $this->tripModel
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->first();

        if (!$activeTrip) {
            $response->say("Hello {$customer->first_name}. You do not have an active ride. Text an address to this number to book one. Goodbye.");
            return $response->asXML();
        }

        $gather = $response->gather([
            'numDigits' => 1,
            'action' => base_url('api/webhooks/twilio/voice/gather-customer'),
            'method' => 'POST'
        ]);

        $menuText = "Hello {$customer->first_name}. Your ride is currently {$activeTrip->status}. ";
        $menuText .= "Press 1 to cancel this request. ";
        
        if ($activeTrip->driver_id) {
             $menuText .= "Press 0 to call your driver directly.";
        }

        $gather->say($menuText);
        $response->say("We didn't receive any input. Goodbye.");

        return $response->asXML();
    }

    /**
     * Process Keypad inputs from Drivers
     */
    public function processDriverInput($from, $digits)
    {
        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $from), -10);
        $driver = $this->driverModel->like('phone', $cleanPhone, 'before')->first();
        $response = new VoiceResponse();

        if (!$driver) {
             $response->say("Error detecting caller.");
             return $response->asXML();
        }

        $activeTrip = $this->tripModel
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->first();

        if (!$activeTrip) {
             $response->say("You do not have a valid active trip to perform this action.");
             return $response->asXML();
        }

        // Perform Action based on keypress
        switch ($digits) {
            case '1':
                if ($activeTrip->status === 'accepted') {
                    $this->tripModel->update($activeTrip->id, ['status' => 'arrived']);
                    $response->say("Status updated to arrived.");
                    $this->notifyCustomerViaSms($activeTrip->customer_id, "Your driver has arrived outside.");
                } else {
                     $response->say("Invalid command for current trip state.");
                }
                break;
            case '2':
                if ($activeTrip->status === 'arrived') {
                    $this->tripModel->update($activeTrip->id, ['status' => 'started', 'started_at' => date('Y-m-d H:i:s')]);
                    $response->say("Status updated to started. Have a safe trip.");
                } else {
                     $response->say("Invalid command for current trip state.");
                }
                break;
            case '3':
                if ($activeTrip->status === 'started') {
                    $this->tripModel->update($activeTrip->id, ['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s')]);
                    $response->say("Trip completed successfully. Goodbye.");
                } else {
                    $response->say("Invalid command for current trip state.");
                }
                break;
            case '0':
                // Proxy Call to Customer
                $customer = $this->customerModel->find($activeTrip->customer_id);
                if ($customer) {
                    $response->say("Connecting you to the customer now. Please hold.");
                    $dial = $response->dial('', ['callerId' => $this->twilioNumber]);
                    $dial->number($customer->phone);
                } else {
                    $response->say("Could not find the customer's phone number.");
                }
                return $response->asXML(); // Return early so we don't say "Goodbye"
            default:
                $response->say("Invalid selection.");
        }

        $response->say("Goodbye.");
        return $response->asXML();
    }

    /**
     * Process Keypad inputs from Customers
     */
    public function processCustomerInput($from, $digits)
    {
        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $from), -10);
        $customer = $this->customerModel->like('phone', $cleanPhone, 'before')->first();
        $response = new VoiceResponse();

        if (!$customer) {
             $response->say("Error detecting caller.");
             return $response->asXML();
        }

        $activeTrip = $this->tripModel
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->first();

        if (!$activeTrip) {
             $response->say("You do not have a valid active trip.");
             return $response->asXML();
        }

        switch ($digits) {
            case '1':
                // Cancel Trip
                $this->tripModel->update($activeTrip->id, ['status' => 'cancelled']);
                if ($activeTrip->driver_id) {
                    $this->notifyDriverViaSms($activeTrip->driver_id, "Alert: Trip {$activeTrip->trip_number} was cancelled by the customer.");
                }
                $response->say("Your trip has been cancelled. Goodbye.");
                break;
            case '0':
                // Proxy Call to Driver
                if ($activeTrip->driver_id) {
                     $driver = $this->driverModel->find($activeTrip->driver_id);
                     if ($driver) {
                         $response->say("Connecting you to your driver now. Please hold.");
                         $dial = $response->dial('', ['callerId' => $this->twilioNumber]);
                         $dial->number($driver->phone);
                         return $response->asXML(); // Return early
                     }
                }
                $response->say("No driver is assigned yet to proxy connect.");
                break;
            default:
                $response->say("Invalid selection.");
        }

        return $response->asXML();
    }

    /**
     * Helper: Notify via SMS when voice state changes happen
     */
    private function notifyCustomerViaSms($customerId, $message)
    {
        $customer = $this->customerModel->find($customerId);
        if ($customer) {
            $twilioService = new TwilioService();
            $twilioService->sendSms($customer->phone, $message);
        }
    }

    private function notifyDriverViaSms($driverId, $message)
    {
        $driver = $this->driverModel->find($driverId);
        if ($driver) {
            $twilioService = new TwilioService();
            $twilioService->sendSms($driver->phone, $message);
        }
    }
}
