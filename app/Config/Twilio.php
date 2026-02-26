<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Twilio extends BaseConfig
{
    /**
     * Account SID from twilio.com/console
     */
    public string $sid;

    /**
     * Auth Token from twilio.com/console
     */
    public string $token;

    /**
     * Twilio Phone Number
     */
    public string $number;

    public function __construct()
    {
        parent::__construct();
        
        $this->sid = env('TWILIO_SID', '');
        $this->token = env('TWILIO_TOKEN', '');
        $this->number = env('TWILIO_NUMBER', '');
    }
}
