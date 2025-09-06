<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for AI services like OpenAI and DeepSeek.
    | Both services use OpenAI-compatible API format.
    |
    */

    'openai' => [
        'api_key' => null, // Will be loaded from database
        'base_url' => 'https://api.openai.com/v1',
        'organization' => env('OPENAI_ORGANIZATION'),
        'models' => [
            // Fallback models - will be overridden by database if available
            'gpt-4' => 'gpt-4',
            'gpt-4-turbo' => 'gpt-4-turbo-preview',
            'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        ],
    ],

    'deepseek' => [
        'api_key' => null, // Will be loaded from database
        'base_url' => 'https://api.deepseek.com/v1',
        'models' => [
            // Fallback models - will be overridden by database if available
            'deepseek-chat' => 'deepseek-chat',
            'deepseek-coder' => 'deepseek-coder',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Service
    |--------------------------------------------------------------------------
    |
    | The default AI service to use when none is specified.
    |
    */
    'default' => env('AI_DEFAULT_SERVICE', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Request Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for API requests.
    | These will be overridden by database settings if available.
    |
    */
    'request' => [
        'timeout' => 60,
        'max_tokens' => 2000, // Will be overridden by database
        'temperature' => 0.7, // Will be overridden by database
    ],
];