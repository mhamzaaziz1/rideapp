<?php

namespace Modules\Dispatch\Services;

use Twilio\Rest\Client;
use Config\Twilio;

class TwilioService
{
    protected $client;
    protected $twilioNumber;

    public function __construct()
    {
        $config = new Twilio();
        if (!empty($config->sid) && !empty($config->token)) {
            $this->client = new Client($config->sid, $config->token);
        }
        $this->twilioNumber = $config->number;
    }

    /**
     * Send an SMS message via Twilio
     */
    public function sendSms($to, $message)
    {
        if (!$this->client) {
            log_message('error', 'Twilio Client not initialized. Check credentials.');
            return false;
        }

        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => $this->twilioNumber,
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Twilio Error: ' . $e->getMessage());
            return false;
        }
    }
}
