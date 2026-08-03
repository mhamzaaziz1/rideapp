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

        // Let TripManipulationService handle all flows and state machines (Booking & Driver)
        $tripResponse = $this->tripService->process($userId, $userType, $userMessage, $conversationId);
        if ($tripResponse) {
            return $tripResponse;
        }
        
        // Search in FAQ
        $faq = $this->faqModel->findAnswer($userMessage);
        if ($faq) {
            return $faq['answer'];
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

        // Try AI response if configured
        $aiResponse = $this->aiManager->ask($userMessage, $history);
        
        if ($aiResponse) {
            return $aiResponse;
        }

        // Default fallback
        return "I'm not sure I understand. Would you like to speak with a human agent? Type 'agent' to be connected.";
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
}
