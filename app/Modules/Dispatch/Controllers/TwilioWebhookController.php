<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Twilio\TwiML\MessagingResponse;

class TwilioWebhookController extends BaseController
{
    /**
     * Endpoint to receive incoming Twilio Webhooks
     */
    public function receive()
    {
        // 1. Get incoming data
        $from = $this->request->getPost('From');
        $body = $this->request->getPost('Body');

        if (!$from || !$body) {
            return $this->response->setStatusCode(400)->setBody("Missing From or Body");
        }

        // Handle State Transitions and Routing
        $logicService = new \Modules\Dispatch\Services\SmsLogicService();
        $replyMessage = $logicService->processIncomingSms($from, $body);

        // If the logic service returns an empty string (e.g. Proxy msg), don't send TwiML reply
        if (trim($replyMessage) === '') {
            return $this->response->setStatusCode(200);
        }

        // Return TwiML
        $response = new MessagingResponse();
        $response->message($replyMessage);

        return $this->response->setContentType('text/xml')->setBody($response->asXML());
    }
}
