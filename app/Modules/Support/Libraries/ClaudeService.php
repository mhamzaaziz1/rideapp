<?php

namespace App\Modules\Support\Libraries;

use App\Modules\Support\Config\AI as AIConfig;

class ClaudeService implements AiServiceInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = new AIConfig();
    }

    public function ask(string $message, array $history = []): ?string
    {
        if (empty($this->config->claudeKey)) return null;

        $messages = [];
        foreach (array_slice($history, -5) as $msg) {
            $role = ($msg['sender_role'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['message']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = [
            'model' => $this->config->claudeModel,
            'max_tokens' => $this->config->maxTokens,
            'system' => $this->config->systemPrompt,
            'messages' => $messages,
            'temperature' => $this->config->temperature,
        ];

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->config->claudeKey,
            'anthropic-version: 2023-06-01'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['content'][0]['text'] ?? null;
    }
}
