<?php

namespace Modules\Dispatch\Services;

class NotificationService
{
    /**
     * Send Notification (Mock)
     * 
     * @param int $userId
     * @param string $message
     * @param string $channel 'sms'|'email'|'push'
     */
    public function send($userId, $message, $channel = 'push')
    {
        // In a real app, this would integrate with Twilio, SendGrid, or Firebase
        
        $logPath = WRITEPATH . 'logs/notifications.log';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] [User: $userId] [$channel] $message" . PHP_EOL;
        
        file_put_contents($logPath, $entry, FILE_APPEND);
        
        return true;
    }

    public function notifyDriverAssigned($driverId, $tripNumber)
    {
        $this->send($driverId, "New Trip Assigned: #$tripNumber. Check your app.", 'sms');
    }

    public function notifyCustomerDriverArrived($customerId, $driverName)
    {
        $this->send($customerId, "Your driver $driverName has arrived!", 'push');
    }
}
