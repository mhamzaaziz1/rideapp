<?php

namespace App\Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use App\Modules\Dispatch\Models\CommunicationLogModel;

class VoiceWebhookController extends BaseController
{
    /**
     * Webhook endpoint for Telnyx Voice Events
     */
    public function telnyx()
    {
        // 1. Get raw POST data
        $payload = $this->request->getBody();
        $data = json_decode($payload, true);

        // 2. Validate Telnyx signature if needed
        // For local testing or basic integration, we just log the event.
        
        if (!$data || !isset($data['data']['event_type'])) {
            // Check if it's a simple GET request for testing connectivity
            if (strtolower($this->request->getMethod()) === 'get') {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Telnyx Voice Webhook endpoint is active and listening.'
                ]);
            }
            // Return 200 OK even on invalid POST so Telnyx doesn't retry
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid payload format']);
        }

        $eventType = $data['data']['event_type'];
        $payloadData = $data['data']['payload'];

        // 3. Log the call if it's a new call initiated
        if ($eventType === 'call.initiated' && $payloadData['direction'] === 'inbound') {
            $from = $payloadData['from'] ?? 'Unknown';
            $to = $payloadData['to'] ?? 'Unknown';

            try {
                // Try to find if this matches a customer or driver
                $customerModel = new \App\Modules\Customer\Models\CustomerModel();
                $driverModel = new \App\Modules\Fleet\Models\DriverModel();

                $customer = $customerModel->where('phone', $from)->first();
                $driver = null;
                
                if (!$customer) {
                    $driver = $driverModel->where('phone', $from)->first();
                }

                $userId = null;
                $userType = 'unknown';

                if ($customer) {
                    $userId = $customer->id;
                    $userType = 'customer';
                } elseif ($driver) {
                    $userId = $driver->id;
                    $userType = 'driver';
                }

                $logModel = new CommunicationLogModel();
                $logModel->insert([
                    'trip_id' => null, // Associate later if needed
                    'user_id' => $userId,
                    'user_type' => $userType,
                    'type' => 'voice',
                    'direction' => 'inbound',
                    'from_number' => $from,
                    'to_number' => $to,
                    'content' => 'Inbound Voice Call Received via Telnyx',
                    'status' => 'initiated',
                    'sid' => $payloadData['call_control_id'] ?? null
                ]);

            } catch (\Exception $e) {
                log_message('error', '[TelnyxVoiceWebhook] DB Log Error: ' . $e->getMessage());
            }

            // Acknowledge the webhook
            return $this->response->setJSON(['success' => true, 'action' => 'logged']);
        }

        return $this->response->setJSON(['success' => true, 'action' => 'ignored']);
    }
}
