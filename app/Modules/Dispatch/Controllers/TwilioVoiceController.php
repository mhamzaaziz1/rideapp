<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Twilio\TwiML\VoiceResponse;

class TwilioVoiceController extends BaseController
{
    /**
     * Initial endpoint hit when Twilio number is called
     */
    public function inbound()
    {
        $from = $this->request->getPost('From');
        
        if (!$from) {
            $response = new VoiceResponse();
            $response->say("We are sorry, an error occurred. Please try again.");
            return $this->response->setContentType('text/xml')->setBody($response->asXML());
        }

        $logicService = new \Modules\Dispatch\Services\VoiceLogicService();
        $twiMlContent = $logicService->processInboundCall($from);

        return $this->response->setContentType('text/xml')->setBody($twiMlContent);
    }

    /**
     * Webhook hit when Driver presses a key on the menu
     */
    public function gatherDriver()
    {
        $from   = $this->request->getPost('From');
        $digits = $this->request->getPost('Digits'); // 0, 1, 2, or 3
        
        $logicService = new \Modules\Dispatch\Services\VoiceLogicService();
        $twiMlContent = $logicService->processDriverInput($from, $digits);

        return $this->response->setContentType('text/xml')->setBody($twiMlContent);
    }

    /**
     * Webhook hit when Customer presses a key on the menu
     */
    public function gatherCustomer()
    {
        $from   = $this->request->getPost('From');
        $digits = $this->request->getPost('Digits'); // 0 or 1
        
        $logicService = new \Modules\Dispatch\Services\VoiceLogicService();
        $twiMlContent = $logicService->processCustomerInput($from, $digits);

        return $this->response->setContentType('text/xml')->setBody($twiMlContent);
    }
}
