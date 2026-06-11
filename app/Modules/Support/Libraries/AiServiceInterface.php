<?php

namespace App\Modules\Support\Libraries;

interface AiServiceInterface
{
    /**
     * Ask the AI a question
     * 
     * @param string $message
     * @param array $history
     * @return string|null
     */
    public function ask(string $message, array $history = []): ?string;
}
