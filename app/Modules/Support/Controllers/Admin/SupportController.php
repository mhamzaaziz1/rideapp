<?php

namespace App\Modules\Support\Controllers\Admin;

use App\Controllers\BaseController;
use App\Modules\Support\Models\ConversationModel;
use App\Modules\Support\Models\MessageModel;

class SupportController extends BaseController
{
    protected $conversationModel;
    protected $messageModel;

    public function __construct()
    {
        $this->conversationModel = new ConversationModel();
        $this->messageModel = new MessageModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Support Dashboard',
            'conversations' => $this->conversationModel
                ->select('chat_conversations.*, 
                         CASE 
                            WHEN chat_conversations.user_type = "customer" AND customers.first_name IS NOT NULL THEN CONCAT(customers.first_name, " ", customers.last_name)
                            WHEN chat_conversations.user_type = "driver" AND drivers.first_name IS NOT NULL THEN CONCAT(drivers.first_name, " ", drivers.last_name)
                            WHEN chat_conversations.guest_name IS NOT NULL THEN CONCAT(chat_conversations.guest_name, " (Guest)")
                            ELSE "Unknown"
                         END as user_name')
                ->join('customers', 'customers.id = chat_conversations.user_id AND chat_conversations.user_type = "customer"', 'left')
                ->join('drivers', 'drivers.id = chat_conversations.user_id AND chat_conversations.user_type = "driver"', 'left')
                ->orderBy('updated_at', 'DESC')
                ->findAll()
        ];

        return view('App\Modules\Support\Views\admin\dashboard', $data);
    }

    public function viewConversation($id)
    {
        $conversation = $this->conversationModel->find($id);
        if (!$conversation) {
            return redirect()->to(base_url('admin/support'))->with('error', 'Conversation not found');
        }

        $messages = $this->messageModel->getMessagesByConversation($id);

        return $this->response->setJSON([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    public function sendReply()
    {
        $conversationId = $this->request->getPost('conversation_id');
        $messageText = $this->request->getPost('message');
        $agentId = session()->get('user_id') ?? 1;

        if (!$conversationId || !$messageText) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing data']);
        }

        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || $conversation['status'] === 'closed') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cannot reply to a closed conversation']);
        }

        // Save agent message
        $this->messageModel->insert([
            'conversation_id' => $conversationId,
            'sender_id' => $agentId,
            'sender_role' => 'agent',
            'message' => $messageText
        ]);

        // Update conversation status if it was bot_active
        $this->conversationModel->update($conversationId, [
            'status' => 'agent_active',
            'agent_id' => $agentId
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function closeConversation($id)
    {
        $this->conversationModel->update($id, ['status' => 'closed']);
        return $this->response->setJSON(['status' => 'success']);
    }
}
