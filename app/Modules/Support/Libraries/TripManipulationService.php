<?php

namespace App\Modules\Support\Libraries;

class TripManipulationService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Process a command from any channel (SMS or Chat)
     * 
     * @param int $userId The ID of the driver or customer
     * @param string $userType 'driver' or 'customer'
     * @param string $message The message body
     * @return string|null Reply message or null if not a command
     */
    public function process($userId, $userType, $message)
    {
        $command = strtoupper(trim($message));

        if ($userType === 'customer') {
            return $this->processCustomerCommand($userId, $message);
        } elseif ($userType === 'driver') {
            return $this->processDriverCommand($userId, $command);
        }

        return null;
    }

    // ─── CUSTOMER LOGIC (Numeric Menu) ───────────────────────────

    protected function processCustomerCommand($userId, $message)
    {
        $customer = $this->db->table('customers')->where('id', $userId)->get()->getRow();
        if (!$customer) {
            return "⚠️ Customer account not found.";
        }

        $command = strtoupper(trim($message));

        // 1. Check for an active session
        $session = $this->db->table('sms_onboarding_sessions')
            ->where('phone_number', $customer->phone)
            ->get()
            ->getRow();

        if ($session) {
            return $this->handleCustomerBookingSession($customer, $session, $message);
        }

        // If command is 'NO', just acknowledge and don't show menu
        if ($command === 'NO') {
            return "Okay, let us know if you need a ride later!";
        }

        return match ($command) {
            '1' => $this->customerRequestRide($customer),
            '2' => $this->customerStatus($customer),
            '3' => $this->customerCancel($customer),
            '4', 'HELP' => $this->getCustomerMenu(),
            default => $this->getCustomerMenu(),
        };
    }

    protected function getCustomerMenu()
    {
        return "👋 Welcome to RideApp!\nReply with a number:\n1️⃣ Request a Ride\n2️⃣ Check Trip Status\n3️⃣ Cancel Trip\n4️⃣ Help";
    }

    protected function handleCustomerBookingSession($customer, $session, $message)
    {
        $state = $session->state;
        $data = json_decode($session->data, true) ?? [];
        $cleanMessage = trim($message);
        
        // Handle cancellation of booking
        if (strtoupper($cleanMessage) === 'CANCEL' || strtoupper($cleanMessage) === 'REJECT') {
            if ($state === 'booking_confirm_price' && strtoupper($cleanMessage) === 'REJECT') {
                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                return "Ride request cancelled. Reply 1 if you change your mind!";
            } elseif (strtoupper($cleanMessage) === 'CANCEL') {
                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                return "Ride request cancelled.";
            }
        }

        if ($state === 'booking_ask_pickup') {
            $addresses = $this->geocodeAddress($cleanMessage);
            
            if (empty($addresses)) {
                return "We couldn't find that pickup address. Please try typing it again (e.g., '123 Main St, City').";
            }
            
            if (count($addresses) === 1) {
                $data['pickup'] = $addresses[0];
                $this->updateSession($session->id, 'booking_ask_dropoff', $data);
                return "Got it. Pickup is: {$data['pickup']}.\nWhere are you heading?";
            }
            
            $data['pending_pickup_options'] = $addresses;
            $this->updateSession($session->id, 'booking_confirm_pickup_address', $data);
            
            $msg = "We found multiple matches. Reply with a number:\n";
            foreach ($addresses as $i => $addr) {
                $num = $i + 1;
                $msg .= "{$num}️⃣ {$addr}\n";
            }
            $noneNum = count($addresses) + 1;
            $msg .= "{$noneNum}️⃣ None (type again)";
            return trim($msg);
        }

        if ($state === 'booking_confirm_pickup_address') {
            $options = $data['pending_pickup_options'] ?? [];
            $choice = (int)$cleanMessage;
            
            if ($choice > 0 && $choice <= count($options)) {
                $data['pickup'] = $options[$choice - 1];
                unset($data['pending_pickup_options']);
                $this->updateSession($session->id, 'booking_ask_dropoff', $data);
                return "Pickup confirmed: {$data['pickup']}.\nWhere are you heading?";
            }
            
            unset($data['pending_pickup_options']);
            $this->updateSession($session->id, 'booking_ask_pickup', $data);
            return "Let's try again. Where would you like to be picked up?";
        }

        if ($state === 'booking_ask_dropoff') {
            $addresses = $this->geocodeAddress($cleanMessage);
            
            if (empty($addresses)) {
                return "We couldn't find that dropoff address. Please try typing it again.";
            }
            
            if (count($addresses) === 1) {
                $data['dropoff'] = $addresses[0];
                $this->updateSession($session->id, 'booking_ask_time', $data);
                return "Got it. Dropoff is: {$data['dropoff']}.\nWhen would you like to be picked up? (e.g. 'Now' or 'In 15 mins')";
            }
            
            $data['pending_dropoff_options'] = $addresses;
            $this->updateSession($session->id, 'booking_confirm_dropoff_address', $data);
            
            $msg = "We found multiple matches for dropoff. Reply with a number:\n";
            foreach ($addresses as $i => $addr) {
                $num = $i + 1;
                $msg .= "{$num}️⃣ {$addr}\n";
            }
            $noneNum = count($addresses) + 1;
            $msg .= "{$noneNum}️⃣ None (type again)";
            return trim($msg);
        }

        if ($state === 'booking_confirm_dropoff_address') {
            $options = $data['pending_dropoff_options'] ?? [];
            $choice = (int)$cleanMessage;
            
            if ($choice > 0 && $choice <= count($options)) {
                $data['dropoff'] = $options[$choice - 1];
                unset($data['pending_dropoff_options']);
                $this->updateSession($session->id, 'booking_ask_time', $data);
                return "Dropoff confirmed: {$data['dropoff']}.\nWhen would you like to be picked up? (e.g. 'Now' or 'In 15 mins')";
            }
            
            unset($data['pending_dropoff_options']);
            $this->updateSession($session->id, 'booking_ask_dropoff', $data);
            return "Let's try again. Where are you heading?";
        }

        if ($state === 'booking_ask_time') {
            $data['pickup_time'] = $cleanMessage;
            $data['price'] = number_format(rand(1000, 5000) / 100, 2); // Random price
            $this->updateSession($session->id, 'booking_confirm_price', $data);
            return "Your trip from {$data['pickup']} to {$data['dropoff']} will cost \${$data['price']}. Reply ACCEPT to confirm or REJECT to cancel.";
        }

        if ($state === 'booking_confirm_price') {
            if (strtoupper($cleanMessage) === 'ACCEPT') {
                // Create trip
                $this->db->table('trips')->insert([
                    'customer_id'     => $customer->id,
                    'trip_number'     => 'TRP-' . strtoupper(substr(uniqid(), -6)),
                    'pickup_address'  => $data['pickup'],
                    'dropoff_address' => $data['dropoff'],
                    'status'          => 'pending',
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                return "✅ Ride confirmed! We are finding a driver. Reply 2 to check status.";
            }
            return "Please reply ACCEPT to confirm your ride or REJECT to cancel.";
        }

        // Failsafe: if state is unknown, delete session
        $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
        
        // Re-process command
        return $this->processCustomerCommand($customer->id, $message);
    }

    protected function updateSession($id, $state, $data)
    {
        $this->db->table('sms_onboarding_sessions')->where('id', $id)->update([
            'state' => $state,
            'data' => json_encode($data),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function geocodeAddress(string $query)
    {
        $settingsFile = WRITEPATH . 'settings.json';
        $apiKey = '';
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true);
            $apiKey = $settings['google_maps_api_key'] ?? '';
        }

        if (empty($apiKey)) {
            return [$query]; // Fallback if no API key is set
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . $apiKey;
        $response = @file_get_contents($url);
        
        if (!$response) return [$query];

        $data = json_decode($response, true);
        if (empty($data['results'])) {
            return [];
        }

        $formattedAddresses = [];
        foreach ($data['results'] as $index => $result) {
            if ($index >= 3) break; // Limit to top 3
            $formattedAddresses[] = $result['formatted_address'];
        }

        return array_unique($formattedAddresses);
    }

    protected function customerRequestRide($customer)
    {
        // Check if they already have an active/pending trip
        $existing = $this->db->table('trips')
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'dispatching', 'active', 'on_trip'])
            ->countAllResults();

        if ($existing > 0) {
            return "ℹ️ You already have an ongoing trip. Reply 2 to check status.";
        }

        // Create a booking session
        $this->db->table('sms_onboarding_sessions')->insert([
            'phone_number' => $customer->phone,
            'state'        => 'booking_ask_pickup',
            'data'         => json_encode([]),
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        return "Where would you like to be picked up?";
    }

    protected function customerStatus($customer)
    {
        $trip = $this->db->table('trips')
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'dispatching', 'active', 'on_trip'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ You have no active rides. Reply 1 to request a ride.";

        $status = ucfirst($trip->status);
        $driverInfo = "Searching for driver...";
        
        if ($trip->driver_id) {
            $driver = $this->db->table('drivers')->where('id', $trip->driver_id)->get()->getRow();
            if ($driver) {
                $driverInfo = "Driver: {$driver->first_name} {$driver->last_name}";
            }
        }

        return "📋 Trip #{$trip->trip_number}\nStatus: {$status}\n{$driverInfo}";
    }

    protected function customerCancel($customer)
    {
        $trip = $this->db->table('trips')
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'dispatching', 'active'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ You have no cancellable rides at the moment.";

        $this->db->table('trips')->where('id', $trip->id)->update(['status' => 'cancelled', 'updated_at' => date('Y-m-d H:i:s')]);
        return "✅ Trip #{$trip->trip_number} has been cancelled.";
    }


    // ─── DRIVER LOGIC ────────────────────────────────────────────

    protected function processDriverCommand($userId, $command)
    {
        $allowedCommands = ['ACCEPT', 'DECLINE', 'STATUS', 'HELP', 'START', 'END'];

        if (!in_array($command, $allowedCommands)) {
            // Show help menu for unrecognized commands
            return "📱 Unrecognized command.\n\nAvailable commands:\nACCEPT — Accept latest trip\nDECLINE — Decline latest trip\nSTART — Start current trip\nEND — Complete current trip\nSTATUS — View current trip info\nHELP — Show this help";
        }

        $driver = $this->db->table('drivers')
            ->where('id', $userId)
            ->where('status', 'active')
            ->get()
            ->getRow();

        if (!$driver) {
            return "⚠️ You are not registered as an active driver.";
        }

        return match ($command) {
            'ACCEPT'  => $this->handleAccept($driver),
            'DECLINE' => $this->handleDecline($driver),
            'STATUS'  => $this->handleStatus($driver),
            'START'   => $this->handleStart($driver),
            'END'     => $this->handleEnd($driver),
            'HELP'    => "📱 Available commands:\nACCEPT — Accept latest trip\nDECLINE — Decline latest trip\nSTART — Start current trip\nEND — Complete current trip\nSTATUS — View current trip info\nHELP — Show this help",
            default   => null,
        };
    }

    protected function handleAccept($driver)
    {
        $trip = $this->db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ No pending trips to accept.";

        $this->db->table('trips')->where('id', $trip->id)->update(['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')]);
        return "✅ Trip #{$trip->trip_number} accepted! Navigate to: {$trip->pickup_address}";
    }

    protected function handleDecline($driver)
    {
        $trip = $this->db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ No pending trips to decline.";

        $this->db->table('trips')->where('id', $trip->id)->update([
            'driver_id' => null,
            'status'    => 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return "❌ Trip #{$trip->trip_number} declined and returned to queue.";
    }

    protected function handleStatus($driver)
    {
        $trip = $this->db->table('trips')
            ->whereIn('status', ['active', 'dispatching', 'pending', 'on_trip'])
            ->where('driver_id', $driver->id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ No active trips.";

        $status = ucfirst($trip->status);
        return "📋 Trip #{$trip->trip_number}\nStatus: {$status}\nPickup: {$trip->pickup_address}\nDropoff: {$trip->dropoff_address}";
    }

    protected function handleStart($driver)
    {
        $trip = $this->db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ You must accept a trip before you can start it.";

        $this->db->table('trips')->where('id', $trip->id)->update(['status' => 'on_trip', 'updated_at' => date('Y-m-d H:i:s')]);
        return "🚀 Trip #{$trip->trip_number} started! Driving to destination.";
    }

    protected function handleEnd($driver)
    {
        $trip = $this->db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'on_trip')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ No ongoing trip to end.";

        $this->db->table('trips')->where('id', $trip->id)->update(['status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')]);
        return "🏁 Trip #{$trip->trip_number} completed! Thank you.";
    }
}
