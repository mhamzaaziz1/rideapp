<?php

namespace App\Modules\Support\Libraries;

class SmsOnboardingService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Process an incoming message from an unknown number
     */
    public function process(string $phone, string $message): ?string
    {
        $session = $this->db->table('sms_onboarding_sessions')
            ->where('phone_number', $phone)
            ->get()
            ->getRow();

        if (!$session) {
            // New unknown number, start onboarding
            $this->db->table('sms_onboarding_sessions')->insert([
                'phone_number' => $phone,
                'state'        => 'awaiting_first_name',
                'data'         => json_encode([]),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
            
            return "👋 Welcome to RideApp! It looks like you're new here. To get started, what is your first name?";
        }

        $state = $session->state;
        $data = json_decode($session->data, true) ?? [];
        $cleanMessage = trim($message);

        if ($state === 'awaiting_first_name') {
            // Save first name, ask for last name
            $data['first_name'] = $cleanMessage;
            
            $this->db->table('sms_onboarding_sessions')
                ->where('id', $session->id)
                ->update([
                    'state'      => 'awaiting_last_name',
                    'data'       => json_encode($data),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
            return "Nice to meet you, {$data['first_name']}! What is your last name?";
        }

        if ($state === 'awaiting_last_name') {
            // Save last name and complete onboarding
            $data['last_name'] = $cleanMessage;
            
            // Create the customer
            $email = str_replace('+', '', $phone) . '@sms-user.local';
            
            $this->db->table('customers')->insert([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $phone,
                'email'      => $email,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $customerId = $this->db->insertID();
            
            // Delete the onboarding session
            $this->db->table('sms_onboarding_sessions')->where('id', $session->id)->delete();
            
            // Trigger "Request a Ride" automatically
            $tripService = new \App\Modules\Support\Libraries\TripManipulationService();
            $tripReply = $tripService->process($customerId, 'customer', '1');
            
            return "🎉 Account created successfully, {$data['first_name']}!\n\n" . $tripReply;
        }

        // Failsafe
        return null;
    }
}
