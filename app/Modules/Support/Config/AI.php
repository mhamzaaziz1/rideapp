<?php

namespace App\Modules\Support\Config;

use CodeIgniter\Config\BaseConfig;

class AI extends BaseConfig
{
    /**
     * Active AI Provider: 'openai', 'claude', 'gemini'
     */
    public $activeProvider = 'openai';

    /**
     * OpenAI Settings
     */
    public $openaiKey = '';
    public $openaiModel = 'gpt-4o';

    /**
     * Anthropic (Claude) Settings
     */
    public $claudeKey = '';
    public $claudeModel = 'claude-3-5-sonnet-20240620';

    /**
     * Google (Gemini) Settings
     */
    public $geminiKey = '';
    public $geminiModel = 'gemini-1.5-pro';

    /**
     * Shared Settings
     */
    public $temperature = 0.7;
    public $maxTokens = 500;
    public $systemPrompt = "You are the official support assistant for RideApp. Be helpful, professional, and concise.";

    public function __construct()
    {
        parent::__construct();
        
        $db = \Config\Database::connect();
        if ($db->tableExists('support_settings')) {
            $settings = $db->table('support_settings')->get()->getResultArray();
            $map = array_column($settings, 'setting_value', 'setting_key');

            $this->activeProvider = $map['ai_provider'] ?? $this->activeProvider;
            $this->openaiKey = $map['openai_key'] ?? $this->openaiKey;
            $this->claudeKey = $map['claude_key'] ?? $this->claudeKey;
            $this->geminiKey = $map['gemini_key'] ?? $this->geminiKey;
            $this->systemPrompt = $map['ai_system_prompt'] ?? $this->systemPrompt;
        }

        // Fallback to .env for sensitive keys if db is empty
        $this->openaiKey = empty($this->openaiKey) ? env('openai.apiKey', '') : $this->openaiKey;
        $this->claudeKey = empty($this->claudeKey) ? env('claude.apiKey', '') : $this->claudeKey;
        $this->geminiKey = empty($this->geminiKey) ? env('gemini.apiKey', '') : $this->geminiKey;
    }
}
