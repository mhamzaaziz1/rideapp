<?php

namespace App\Modules\Dispatch\Services;

use Twilio\Rest\Client;
use Config\Twilio;
use App\Modules\Dispatch\Models\CommunicationLogModel;

class TwilioService
{
    protected $client;
    protected $twilioNumber;
    protected $logModel;

    public function __construct()
    {
        $config = new Twilio();
        if (!empty($config->sid) && !empty($config->token)) {
            $this->client = new Client($config->sid, $config->token);
        }
        $this->twilioNumber = $config->number;
        $this->logModel = new CommunicationLogModel();
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

            $this->logModel->insert([
                'type' => 'sms',
                'direction' => 'outbound',
                'from_number' => $this->twilioNumber,
                'to_number' => $to,
                'user_type' => 'system',
                'content' => $message,
                'action_taken' => 'Sent automated SMS'
            ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Twilio Error: ' . $e->getMessage());
            return false;
        }
    }
}
