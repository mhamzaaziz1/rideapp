<?php

namespace App\Modules\Support\Libraries;

use App\Modules\Support\Config\AI as AIConfig;

class GeminiService implements AiServiceInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = new AIConfig();
    }

    public function ask(string $message, array $history = []): ?string
    {
        if (empty($this->config->geminiKey)) return null;

        $contents = [];
        // Gemini likes system instructions separately or as first message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $this->config->systemPrompt]]];
        $contents[] = ['role' => 'model', 'parts' => [['text' => 'Understood. I will act as the RideApp support assistant.']]];

        foreach (array_slice($history, -5) as $msg) {
            $role = ($msg['sender_role'] === 'user') ? 'user' : 'model';
            $contents[] = ['role' => $role, 'parts' => [['text' => $msg['message']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->config->temperature,
                'maxOutputTokens' => $this->config->maxTokens,
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->config->geminiModel}:generateContent?key={$this->config->geminiKey}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
