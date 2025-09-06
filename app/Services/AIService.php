<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Helpers\AppSettingHelper;
use OpenAI\Client;
use OpenAI\Factory;
use Exception;

class AIService
{
    protected Client $client;
    protected string $service;
    protected array $config;

    public function __construct(string $service = null)
    {
        $this->service = $service ?? config('ai.default', 'openai');
        $this->config = config("ai.{$this->service}");
        
        if (!$this->config) {
            throw new Exception("AI service '{$this->service}' is not configured.");
        }

        // Load API keys from database
        $this->loadDatabaseSettings();
        $this->initializeClient();
    }

    /**
     * Load settings from database
     */
    protected function loadDatabaseSettings(): void
    {
        $appSettings = AppSetting::getSettings();
        
        if ($appSettings) {
            // Set API keys from database
            if ($this->service === 'openai') {
                $this->config['api_key'] = $appSettings->getOpenAIKey();
            } elseif ($this->service === 'deepseek') {
                $this->config['api_key'] = $appSettings->getDeepSeekKey();
            }
            
            // Override models from database if available
            $dbModels = $appSettings->getModelsForService($this->service);
            if (!empty($dbModels)) {
                $this->config['models'] = $dbModels;
            }
            
            // Set default model from database
            $defaultModel = $appSettings->getDefaultModelForService($this->service);
            if ($defaultModel) {
                $this->config['default_model'] = $defaultModel;
            }
        }
        
        if (!$this->config['api_key']) {
            throw new Exception("API key for '{$this->service}' service is not configured in database.");
        }
    }

    /**
     * Initialize the OpenAI client for the specified service
     */
    protected function initializeClient(): void
    {
        $factory = new Factory();
        
        // Configure the client with API key and base URL
        $this->client = $factory
            ->withApiKey($this->config['api_key'])
            ->withBaseUri($this->config['base_url'])
            ->withHttpClient(new \GuzzleHttp\Client([
                'timeout' => config('ai.request.timeout', 60),
            ]))
            ->make();
    }

    /**
     * Create a chat completion with fallback support
     */
    public function chat(array $messages, array $options = []): array
    {
        $defaultModel = $this->getDefaultModel();
        $appSettings = AppSetting::getSettings();
        
        try {
            $response = $this->client->chat()->create(array_merge([
                'model' => $defaultModel,
                'messages' => $messages,
                'max_tokens' => $appSettings ? $appSettings->getAIMaxTokens() : config('ai.request.max_tokens', 2000),
                'temperature' => $appSettings ? $appSettings->getAITemperature() : config('ai.request.temperature', 0.7),
            ], $options));

            return $response->toArray();
        } catch (Exception $e) {
            // Try fallback model if available
            return $this->chatWithFallback($messages, $options, $e);
        }
    }
    
    /**
     * Attempt chat completion with fallback model
     */
    protected function chatWithFallback(array $messages, array $options, Exception $originalException): array
    {
        $fallbackModel = $this->getFallbackModel();
        
        if (!$fallbackModel) {
            throw $originalException;
        }
        
        try {
            // Create new service instance for fallback provider
            $fallbackService = new self($fallbackModel['provider']);
            $appSettings = AppSetting::getSettings();
            
            $response = $fallbackService->client->chat()->create(array_merge([
                'model' => $fallbackModel['model'],
                'messages' => $messages,
                'max_tokens' => $appSettings ? $appSettings->getAIMaxTokens() : config('ai.request.max_tokens', 2000),
                'temperature' => $appSettings ? $appSettings->getAITemperature() : config('ai.request.temperature', 0.7),
            ], $options));

            return $response->toArray();
        } catch (Exception $fallbackException) {
            // If fallback also fails, throw the original exception
            throw $originalException;
        }
    }

    /**
     * Create a simple text completion
     */
    public function complete(string $prompt, array $options = []): string
    {
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->chat($messages, $options);
        
        return $response['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Get available models for the current service
     */
    public function getModels(): array
    {
        return $this->config['models'] ?? [];
    }

    /**
     * Get the default model for the current service
     */
    protected function getDefaultModel(): string
    {
        // Check if default model is set in database config
        if (isset($this->config['default_model'])) {
            return $this->config['default_model'];
        }
        
        $models = $this->getModels();
        return reset($models) ?: 'gpt-3.5-turbo';
    }
    
    /**
     * Get the fallback model from cache
     */
    protected function getFallbackModel(): ?array
    {
        return AppSettingHelper::getFallbackModel();
    }
    
    /**
     * Check if a fallback model is available
     */
    public function hasFallbackModel(): bool
    {
        return AppSettingHelper::hasFallbackModel();
    }

    /**
     * Get the current service name
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Create a new instance for a specific service
     */
    public static function for(string $service): self
    {
        return new self($service);
    }

    /**
     * Create OpenAI service instance
     */
    public static function openai(): self
    {
        return new self('openai');
    }

    /**
     * Create DeepSeek service instance
     */
    public static function deepseek(): self
    {
        return new self('deepseek');
    }
}