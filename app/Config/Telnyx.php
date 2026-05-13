<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Telnyx extends BaseConfig
{
    /**
     * API Key from portal.telnyx.com
     */
    public string $apiKey;

    /**
     * Telnyx Phone Number (E.164 format)
     */
    public string $number;

    /**
     * Messaging Profile ID from Telnyx portal
     */
    public string $messagingProfileId;

    /**
     * Public Key for webhook signature verification
     */
    public string $publicKey;

    public function __construct()
    {
        parent::__construct();
        
        $this->apiKey = env('TELNYX_API_KEY', '');
        $this->number = env('TELNYX_NUMBER', '');
        $this->messagingProfileId = env('TELNYX_MESSAGING_PROFILE_ID', '');
        $this->publicKey = env('TELNYX_PUBLIC_KEY', '');
    }
}
