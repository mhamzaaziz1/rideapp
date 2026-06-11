<?php

namespace App\Modules\Support\Libraries;

use App\Modules\Support\Config\AI as AIConfig;

class AiManager
{
    protected $config;
    protected $service;

    public function __construct()
    {
        $this->config = new AIConfig();
        $this->service = $this->resolveService();
    }

    protected function resolveService(): ?AiServiceInterface
    {
        return match ($this->config->activeProvider) {
            'openai' => new OpenAiService(),
            'claude' => new ClaudeService(),
            'gemini' => new GeminiService(),
            default  => null,
        };
    }

    public function ask(string $message, array $history = []): ?string
    {
        if (!$this->service) return null;
        return $this->service->ask($message, $history);
    }
}
