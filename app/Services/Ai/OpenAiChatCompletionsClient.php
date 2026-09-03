<?php

namespace App\Services\Ai;

use App\Services\Ai\Exceptions\AiServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * Transport for OpenAI-compatible chat/completions forced tool calls.
 */
class OpenAiChatCompletionsClient
{
    private const CHAT_COMPLETIONS_PATH = '/chat/completions';

    private const TOOL_CALL_ARGUMENTS_PATH = 'choices.0.message.tool_calls.0.function.arguments';

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    /**
     * @throws AiServiceException
     */
    public function send(ChatPrompt $prompt, ToolSchema $schema, int $timeout): mixed
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($timeout)
                ->post(rtrim($this->baseUrl, '/').self::CHAT_COMPLETIONS_PATH, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $prompt->system],
                        ['role' => 'user', 'content' => $prompt->user],
                    ],
                    'tools' => [$schema->toolDefinition()],
                    'tool_choice' => ['type' => 'function', 'function' => ['name' => $schema->toolName()]],
                ]);
        } catch (ConnectionException $e) {
            throw new AiServiceException('AI service is unreachable. Please try again later.', previous: $e);
        }

        if ($response->failed()) {
            if ($response->status() === Response::HTTP_TOO_MANY_REQUESTS) {
                throw new AiServiceException('AI service rate limit reached. Please try again in a moment.');
            }

            if ($response->status() === Response::HTTP_REQUEST_TIMEOUT) {
                throw new AiServiceException('AI service timed out. Please try again.');
            }

            throw new AiServiceException('AI service request failed. Please try again later.');
        }

        return $response->json(self::TOOL_CALL_ARGUMENTS_PATH);
    }
}
