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
     */
    public function process($userId, $userType, $message, $conversationId = null)
    {
        $command = trim($message);

        if ($userType === 'customer') {
            return $this->processCustomerCommand($userId, $command, $conversationId);
        } elseif ($userType === 'driver') {
            return $this->processDriverCommand($userId, $command, $conversationId);
        }

        return null;
    }

    protected function getSessionIdentifier($userId, $conversationId)
    {
        if ($userId) {
            $customer = $this->db->table('customers')->where('id', $userId)->get()->getRow();
            if ($customer && $customer->phone) {
                return $customer->phone;
            }
            return "user_{$userId}";
        } elseif ($conversationId) {
            return "conv_{$conversationId}";
        }
        return "guest_" . uniqid();
    }

    protected function processCustomerCommand($userId, $message, $conversationId)
    {
        $identifier = $this->getSessionIdentifier($userId, $conversationId);
        $command = strtoupper(trim($message));

        $session = $this->db->table('sms_onboarding_sessions')
            ->where('phone_number', $identifier)
            ->get()
            ->getRow();

        if ($session) {
            return $this->handleCustomerBookingSession($userId, $session, $message, $identifier);
        }

        // Check for Saved Locations Fast Booking
        if ($userId) {
            $savedLocation = $this->db->table('customer_addresses')
                ->where('customer_id', $userId)
                ->where('type', ucfirst(strtolower($message)))
                ->get()
                ->getRow();

            if ($savedLocation) {
                $this->db->table('sms_onboarding_sessions')->insert([
                    'phone_number' => $identifier,
                    'state'        => 'fast_booking_confirm',
                    'data'         => json_encode(['dropoff' => $savedLocation->address, 'pickup' => 'Current Location']),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s')
                ]);
                $price = number_format(rand(1500, 4000) / 100, 2);
                return "Did you mean your saved location?\n🏢 {$savedLocation->type}\n{$savedLocation->address}\nEstimated Price: \${$price}\nReply YES to book or NO to cancel.";
            }
        }

        if ($command === 'NO') {
            return "Okay, let us know if you need a ride later!";
        }
        
        if ($command === 'HELP') {
            return "👋 Welcome to RideApp!\nReply 'Hi' to start a booking, or text a saved location like 'Home' or 'Work'.";
        }

        $bookingKeywords = ['HI', 'HELLO', 'HEY', 'START', 'TRIP', 'RIDE', 'BOOK'];
        
        if (in_array($command, $bookingKeywords)) {
            // Start new booking flow
            $this->db->table('sms_onboarding_sessions')->insert([
                'phone_number' => $identifier,
                'state'        => 'booking_init',
                'data'         => json_encode([]),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]);

            return "Hi! This is CarLine 🚕\nYou need a trip?\nReply YES to start or NO to stop.";
        }

        return null; // Let other handlers (like AI/FAQ) handle it if it's an unrecognized command and no session exists
    }

    protected function handleCustomerBookingSession($userId, $session, $message, $identifier)
    {
        $state = $session->state;
        $data = json_decode($session->data, true) ?? [];
        $cleanMessage = trim($message);
        $upperCommand = strtoupper($cleanMessage);

        $affirmative = ['YES', 'YEP', 'YEAH', 'SURE', 'OK', 'OKAY', 'Y', 'BOOK IT', 'CONFIRM'];
        $negative = ['NO', 'NAH', 'NOPE', 'CANCEL', 'STOP', 'N'];

        if (in_array($upperCommand, $negative)) {
            $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
            return "No problem! Your ride request has been cancelled.";
        }

        if ($state === 'booking_init') {
            if (in_array($upperCommand, $affirmative)) {
                $this->updateSession($session->id, 'booking_ask_pickup', $data);
                return "Great! What is your pickup address?";
            }
            return "Reply YES to start or NO to stop.";
        }

        if ($state === 'booking_ask_pickup') {
            $addresses = $this->geocodeAddress($cleanMessage);
            
            if ($addresses === false) {
                return "API Problem: The Google Maps API key is not configured correctly or is restricted.";
            }

            if (empty($addresses)) {
                return "We couldn't find that address. Please try typing it again.";
            }

            if (count($addresses) > 1) {
                $data['pending_pickup_options'] = $addresses;
                $this->updateSession($session->id, 'booking_pick_pickup', $data);
                
                $msg = "We found multiple matches. Reply with a number:\n";
                foreach ($addresses as $i => $addr) {
                    $num = $i + 1;
                    $msg .= "{$num}. {$addr}\n";
                }
                $noneNum = count($addresses) + 1;
                $msg .= "{$noneNum}. None (type again)";
                return trim($msg);
            }

            $verifiedAddress = $addresses[0];
            $data['pending_pickup'] = $verifiedAddress;
            $this->updateSession($session->id, 'booking_confirm_pickup', $data);
            $mapLink = "https://maps.google.com/?q=" . urlencode($verifiedAddress);
            return "Did you mean:\n{$verifiedAddress}\n📍 Map: {$mapLink}\nReply YES to confirm or send a different address.";
        }

        if ($state === 'booking_pick_pickup') {
            $options = $data['pending_pickup_options'] ?? [];
            $choice = (int)$cleanMessage;
            
            if ($choice > 0 && $choice <= count($options)) {
                $data['pickup'] = $options[$choice - 1];
                unset($data['pending_pickup_options']);
                $this->updateSession($session->id, 'booking_ask_dropoff', $data);
                return "Got it. Pickup is: {$data['pickup']}.\nWhere do you want to go?\n(Drop-off address)";
            }
            
            unset($data['pending_pickup_options']);
            $this->updateSession($session->id, 'booking_ask_pickup', $data);
            return "Okay, let's try again. What is your correct pickup address?";
        }

        if ($state === 'booking_confirm_pickup') {
            if (in_array($upperCommand, $affirmative)) {
                $data['pickup'] = $data['pending_pickup'];
                unset($data['pending_pickup']);
                $this->updateSession($session->id, 'booking_ask_dropoff', $data);
                return "Where do you want to go?\n(Drop-off address)";
            } else {
                $this->updateSession($session->id, 'booking_ask_pickup', $data);
                return "Okay, what is your correct pickup address?";
            }
        }

        if ($state === 'booking_ask_dropoff') {
            $addresses = $this->geocodeAddress($cleanMessage);
            
            if ($addresses === false) {
                return "API Problem: The Google Maps API key is not configured correctly or is restricted.";
            }

            if (empty($addresses)) {
                return "We couldn't find that address. Please try typing it again.";
            }

            if (count($addresses) > 1) {
                $data['pending_dropoff_options'] = $addresses;
                $this->updateSession($session->id, 'booking_pick_dropoff', $data);
                
                $msg = "We found multiple matches. Reply with a number:\n";
                foreach ($addresses as $i => $addr) {
                    $num = $i + 1;
                    $msg .= "{$num}. {$addr}\n";
                }
                $noneNum = count($addresses) + 1;
                $msg .= "{$noneNum}. None (type again)";
                return trim($msg);
            }

            $verifiedAddress = $addresses[0];
            $data['pending_dropoff'] = $verifiedAddress;
            $this->updateSession($session->id, 'booking_confirm_dropoff', $data);
            $mapLink = "https://maps.google.com/?q=" . urlencode($verifiedAddress);
            return "Did you mean:\n{$verifiedAddress}\n📍 Map: {$mapLink}\nReply YES to confirm or send a different address.";
        }

        if ($state === 'booking_pick_dropoff') {
            $options = $data['pending_dropoff_options'] ?? [];
            $choice = (int)$cleanMessage;
            
            if ($choice > 0 && $choice <= count($options)) {
                $data['dropoff'] = $options[$choice - 1];
                unset($data['pending_dropoff_options']);
                $this->updateSession($session->id, 'booking_ask_vehicle', $data);
                return "Got it. Drop-off is: {$data['dropoff']}.\nWhat type of vehicle do you need?\nReply 1 for Standard (up to 5 pass.)\nReply 2 for Minivan (7-8 pass.)\nReply 3 for Don't care (Quick Book)";
            }
            
            unset($data['pending_dropoff_options']);
            $this->updateSession($session->id, 'booking_ask_dropoff', $data);
            return "Okay, let's try again. Where do you want to go?";
        }

        if ($state === 'booking_confirm_dropoff') {
            if (in_array($upperCommand, $affirmative)) {
                $data['dropoff'] = $data['pending_dropoff'];
                unset($data['pending_dropoff']);
                $this->updateSession($session->id, 'booking_ask_vehicle', $data);
                return "What type of vehicle do you need?\nReply 1 for Standard (up to 5 pass.)\nReply 2 for Minivan (7-8 pass.)\nReply 3 for Don't care (Quick Book)";
            } else {
                $this->updateSession($session->id, 'booking_ask_dropoff', $data);
                return "Okay, where do you want to go?";
            }
        }

        if ($state === 'booking_ask_vehicle') {
            if (in_array($cleanMessage, ['1', '2', '3'])) {
                $types = ['1' => 'Standard', '2' => 'Minivan', '3' => 'Quick Book (Any available car)'];
                $data['vehicle'] = $types[$cleanMessage];
                $this->updateSession($session->id, 'booking_ask_time', $data);
                return "When do you need the ride?\nReply 1 for ASAP (Quick Book 7-10 min)\nReply 2 for Later (choose time)";
            }
            return "Reply 1, 2, or 3 to select your vehicle type.";
        }

        if ($state === 'booking_ask_time') {
            if (in_array($cleanMessage, ['1', '2'])) {
                $time = ($cleanMessage === '1') ? '7 - 10 minutes' : 'Later';
                $data['eta'] = $time;
                $data['price'] = number_format(rand(2000, 6000) / 100, 2);
                $this->updateSession($session->id, 'booking_confirm_trip', $data);
                
                return "Trip Summary\nPickup: {$data['pickup']}\nDestination: {$data['dropoff']}\nVehicle: {$data['vehicle']}\nETA for driver: {$data['eta']}\nEstimated Price: \${$data['price']}\n\nReply YES to book or NO to cancel.";
            }
            return "Reply 1 for ASAP or 2 for Later.";
        }

        if ($state === 'booking_confirm_trip' || $state === 'fast_booking_confirm') {
            if (in_array($upperCommand, $affirmative)) {
                // Book the trip
                $tripNumber = 'TRP-' . strtoupper(substr(uniqid(), -6));
                $this->db->table('trips')->insert([
                    'customer_id'     => $userId ?: null,
                    'trip_number'     => $tripNumber,
                    'pickup_address'  => $data['pickup'],
                    'dropoff_address' => $data['dropoff'],
                    'status'          => 'pending',
                    'fare_amount'     => $data['price'] ?? 0,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                
                if ($userId && $state !== 'fast_booking_confirm') {
                    $this->updateSession($session->id, 'booking_suggest_save', $data);
                    return "Ride Booked!\nPlease wait, finding your driver...\n\nWe noticed you go to this address often. Would you like to save it as?\n1. Home\n2. Work\n3. Custom Name";
                }

                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                return "Ride Booked!\nPlease wait, finding your driver...\nSends to driver queue.";
            }
            return "Reply YES to book or NO to cancel.";
        }

        if ($state === 'booking_suggest_save') {
            if (in_array($cleanMessage, ['1', '2'])) {
                $type = ($cleanMessage === '1') ? 'Home' : 'Work';
                if ($userId) {
                    $this->db->table('customer_addresses')->insert([
                        'customer_id' => $userId,
                        'type' => $type,
                        'address' => $data['dropoff'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                return "Great! Saved as '{$type}'.\nNow you can text '{$type}' anytime to book a ride faster.";
            } elseif ($cleanMessage === '3') {
                $this->updateSession($session->id, 'booking_save_custom_name', $data);
                return "What name would you like to use for this location?";
            }
            $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
            return "Okay, skipping save location.";
        }

        if ($state === 'booking_save_custom_name') {
            if ($userId) {
                $this->db->table('customer_addresses')->insert([
                    'customer_id' => $userId,
                    'type' => $cleanMessage,
                    'address' => $data['dropoff'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
            return "Great! Saved as '{$cleanMessage}'.\nNow you can text '{$cleanMessage}' anytime to book a ride faster.";
        }

        // Failsafe: if state is unknown, delete session
        $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
        return $this->processCustomerCommand($userId, $message, $conversationId);
    }

    protected function processDriverCommand($userId, $command, $conversationId)
    {
        $identifier = $this->getSessionIdentifier($userId, $conversationId);
        $session = $this->db->table('sms_onboarding_sessions')
            ->where('phone_number', $identifier)
            ->get()
            ->getRow();

        if ($session && $session->state === 'driver_payment_method') {
            if (in_array($command, ['1', '2', '3'])) {
                $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
                $tripData = json_decode($session->data, true);
                $amount = number_format($tripData['fare_amount'] ?? 48.00, 2);
                
                $trip = $this->db->table('trips')->where('id', $tripData['trip_id'] ?? 0)->get()->getRow();
                if ($trip) {
                    $this->notifyCustomer($trip, "✅ Thank you for riding with us!\nPayment of \${$amount} was successful.\nView Receipt: rideapp.com/receipt/{$trip->id}");
                }
                
                return "Payment Successful!\nAmount: \${$amount}\nTrip is fully closed.";
            }
            return "Reply 1 for Card on File, 2 for Cash, 3 for Manual Credit Card";
        }

        $allowedCommands = ['1', 'ACCEPT', 'YES', 'DECLINE', 'STATUS', 'HELP', 'START', 'FINISH', 'END', '2'];

        $upperCommand = strtoupper($command);

        if (!in_array($upperCommand, $allowedCommands)) {
            return "📱 Unrecognized command.\n\nAvailable commands:\nACCEPT / 1 — Accept latest trip\nDECLINE — Decline latest trip\nSTART — Start current trip\nEND / FINISH / 2 — Complete current trip";
        }

        $driver = $this->db->table('drivers')
            ->where('id', $userId)
            ->get()
            ->getRow();

        if (!$driver) {
            return "⚠️ You are not registered as a driver.";
        }

        if (in_array($upperCommand, ['1', 'ACCEPT', 'YES'])) {
            return $this->handleAccept($driver);
        }

        return match ($upperCommand) {
            'DECLINE' => $this->handleDecline($driver),
            'STATUS'  => $this->handleStatus($driver),
            'START'   => $this->handleStart($driver),
            'FINISH', 'END', '2'     => $this->handleEnd($driver, $identifier),
            'HELP'    => "📱 Available commands:\nACCEPT — Accept latest trip\nSTART — Start current trip\nEND / FINISH — Complete current trip\n",
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
        
        $passengerName = 'John';
        $passengerPhone = '+1 555-0199';
        if ($trip->customer_id) {
            $customer = $this->db->table('customers')->where('id', $trip->customer_id)->get()->getRow();
            if ($customer) {
                $passengerName = $customer->first_name;
                $passengerPhone = $customer->phone;
            }
        }
        
        $this->notifyCustomer($trip, "🚗 Your driver is on the way!\nDriver: {$driver->first_name} {$driver->last_name}\nCar: Any available car\nETA: 7 - 10 minutes");
        
        return "Trip Confirmed!\nPassenger: {$passengerName}\nPhone: {$passengerPhone}\nNavigate to: {$trip->pickup_address}";
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

    protected function handleEnd($driver, $identifier)
    {
        $trip = $this->db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'on_trip')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) return "ℹ️ No ongoing trip to end.";

        $this->db->table('trips')->where('id', $trip->id)->update(['status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')]);
        
        $this->db->table('sms_onboarding_sessions')->insert([
            'phone_number' => $identifier,
            'state'        => 'driver_payment_method',
            'data'         => json_encode(['fare_amount' => $trip->fare_amount, 'trip_id' => $trip->id]),
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        return "Trip ended. How is the customer paying?\nReply 1 for Card on File\nReply 2 for Cash\nReply 3 for Manual Credit Card";
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
            // Fallback for local testing if no API key is set
            return [
                trim($query) . ", Option 1, NY 10950",
                trim($query) . ", Option 2, NY 10950",
                trim($query) . ", Option 3, NY 10950"
            ];
        }

        // Use Places Autocomplete API instead of Geocode API for better partial text suggestions
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=' . urlencode($query) . '&key=' . $apiKey;
        $response = @file_get_contents($url);
        
        if (!$response) return [$query];

        $data = json_decode($response, true);
        
        // Handle API key restriction errors gracefully
        if (isset($data['status']) && $data['status'] === 'REQUEST_DENIED') {
            log_message('error', 'Google Maps API Error: ' . ($data['error_message'] ?? 'Request denied'));
            return false;
        }

        if (empty($data['predictions'])) {
            return [];
        }

        $formattedAddresses = [];
        foreach ($data['predictions'] as $index => $prediction) {
            if ($index >= 3) break; // Limit to top 3
            $formattedAddresses[] = $prediction['description'];
        }

        return array_unique($formattedAddresses);
    }

    protected function notifyCustomer($trip, $messageText)
    {
        if (!$trip) return;

        // Try to find an open chat conversation for the customer
        $builder = $this->db->table('chat_conversations')
            ->where('status !=', 'closed')
            ->orderBy('id', 'DESC')
            ->limit(1);
            
        if ($trip->customer_id) {
            $builder->where('user_id', $trip->customer_id);
        } else {
            return; // Fallback: guest users cannot easily be notified via SMS if they disconnect from chat
        }

        $conversation = $builder->get()->getRow();

        if ($conversation) {
            $this->db->table('chat_messages')->insert([
                'conversation_id' => $conversation->id,
                'sender_id'       => 0,
                'sender_role'     => 'bot',
                'message'         => $messageText,
                'created_at'      => date('Y-m-d H:i:s')
            ]);
            // Bump conversation to top
            $this->db->table('chat_conversations')
                ->where('id', $conversation->id)
                ->update(['updated_at' => date('Y-m-d H:i:s')]);
        } else if ($trip->customer_id) {
            // Queue SMS if no active web chat is found
            $customer = $this->db->table('customers')->where('id', $trip->customer_id)->get()->getRow();
            if ($customer && $customer->phone) {
                $this->db->table('sms_messages')->insert([
                    'to_number'   => $customer->phone,
                    'from_number' => '+15550000',
                    'body'        => $messageText,
                    'status'      => 'queued',
                    'direction'   => 'outbound',
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
