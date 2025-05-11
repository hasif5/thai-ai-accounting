<?php

namespace Tests\Unit\AI;

use App\AI\AssistantService;
use Illuminate\Support\Facades\Config;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateResponseChoice;
use OpenAI\Responses\Chat\CreateResponseMessage;
use PHPUnit\Framework\TestCase;

class AssistantServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::shouldReceive('get')
            ->with('ai.openai')
            ->andReturn([
                'api_key' => 'test-key',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);
    }

    public function test_ask_returns_decoded_json_response()
    {
        $mockResponse = $this->createMockResponse(['test' => 'value']);
        OpenAI::shouldReceive('chat->create')->once()->andReturn($mockResponse);

        $assistant = new AssistantService();
        $result = $assistant->ask('Test prompt');

        $this->assertEquals(['test' => 'value'], $result);
    }

    public function test_ask_validates_schema_successfully()
    {
        $mockResponse = $this->createMockResponse(['name' => 'Test']);
        OpenAI::shouldReceive('chat->create')->once()->andReturn($mockResponse);

        $schema = ['name' => 'required|string'];

        $assistant = new AssistantService();
        $result = $assistant->ask('Test prompt', $schema);

        $this->assertEquals(['name' => 'Test'], $result);
    }

    public function test_ask_throws_exception_for_invalid_schema()
    {
        $mockResponse = $this->createMockResponse(['age' => 'invalid']);
        OpenAI::shouldReceive('chat->create')->once()->andReturn($mockResponse);

        $schema = ['age' => 'required|integer'];

        $this->expectException(\InvalidArgumentException::class);

        $assistant = new AssistantService();
        $assistant->ask('Test prompt', $schema);
    }

    public function test_constructor_throws_exception_when_api_key_not_configured()
    {
        Config::shouldReceive('get')
            ->with('ai.openai')
            ->andReturn(['api_key' => '']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAI API key is not configured');

        new AssistantService();
    }

    protected function createMockResponse(array $content): CreateResponse
    {
        $message = new CreateResponseMessage(['content' => json_encode($content)]);
        $choice = new CreateResponseChoice(['message' => $message]);
        return new CreateResponse(['choices' => [$choice]]);
    }
}