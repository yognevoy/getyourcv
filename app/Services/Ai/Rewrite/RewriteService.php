<?php

namespace App\Services\Ai\Rewrite;

use App\Services\Ai\ChatPrompt;
use App\Services\Ai\OpenAiChatCompletionsClient;

class RewriteService implements RewriteServiceInterface
{
    private const REQUEST_TIMEOUT = 20;

    public function __construct(
        private readonly OpenAiChatCompletionsClient $client,
        private readonly RewritePromptBuilder $builder = new RewritePromptBuilder,
        private readonly RewriteVariantsSchema $schema = new RewriteVariantsSchema,
    ) {}

    public function rewrite(string $text, RewriteTarget $target): array
    {
        $prompt = new ChatPrompt(
            system: $this->builder->build($target, $this->schema->toolName()),
            user: $text,
        );

        $arguments = $this->client->send($prompt, $this->schema, timeout: self::REQUEST_TIMEOUT);

        return $this->schema->parse($arguments);
    }
}
