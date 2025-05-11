<?php

namespace App\AI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use OpenAI\Client;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Validator;

class AssistantService
{
    protected Client $client;
    protected array $config;
    protected string $promptsPath;

    public function __construct()
    {
        $this->config = Config::get('ai.openai');
        $this->promptsPath = resource_path('prompts');
        
        if (empty($this->config['api_key'])) {
            throw new \RuntimeException('OpenAI API key is not configured');
        }
    }

    public function ask(string $prompt, ?array $schema = null): array
    {
        $response = OpenAI::chat()->create([
            'model' => $this->config['model'],
            'temperature' => $this->config['temperature'],
            'max_tokens' => $this->config['max_tokens'],
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = $response->choices[0]->message->content;
        $decodedResponse = json_decode($content, true);

        if ($schema) {
            $validator = Validator::make($decodedResponse, $schema);
            if ($validator->fails()) {
                throw new \InvalidArgumentException('Response does not match schema: ' . $validator->errors()->first());
            }
        }

        return $decodedResponse;
    }

    public function loadPromptTemplate(string $templateName): string
    {
        $templatePath = $this->promptsPath . '/' . $templateName . '.md';
        
        if (!File::exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$templateName}");
        }
        
        return File::get($templatePath);
    }

    public function processTemplate(string $template, array $variables): string
    {
        return preg_replace_callback('/\{\{\s*([\w_]+)\s*\}\}/', function($matches) use ($variables) {
            $key = $matches[1];
            return $variables[$key] ?? $matches[0];
        }, $template);
    }

    public function askWithTemplate(string $templateName, array $variables, ?array $schema = null): array
    {
        $template = $this->loadPromptTemplate($templateName);
        $prompt = $this->processTemplate($template, $variables);
        return $this->ask($prompt, $schema);
    }
}