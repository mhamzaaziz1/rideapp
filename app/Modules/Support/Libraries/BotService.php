<?php

namespace App\Modules\Support\Libraries;

use App\Modules\Support\Models\FaqModel;

class BotService
{
    protected $faqModel;
    protected $tripService;
    protected $aiManager;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
        $this->tripService = new TripManipulationService();
        $this->aiManager = new AiManager();
    }

    public function getResponse($userMessage, $userId = null, $userType = null, $conversationId = null)
    {
        $userMessage = strtolower(trim($userMessage));

        // Check for Trip Manipulation Commands first
        if ($userId && $userType) {
            $tripResponse = $this->tripService->process($userId, $userType, $userMessage);
            if ($tripResponse) {
                return $tripResponse;
            }
        }
        
        // Basic greetings
        $greetings = ['hi', 'hello', 'hey', 'start'];
        foreach ($greetings as $greet) {
            if (str_contains($userMessage, $greet)) {
                return "Hello! I'm your RideApp assistant. How can I help you today? You can ask about 'refund', 'booking', or 'password'.";
            }
        }

        // Search in FAQ
        $faq = $this->faqModel->findAnswer($userMessage);
        if ($faq) {
            return $faq['answer'];
        }

        // Manual Booking Flow
        $bookingResponse = $this->handleManualBookingFlow($userMessage, $conversationId);
        if ($bookingResponse) {
            return $bookingResponse;
        }

        // Check for specific data queries
        if ($userId && $userType) {
            if (str_contains($userMessage, 'balance') || str_contains($userMessage, 'earnings')) {
                return $this->getBalance($userId, $userType);
            }
            if (str_contains($userMessage, 'last trip') || str_contains($userMessage, 'trip status') || str_contains($userMessage, 'status of my ride')) {
                return $this->getRecentTrip($userId, $userType);
            }
        }
        
        if (str_contains($userMessage, 'cancel my ride')) {
            return "To cancel an active ride, please navigate to your 'My Trips' section in the app and select 'Cancel', or type 'agent' and a human representative will cancel it for you.";
        }

        // Fetch conversation history for AI Context
        $history = [];
        if ($conversationId) {
            $db = \Config\Database::connect();
            $messages = $db->table('chat_messages')
                ->where('conversation_id', $conversationId)
                ->orderBy('created_at', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
            $history = $messages;
        }

        // Inject Automated Booking Prompt
        $bookingInstructions = "\n\n[SYSTEM INSTRUCTION: If the user explicitly asks to book a trip or ride, and provides BOTH a pickup and dropoff location, you MUST reply EXACTLY with this JSON format and absolutely nothing else: [BOOK_TRIP] {\"pickup\": \"their pickup\", \"dropoff\": \"their dropoff\"}. If they want to book but are missing either location, politely ask them for the missing details.]";

        // Try AI response if configured
        $aiResponse = $this->aiManager->ask($userMessage . $bookingInstructions, $history);
        
        if ($aiResponse) {
            if (str_contains($aiResponse, '[BOOK_TRIP]')) {
                return $this->handleAutomatedBooking($aiResponse, $userId, $userType, $conversationId);
            }
            return $aiResponse;
        }

        // Default fallback
        return "I'm not sure I understand. Would you like to speak with a human agent? Type 'agent' to be connected.";
    }

    protected function handleAutomatedBooking($aiResponse, $userId, $userType, $conversationId)
    {
        preg_match('/\[BOOK_TRIP\]\s*(\{.*?\})/s', $aiResponse, $matches);
        if (!isset($matches[1])) {
            return "I couldn't fully process your booking details. Please provide your pickup and dropoff again, or type 'agent' for human assistance.";
        }

        $data = json_decode($matches[1], true);
        if (!$data || empty($data['pickup']) || empty($data['dropoff'])) {
            return "I need both a pickup and dropoff location to book your trip. Where are you heading to and from?";
        }

        $db = \Config\Database::connect();
        $tripNumber = 'TRP-' . strtoupper(substr(uniqid(), -6));
        
        $tripData = [
            'trip_number' => $tripNumber,
            'customer_id' => ($userType === 'customer' && $userId) ? $userId : null,
            'status' => 'pending',
            'pickup_address' => $data['pickup'],
            'dropoff_address' => $data['dropoff'],
            'fare_amount' => rand(15, 50), // Simulated fare
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Attach guest info if available
        if (!$tripData['customer_id'] && $conversationId) {
            $conv = $db->table('chat_conversations')->where('id', $conversationId)->get()->getRow();
            if ($conv && $conv->guest_name) {
                $tripData['notes'] = "Guest Booking: {$conv->guest_name} ({$conv->guest_phone})";
            } else {
                $tripData['notes'] = "Guest Booking from Chat";
            }
        }

        $db->table('trips')->insert($tripData);

        return "🎉 Great news! Your trip has been successfully booked.\n\n📍 Pickup: {$data['pickup']}\n🏁 Dropoff: {$data['dropoff']}\n🚕 Trip ID: {$tripNumber}\n\nWe are dispatching a driver to your location. You will be notified shortly.";
    }

    protected function getBalance($userId, $userType)
    {
        $db = \Config\Database::connect();
        $table = ($userType === 'driver') ? 'drivers' : 'customers';
        $user = $db->table($table)->where('id', $userId)->get()->getRow();

        if (!$user) return "I couldn't find your account details.";

        $balance = number_format((float)($user->balance ?? 0), 2);
        return "💰 Your current balance is \${$balance}.";
    }

    protected function getRecentTrip($userId, $userType)
    {
        $db = \Config\Database::connect();
        $trip = $db->table('trips')
            ->where($userType . '_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        if (!$trip) return "I couldn't find any recent trips for you.";

        $status = ucfirst($trip->status);
        $fare = number_format((float)($trip->fare_amount ?? 0), 2);
        return "🚗 Your last trip (#{$trip->trip_number}) was to {$trip->dropoff_address}. \nStatus: {$status}\nFare: \${$fare}";
    }

    protected function handleManualBookingFlow($userMessage, $conversationId)
    {
        $session = session();
        $stateKey = "booking_state_{$conversationId}";
        $pickupKey = "booking_pickup_{$conversationId}";
        $dropoffKey = "booking_dropoff_{$conversationId}";

        $state = $session->get($stateKey);

        // Initiate Booking
        if (str_contains($userMessage, 'book a ride') || str_contains($userMessage, 'book trip')) {
            $session->set($stateKey, 'awaiting_pickup');
            return "Sure! I can help you book a ride. Where would you like to be picked up from?";
        }

        if ($state === 'awaiting_pickup') {
            $session->set($pickupKey, $userMessage);
            $session->set($stateKey, 'awaiting_dropoff');
            return "Got it. And where are you heading to?";
        }

        if ($state === 'awaiting_dropoff') {
            $session->set($dropoffKey, $userMessage);
            $session->set($stateKey, 'awaiting_confirmation');
            $fare = rand(15, 50);
            $session->set("booking_fare_{$conversationId}", $fare);
            
            $pickup = $session->get($pickupKey);
            return "Awesome! Let me review your details:\n\n📍 Pickup: {$pickup}\n🏁 Dropoff: {$userMessage}\n💰 Estimated Fare: \${$fare}\n\nShall I go ahead and book this ride for you? (Yes/No)";
        }

        if ($state === 'awaiting_confirmation') {
            if (in_array(strtolower(trim($userMessage)), ['yes', 'y', 'sure', 'ok', 'okay', 'book it'])) {
                $pickup = $session->get($pickupKey);
                $dropoff = $session->get($dropoffKey);
                $fare = $session->get("booking_fare_{$conversationId}");

                // Create a simulated AI response to reuse the handleAutomatedBooking method logic
                $jsonPayload = json_encode(['pickup' => $pickup, 'dropoff' => $dropoff]);
                $aiFormat = "[BOOK_TRIP] {$jsonPayload}";

                // Clear session state
                $session->remove([$stateKey, $pickupKey, $dropoffKey, "booking_fare_{$conversationId}"]);

                $userId = session()->get('user_id') ?? null;
                $userType = session()->get('user_type') ?? 'customer';

                return $this->handleAutomatedBooking($aiFormat, $userId, $userType, $conversationId);
            } else if (in_array(strtolower(trim($userMessage)), ['no', 'n', 'cancel', 'stop'])) {
                $session->remove([$stateKey, $pickupKey, $dropoffKey, "booking_fare_{$conversationId}"]);
                return "No problem! Your booking has been canceled. Let me know if you need anything else.";
            } else {
                return "Please reply with 'Yes' to confirm the booking or 'No' to cancel.";
            }
        }

        return null;
    }
}
