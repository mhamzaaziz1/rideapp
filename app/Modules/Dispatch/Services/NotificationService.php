<?php

namespace App\Modules\Dispatch\Services;

use App\Libraries\SmsService;

/**
 * Notification Service
 * 
 * Routes notifications to the correct channel (SMS, push, email)
 * and handles user phone lookup with proper type disambiguation.
 */
class NotificationService
{
    protected SmsService $smsService;

    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Send Notification via the appropriate channel
     * 
     * @param int    $userId   The user's ID
     * @param string $message  The notification message
     * @param string $channel  'sms'|'email'|'push'
     * @param string $userType 'driver'|'customer'|'auto' — specifies which table to look up
     * @return bool
     */
    public function send(int $userId, string $message, string $channel = 'push', string $userType = 'auto'): bool
    {
        // Always log the notification
        $logPath = WRITEPATH . 'logs/notifications.log';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] [$userType:$userId] [$channel] $message" . PHP_EOL;
        file_put_contents($logPath, $entry, FILE_APPEND);

        // Route to appropriate channel
        if ($channel === 'sms') {
            return $this->sendSms($userId, $message, $userType);
        }

        // push / email channels — extend as needed
        return true;
    }

    /**
     * Send SMS to a user by looking up their phone number
     *
     * @param int    $userId   The user's database ID
     * @param string $message  The message to send
     * @param string $userType 'driver'|'customer'|'auto'
     * @return bool
     */
    protected function sendSms(int $userId, string $message, string $userType = 'auto'): bool
    {
        $db = \Config\Database::connect();
        $phone = null;
        $resolvedType = $userType;

        // Look up the phone number based on the user type
        if ($userType === 'driver' || $userType === 'auto') {
            $driver = $db->table('drivers')->select('phone')->where('id', $userId)->get()->getRow();
            if ($driver && !empty($driver->phone)) {
                $phone = $driver->phone;
                $resolvedType = 'driver';
            }
        }

        if (empty($phone) && ($userType === 'customer' || $userType === 'auto')) {
            $customer = $db->table('customers')->select('phone')->where('id', $userId)->get()->getRow();
            if ($customer && !empty($customer->phone)) {
                $phone = $customer->phone;
                $resolvedType = 'customer';
            }
        }

        if (empty($phone)) {
            log_message('warning', "[NotificationService] No phone found for {$resolvedType} #{$userId}");
            return false;
        }

        // Check if SMS service is configured
        if (!$this->smsService->isConfigured()) {
            log_message('warning', "[NotificationService] SMS provider ({$this->smsService->getProvider()}) not configured. Message logged only.");
            return false;
        }

        $result = $this->smsService->send($phone, $message);
        
        // Store outbound message in DB
        $this->logOutboundSms($phone, $message, $userId, $result);

        return $result['success'];
    }

    /**
     * Send SMS directly to a phone number (bypass user lookup)
     */
    public function sendSmsToPhone(string $phone, string $message, ?int $relatedUserId = null): array
    {
        if (!$this->smsService->isConfigured()) {
            return ['success' => false, 'error' => 'SMS provider not configured.'];
        }

        $result = $this->smsService->send($phone, $message);
        $this->logOutboundSms($phone, $message, $relatedUserId, $result);

        return $result;
    }

    /**
     * Log an outbound SMS to the sms_messages table
     */
    protected function logOutboundSms(string $phone, string $message, ?int $userId, array $result): void
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('sms_messages')) {
                $db->table('sms_messages')->insert([
                    'provider'        => $this->smsService->getProvider(),
                    'direction'       => 'outbound',
                    'from_number'     => $this->smsService->getFromNumber(),
                    'to_number'       => $phone,
                    'body'            => $message,
                    'external_id'     => $result['message_id'] ?? null,
                    'status'          => $result['success'] ? 'sent' : 'failed',
                    'related_user_id' => $userId,
                    'error_message'   => $result['error'] ?? null,
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "[NotificationService] Failed to log SMS: " . $e->getMessage());
        }
    }

    // ─── Typed Convenience Methods ───────────────────────────────

    /**
     * Notify a driver about trip assignment
     */
    public function notifyDriverAssigned(int $driverId, string $tripNumber): bool
    {
        return $this->send($driverId, "🚗 New Trip Assigned: #{$tripNumber}. Open the app or reply ACCEPT to confirm.", 'sms', 'driver');
    }

    /**
     * Notify a customer that their driver has arrived
     */
    public function notifyCustomerDriverArrived(int $customerId, string $driverName): bool
    {
        return $this->send($customerId, "📍 Your driver {$driverName} has arrived at the pickup location!", 'sms', 'customer');
    }

    /**
     * Notify a customer that their trip is completed
     */
    public function notifyCustomerTripCompleted(int $customerId, string $tripNumber, float $fare): bool
    {
        $formattedFare = number_format($fare, 2);
        return $this->send($customerId, "✅ Trip #{$tripNumber} completed! Total: \${$formattedFare}. Thank you for riding with us!", 'sms', 'customer');
    }

    /**
     * Send an OTP verification code
     */
    public function sendOtp(string $phone, string $code): array
    {
        $message = "Your verification code is: {$code}. Do not share this with anyone. Expires in 5 minutes.";
        return $this->sendSmsToPhone($phone, $message);
    }

    /**
     * Notify a driver about trip cancellation
     */
    public function notifyDriverTripCancelled(int $driverId, string $tripNumber): bool
    {
        return $this->send($driverId, "❌ Trip #{$tripNumber} has been cancelled.", 'sms', 'driver');
    }
}
