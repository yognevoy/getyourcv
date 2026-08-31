<?php

namespace App\Services\Ai;

use App\Services\Ai\Exceptions\AiServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * OpenAI-compatible chat/completions client.
 */
class OpenAiCompatibleAiService implements AiServiceInterface
{
    private const CHAT_COMPLETIONS_PATH = '/chat/completions';

    private const TOOL_CALL_ARGUMENTS_PATH = 'choices.0.message.tool_calls.0.function.arguments';

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly RewritePromptBuilder $builder = new RewritePromptBuilder(),
        private readonly RewriteVariantsSchema $schema = new RewriteVariantsSchema(),
    ) {
    }

    public function rewrite(string $text, RewriteTarget $target): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post(rtrim($this->baseUrl, '/').self::CHAT_COMPLETIONS_PATH, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->builder->build($target, $this->schema->toolName())],
                        ['role' => 'user', 'content' => $text],
                    ],
                    'tools' => [$this->schema->toolDefinition()],
                    'tool_choice' => ['type' => 'function', 'function' => ['name' => $this->schema->toolName()]],
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

        return $this->schema->parse($response->json(self::TOOL_CALL_ARGUMENTS_PATH));
    }
}
