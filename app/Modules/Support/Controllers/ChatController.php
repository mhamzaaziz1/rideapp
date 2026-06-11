<?php

namespace App\Modules\Support\Controllers;

use App\Controllers\BaseController;
use App\Modules\Support\Models\ConversationModel;
use App\Modules\Support\Models\MessageModel;
use App\Modules\Support\Libraries\BotService;
use CodeIgniter\API\ResponseTrait;

class ChatController extends BaseController
{
    use ResponseTrait;

    protected $conversationModel;
    protected $messageModel;
    protected $botService;

    public function __construct()
    {
        $this->conversationModel = new ConversationModel();
        $this->messageModel = new MessageModel();
        $this->botService = new BotService();
    }

    /**
     * Serve the chat widget as a standalone HTML page for iframe embedding
     */
    public function embed()
    {
        // Allow framing by removing CodeIgniter's default X-Frame-Options SAMEORIGIN
        $this->response->removeHeader('X-Frame-Options');
        // Set an open CSP for frames if needed
        $this->response->setHeader('Content-Security-Policy', "frame-ancestors *");

        return view('App\Modules\Support\Views\embed_widget');
    }

    public function getConversation()
    {
        $userId = session()->get('user_id'); 
        $userType = session()->get('user_type') ?? 'customer';

        $guestName = $this->request->getPost('guest_name');
        $guestEmail = $this->request->getPost('guest_email');
        $guestPhone = $this->request->getPost('guest_phone');
        $subject = $this->request->getPost('subject');

        // Check for existing active conversation for logged-in user
        $conversation = null;
        if ($userId) {
            $conversation = $this->conversationModel->getActiveConversation($userId, $userType);
        }

        if (!$conversation) {
            $id = $this->conversationModel->insert([
                'user_id' => $userId,
                'user_type' => $userType,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'guest_phone' => $guestPhone,
                'subject' => $subject,
                'status' => 'bot_active'
            ]);
            $conversation = $this->conversationModel->find($id);
            
            // Send initial bot greeting
            $this->messageModel->insert([
                'conversation_id' => $id,
                'sender_id' => 0,
                'sender_role' => 'bot',
                'message' => "Welcome to RideApp Support! How can we help you today?"
            ]);
        } else {
            // Update timestamp for existing conversation to show activity
            $this->conversationModel->update($conversation['id'], ['updated_at' => date('Y-m-d H:i:s')]);
        }

        return $this->respond($conversation);
    }

    public function getMessages($conversationId)
    {
        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || $conversation['status'] === 'closed') {
            return $this->failNotFound('Conversation is closed or not found');
        }

        $messages = $this->messageModel->getMessagesByConversation($conversationId);
        return $this->respond($messages);
    }

    public function sendMessage()
    {
        $conversationId = $this->request->getPost('conversation_id');
        $messageText = $this->request->getPost('message');
        $userId = session()->get('user_id') ?? 0;

        if (!$conversationId || !$messageText) {
            return $this->fail('Missing parameters');
        }

        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || $conversation['status'] === 'closed') {
            return $this->fail('Cannot send messages to a closed conversation');
        }

        // Save user message
        $this->messageModel->insert([
            'conversation_id' => $conversationId,
            'sender_id' => $userId,
            'sender_role' => 'user',
            'message' => $messageText
        ]);

        // If bot is active, get bot response
        if ($conversation['status'] === 'bot_active') {
            
            $userType = session()->get('user_type') ?? 'customer';

            if (strtolower(trim($messageText)) === 'agent') {
                $this->conversationModel->update($conversationId, ['status' => 'agent_active']);
                $botResponse = "Understood. I am connecting you to an agent. Please wait...";
            } else {
                $botResponse = $this->botService->getResponse($messageText, $userId, $userType, $conversationId);
            }

            $this->messageModel->insert([
                'conversation_id' => $conversationId,
                'sender_id' => 0,
                'sender_role' => 'bot',
                'message' => $botResponse
            ]);
        }

        // Update conversation timestamp to bring it to the top of the list
        $this->conversationModel->update($conversationId, ['updated_at' => date('Y-m-d H:i:s')]);

        return $this->respondCreated(['status' => 'success']);
    }
}
