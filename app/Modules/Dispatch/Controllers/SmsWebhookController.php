<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;

/**
 * SMS Webhook Controller
 * 
 * Handles inbound SMS messages from both Twilio and Telnyx.
 * Configure your webhook URLs in the respective provider portals:
 *   - Twilio:  https://yourdomain.com/sms/webhook/twilio
 *   - Telnyx:  https://yourdomain.com/sms/webhook/telnyx
 * 
 * Supported inbound commands:
 *   ACCEPT  — Driver accepts their latest pending trip
 *   DECLINE — Driver declines their latest pending trip  
 *   STATUS  — Driver requests their current trip status
 *   HELP    — Returns a list of available commands
 */
class SmsWebhookController extends BaseController
{
    /**
     * Handle inbound SMS from Twilio
     * POST /sms/webhook/twilio
     */
    public function twilio()
    {
        $from   = $this->request->getPost('From') ?? '';
        $to     = $this->request->getPost('To') ?? '';
        $body   = $this->request->getPost('Body') ?? '';
        $msgSid = $this->request->getPost('MessageSid') ?? '';

        log_message('info', "[SMS:Twilio] Inbound from {$from}: " . substr($body, 0, 100));

        // Idempotency: Skip if we already processed this MessageSid
        if (!empty($msgSid) && $this->isDuplicate($msgSid)) {
            log_message('debug', "[SMS:Twilio] Duplicate webhook for SID {$msgSid}, skipping.");
            return $this->response
                ->setHeader('Content-Type', 'text/xml')
                ->setBody('<?xml version="1.0" encoding="UTF-8"?><Response></Response>');
        }

        // Store the inbound message
        $this->storeInboundMessage('twilio', $from, $to, $body, $msgSid);

        // Process auto-reply commands
        $reply = $this->processCommand($from, $body);

        // Build TwiML response (with optional reply)
        $twiml = '<?xml version="1.0" encoding="UTF-8"?><Response>';
        if (!empty($reply)) {
            $twiml .= '<Message>' . htmlspecialchars($reply) . '</Message>';
        }
        $twiml .= '</Response>';

        return $this->response
            ->setHeader('Content-Type', 'text/xml')
            ->setBody($twiml);
    }

    /**
     * Handle inbound SMS from Telnyx
     * POST /sms/webhook/telnyx
     */
    public function telnyx()
    {
        $rawPayload = $this->request->getBody();
        $payload = json_decode($rawPayload ?? '');

        if (!$payload || !isset($payload->data)) {
            log_message('warning', '[SMS:Telnyx] Received invalid webhook payload');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid payload']);
        }

        $eventType = $payload->data->event_type ?? '';

        // Only process inbound messages, acknowledge everything else
        if ($eventType !== 'message.received') {
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        $msgPayload = $payload->data->payload ?? null;
        if (!$msgPayload) {
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // Extract fields with null-safe navigation and fallbacks
        $from  = $this->extractTelnyxFrom($msgPayload);
        $to    = $this->extractTelnyxTo($msgPayload);
        $body  = $msgPayload->text ?? $msgPayload->body ?? '';
        $msgId = $payload->data->id ?? '';

        log_message('info', "[SMS:Telnyx] Inbound from {$from}: " . substr($body, 0, 100));

        // Idempotency: Skip if we already processed this event ID
        if (!empty($msgId) && $this->isDuplicate($msgId)) {
            log_message('debug', "[SMS:Telnyx] Duplicate webhook for ID {$msgId}, skipping.");
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // Store the inbound message
        $this->storeInboundMessage('telnyx', $from, $to, $body, $msgId);

        // Process auto-reply commands
        $reply = $this->processCommand($from, $body);

        // For Telnyx, send reply via API (not inline response)
        if (!empty($reply)) {
            try {
                $smsService = new \App\Libraries\SmsService();
                $smsService->send($from, $reply);
            } catch (\Exception $e) {
                log_message('error', "[SMS:Telnyx] Failed to send auto-reply: " . $e->getMessage());
            }
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Extract "from" phone number from Telnyx payload
     * Telnyx may send: { from: { phone_number: "..." } } or { from: "..." }
     */
    protected function extractTelnyxFrom(object $payload): string
    {
        if (isset($payload->from->phone_number)) {
            return $payload->from->phone_number;
        }
        if (isset($payload->from) && is_string($payload->from)) {
            return $payload->from;
        }
        return '';
    }

    /**
     * Extract "to" phone number from Telnyx payload
     * Telnyx may send: { to: [{ phone_number: "..." }] } or { to: "..." }
     */
    protected function extractTelnyxTo(object $payload): string
    {
        if (isset($payload->to) && is_array($payload->to) && !empty($payload->to)) {
            $first = $payload->to[0];
            if (is_object($first) && isset($first->phone_number)) {
                return $first->phone_number;
            }
            if (is_string($first)) {
                return $first;
            }
        }
        if (isset($payload->to) && is_string($payload->to)) {
            return $payload->to;
        }
        return '';
    }

    /**
     * Check if a message has already been processed (idempotency)
     */
    protected function isDuplicate(string $externalId): bool
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('sms_messages')) {
                $existing = $db->table('sms_messages')
                    ->where('external_id', $externalId)
                    ->where('direction', 'inbound')
                    ->countAllResults();
                return $existing > 0;
            }
        } catch (\Exception $e) {
            // If we can't check, assume not duplicate
        }
        return false;
    }

    /**
     * Store inbound SMS in the database
     */
    protected function storeInboundMessage(string $provider, string $from, string $to, string $body, string $externalId): void
    {
        try {
            $db = \Config\Database::connect();

            if ($db->tableExists('sms_messages')) {
                // Resolve related user: try matching phone to a driver or customer
                $relatedUserId = null;
                $driver = $db->table('drivers')->select('id')->where('phone', $from)->get()->getRow();
                if ($driver) {
                    $relatedUserId = $driver->id;
                } else {
                    $customer = $db->table('customers')->select('id')->where('phone', $from)->get()->getRow();
                    if ($customer) {
                        $relatedUserId = $customer->id;
                    }
                }

                $db->table('sms_messages')->insert([
                    'provider'        => $provider,
                    'direction'       => 'inbound',
                    'from_number'     => $from,
                    'to_number'       => $to,
                    'body'            => $body,
                    'external_id'     => $externalId,
                    'status'          => 'received',
                    'related_user_id' => $relatedUserId,
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "[SMS] Failed to store inbound message: " . $e->getMessage());
        }
    }

    /**
     * Process inbound SMS commands and return a reply message (or null)
     *
     * @param string $from Sender phone number
     * @param string $body Message body
     * @return string|null Reply message or null
     */
    protected function processCommand(string $from, string $body): ?string
    {
        $command = strtoupper(trim($body));

        // Only process known commands (ignore regular messages)
        if (!in_array($command, ['ACCEPT', 'DECLINE', 'STATUS', 'HELP'])) {
            return null;
        }

        try {
            $db = \Config\Database::connect();

            // Find the driver by phone number
            $driver = $db->table('drivers')
                ->where('phone', $from)
                ->where('status', 'active')
                ->get()
                ->getRow();

            if (!$driver) {
                return "⚠️ Your phone number is not registered as an active driver.";
            }

            return match ($command) {
                'ACCEPT'  => $this->handleAccept($db, $driver),
                'DECLINE' => $this->handleDecline($db, $driver),
                'STATUS'  => $this->handleStatus($db, $driver),
                'HELP'    => "📱 Available commands:\nACCEPT — Accept your latest trip\nDECLINE — Decline your latest trip\nSTATUS — View your current trip\nHELP — Show this help",
                default   => null,
            };
        } catch (\Exception $e) {
            log_message('error', "[SMS] Command processing error: " . $e->getMessage());
            return "⚠️ System error. Please try again or contact dispatch.";
        }
    }

    /**
     * Handle ACCEPT command — accept the latest pending trip
     */
    protected function handleAccept(object $db, object $driver): string
    {
        $trip = $db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) {
            return "ℹ️ No pending trips to accept. You're all clear!";
        }

        $db->table('trips')
            ->where('id', $trip->id)
            ->update(['status' => 'active']);

        log_message('info', "[SMS] Driver #{$driver->id} ({$driver->first_name}) accepted trip #{$trip->trip_number} via SMS");

        return "✅ Trip #{$trip->trip_number} accepted! Navigate to: {$trip->pickup_address}";
    }

    /**
     * Handle DECLINE command — decline the latest pending trip
     */
    protected function handleDecline(object $db, object $driver): string
    {
        $trip = $db->table('trips')
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) {
            return "ℹ️ No pending trips to decline.";
        }

        // Unassign driver and return to queue
        $db->table('trips')
            ->where('id', $trip->id)
            ->update([
                'driver_id' => null,
                'status'    => 'pending',
                'driver_earnings'    => null,
                'commission_amount'  => null,
            ]);

        log_message('info', "[SMS] Driver #{$driver->id} ({$driver->first_name}) declined trip #{$trip->trip_number} via SMS");

        return "❌ Trip #{$trip->trip_number} declined. It has been returned to the dispatch queue.";
    }

    /**
     * Handle STATUS command — report current trip status
     */
    protected function handleStatus(object $db, object $driver): string
    {
        $trip = $db->table('trips')
            ->whereIn('status', ['active', 'dispatching', 'pending'])
            ->where('driver_id', $driver->id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$trip) {
            return "ℹ️ No active trips. You're currently available.";
        }

        $status = ucfirst($trip->status);
        $fare = number_format((float)($trip->fare_amount ?? 0), 2);

        return "📋 Trip #{$trip->trip_number}\nStatus: {$status}\nPickup: {$trip->pickup_address}\nDropoff: {$trip->dropoff_address}\nFare: \${$fare}";
    }
}
