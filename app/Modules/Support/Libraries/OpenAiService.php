<?php

namespace App\Modules\Support\Libraries;

use App\Modules\Support\Config\AI as AIConfig;

class OpenAiService implements AiServiceInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = new AIConfig();
    }

    /**
     * Get a response from OpenAI
     * 
     * @param string $userMessage
     * @param array $chatHistory Optional history for context
     * @return string|null
     */
    public function ask(string $userMessage, array $chatHistory = []): ?string
    {
        if (empty($this->config->openaiKey)) {
            return null;
        }

        $messages = [
            ['role' => 'system', 'content' => $this->config->systemPrompt]
        ];

        // Add history if needed (limited to last 5 for tokens)
        foreach (array_slice($chatHistory, -5) as $msg) {
            $role = ($msg['sender_role'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['message']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $payload = [
            'model' => $this->config->openaiModel,
            'messages' => $messages,
            'temperature' => $this->config->temperature,
            'max_tokens' => $this->config->maxTokens
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->openaiKey
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'OpenAI Error: ' . $err);
            return null;
        }

        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            return trim($result['choices'][0]['message']['content']);
        }

        log_message('error', 'OpenAI API Invalid Response: ' . $response);
        return null;
    }
}
