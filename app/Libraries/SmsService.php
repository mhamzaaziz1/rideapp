<?php

namespace App\Libraries;

use Config\Twilio as TwilioConfig;
use Config\Telnyx as TelnyxConfig;

/**
 * Unified SMS Service
 * 
 * Supports multiple SMS providers (Twilio, Telnyx) via a strategy pattern.
 * The active provider is determined by the app settings stored in writable/settings.json.
 * 
 * Features:
 *  - Provider auto-selection from settings
 *  - Credential cascading: Settings → .env → empty (with placeholder rejection)
 *  - E.164 phone number validation
 *  - Duplicate send protection (60s window)
 *  - Full error reporting and structured logging
 */
class SmsService
{
    protected string $provider;
    protected array  $settings;

    /** @var array Tracks recent sends to prevent duplicates within 60s */
    private static array $recentSends = [];

    public function __construct()
    {
        $settingsFile = WRITEPATH . 'settings.json';
        $this->settings = [];

        if (file_exists($settingsFile)) {
            $this->settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }

        // Default to 'twilio' if no provider is configured
        $this->provider = $this->settings['sms_provider'] ?? 'twilio';
    }

    /**
     * Get the active SMS provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Send an SMS message
     *
     * @param string $to   Recipient phone number in E.164 format
     * @param string $body Message text
     * @return array       ['success' => bool, 'message_id' => string|null, 'error' => string|null, 'provider' => string]
     */
    public function send(string $to, string $body): array
    {
        // Normalize and validate the phone number
        $to = $this->normalizePhone($to);

        if (!$this->isValidE164($to)) {
            return [
                'success'    => false,
                'message_id' => null,
                'error'      => "Invalid phone number format. Expected E.164 (e.g. +15551234567), got: {$to}",
                'provider'   => $this->provider,
            ];
        }

        // Deduplication: prevent the same (to + body) within 60 seconds
        $hash = md5($to . '|' . $body);
        $now  = time();
        if (isset(self::$recentSends[$hash]) && ($now - self::$recentSends[$hash]) < 60) {
            log_message('warning', "[SmsService] Duplicate send suppressed to {$to} within 60s window.");
            return [
                'success'    => true,
                'message_id' => null,
                'error'      => null,
                'provider'   => $this->provider,
            ];
        }
        self::$recentSends[$hash] = $now;

        $result = match ($this->provider) {
            'telnyx' => $this->sendViaTelnyx($to, $body),
            'twilio' => $this->sendViaTwilio($to, $body),
            default  => [
                'success'    => false,
                'message_id' => null,
                'error'      => "Unknown SMS provider: {$this->provider}",
                'provider'   => $this->provider,
            ],
        };

        $result['provider'] = $this->provider;
        return $result;
    }

    /**
     * Send SMS via Twilio REST API
     */
    protected function sendViaTwilio(string $to, string $body): array
    {
        $sid   = $this->getCredential('twilio_sid', 'sid', TwilioConfig::class);
        $token = $this->getCredential('twilio_token', 'token', TwilioConfig::class);
        $from  = $this->getCredential('twilio_number', 'number', TwilioConfig::class);

        if (empty($sid) || empty($token) || empty($from)) {
            return ['success' => false, 'message_id' => null, 'error' => 'Twilio credentials not configured. Set Account SID, Auth Token, and Phone Number.'];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => "{$sid}:{$token}",
            CURLOPT_POSTFIELDS     => http_build_query([
                'From' => $from,
                'To'   => $to,
                'Body' => $body,
            ]),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', "[SmsService][Twilio] cURL Error: {$curlError}");
            return ['success' => false, 'message_id' => null, 'error' => "Connection error: {$curlError}"];
        }

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $msgSid = $result['sid'] ?? null;
            log_message('info', "[SmsService][Twilio] ✓ Sent to {$to} | SID: {$msgSid} | Status: " . ($result['status'] ?? 'n/a'));
            return ['success' => true, 'message_id' => $msgSid, 'error' => null];
        }

        $errorMsg = $result['message'] ?? 'Unknown Twilio error';
        $errorCode = $result['code'] ?? $httpCode;
        log_message('error', "[SmsService][Twilio] ✗ Failed ({$httpCode}, code:{$errorCode}): {$errorMsg}");
        return ['success' => false, 'message_id' => null, 'error' => "[{$errorCode}] {$errorMsg}"];
    }

    /**
     * Send SMS via Telnyx v2 API
     */
    protected function sendViaTelnyx(string $to, string $body): array
    {
        $apiKey             = $this->getCredential('telnyx_api_key', 'apiKey', TelnyxConfig::class);
        $from               = $this->getCredential('telnyx_number', 'number', TelnyxConfig::class);
        $messagingProfileId = $this->getCredential('telnyx_messaging_profile_id', 'messagingProfileId', TelnyxConfig::class);

        if (empty($apiKey) || empty($from)) {
            return ['success' => false, 'message_id' => null, 'error' => 'Telnyx credentials not configured. Set API Key and Phone Number.'];
        }

        $url = 'https://api.telnyx.com/v2/messages';

        $payload = [
            'from' => $from,
            'to'   => $to,
            'text' => $body,
            'type' => 'SMS',
        ];

        // Include messaging profile ID if set
        if (!empty($messagingProfileId)) {
            $payload['messaging_profile_id'] = $messagingProfileId;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', "[SmsService][Telnyx] cURL Error: {$curlError}");
            return ['success' => false, 'message_id' => null, 'error' => "Connection error: {$curlError}"];
        }

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $msgId = $result['data']['id'] ?? null;
            log_message('info', "[SmsService][Telnyx] ✓ Sent to {$to} | ID: {$msgId} | Carrier: " . ($result['data']['carrier']['name'] ?? 'n/a'));
            return ['success' => true, 'message_id' => $msgId, 'error' => null];
        }

        // Telnyx error structure: { errors: [{ title, detail, code, meta }] }
        $errorTitle  = $result['errors'][0]['title']  ?? 'Unknown error';
        $errorDetail = $result['errors'][0]['detail'] ?? '';
        $errorCode   = $result['errors'][0]['code']   ?? $httpCode;
        $errorMsg    = $errorDetail ?: $errorTitle;
        log_message('error', "[SmsService][Telnyx] ✗ Failed ({$httpCode}, code:{$errorCode}): {$errorMsg}");
        return ['success' => false, 'message_id' => null, 'error' => "[{$errorCode}] {$errorMsg}"];
    }

    /**
     * Send a test SMS to verify provider configuration
     *
     * @param string $to Test phone number
     * @return array
     */
    public function sendTest(string $to): array
    {
        $providerName = ucfirst($this->provider);
        $appName = $this->settings['company_name'] ?? 'RideFlow';

        // Bypass deduplication for test messages by including a unique nonce
        $nonce = substr(md5(uniqid()), 0, 6);
        return $this->send($to, "✅ [{$appName}] {$providerName} SMS test successful! Ref: {$nonce} — " . date('Y-m-d H:i:s'));
    }

    /**
     * Check if the current provider is properly configured
     */
    public function isConfigured(): bool
    {
        return match ($this->provider) {
            'twilio' => !empty($this->getCredential('twilio_sid', 'sid', TwilioConfig::class))
                     && !empty($this->getCredential('twilio_token', 'token', TwilioConfig::class))
                     && !empty($this->getCredential('twilio_number', 'number', TwilioConfig::class)),
            'telnyx' => !empty($this->getCredential('telnyx_api_key', 'apiKey', TelnyxConfig::class))
                     && !empty($this->getCredential('telnyx_number', 'number', TelnyxConfig::class)),
            default  => false,
        };
    }

    /**
     * Get the "from" phone number for the active provider
     */
    public function getFromNumber(): string
    {
        return match ($this->provider) {
            'twilio' => $this->getCredential('twilio_number', 'number', TwilioConfig::class),
            'telnyx' => $this->getCredential('telnyx_number', 'number', TelnyxConfig::class),
            default  => '',
        };
    }

    /**
     * Resolve a credential value with cascading: settings.json → .env config → empty
     * Rejects common placeholder values like "YOUR_TWILIO_ACCOUNT_SID"
     *
     * @param string $settingsKey  Key in settings.json
     * @param string $configProp   Property name on the Config class
     * @param string $configClass  Fully-qualified Config class name
     * @return string
     */
    protected function getCredential(string $settingsKey, string $configProp, string $configClass): string
    {
        // 1. Try settings.json first
        $value = $this->settings[$settingsKey] ?? '';

        // 2. If empty, fallback to .env config
        if (empty($value)) {
            try {
                $config = config($configClass);
                $value = $config->{$configProp} ?? '';
            } catch (\Exception $e) {
                $value = '';
            }
        }

        // 3. Reject placeholder values
        if ($this->isPlaceholder($value)) {
            return '';
        }

        return trim($value);
    }

    /**
     * Check if a value is a placeholder (not a real credential)
     */
    protected function isPlaceholder(string $value): bool
    {
        $placeholders = [
            'YOUR_TWILIO_ACCOUNT_SID',
            'YOUR_TWILIO_AUTH_TOKEN',
            'YOUR_TWILIO_PHONE_NUMBER',
            'YOUR_TELNYX_API_KEY',
            'YOUR_TELNYX_PHONE_NUMBER',
            'YOUR_TELNYX_MESSAGING_PROFILE_ID',
            'YOUR_TELNYX_PUBLIC_KEY',
        ];

        return in_array(strtoupper(trim($value)), $placeholders, true)
            || str_starts_with(strtoupper(trim($value)), 'YOUR_');
    }

    /**
     * Normalize a phone number (basic cleanup)
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = trim($phone);

        // Remove all non-digit characters except leading +
        if (str_starts_with($phone, '+')) {
            $phone = '+' . preg_replace('/[^0-9]/', '', substr($phone, 1));
        } else {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            // Assume + prefix if it looks like an international number (10+ digits)
            if (strlen($phone) >= 10) {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }

    /**
     * Validate E.164 phone number format
     */
    protected function isValidE164(string $phone): bool
    {
        // E.164: + followed by 7-15 digits
        return (bool) preg_match('/^\+[1-9]\d{6,14}$/', $phone);
    }

    /**
     * Get a summary of the current configuration status
     * Useful for the settings UI
     *
     * @return array{provider: string, configured: bool, from_number: string, details: string}
     */
    public function getStatus(): array
    {
        $configured = $this->isConfigured();
        $from = $this->getFromNumber();

        $details = match (true) {
            !$configured => 'Missing credentials — configure your ' . ucfirst($this->provider) . ' API keys.',
            default      => 'Ready to send via ' . ucfirst($this->provider) . ' from ' . $from,
        };

        return [
            'provider'    => $this->provider,
            'configured'  => $configured,
            'from_number' => $from,
            'details'     => $details,
        ];
    }
}
