<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file manages the OpenAI API settings for the application.
    | The API key and other sensitive data should be stored in the .env file.
    |
    */

    'openai' => [
        'api_type' => env('OPENAI_API_TYPE', 'azure'), // 'azure' or 'openai'
        'api_version' => env('OPENAI_API_VERSION', '2024-02-15-preview'),
        'api_base' => env('OPENAI_API_BASE'),
        'api_key' => env('OPENAI_API_KEY'),
        'deployment_name' => env('OPENAI_DEPLOYMENT_NAME'),
        'organization' => env('OPENAI_ORGANIZATION', null),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    ],
];