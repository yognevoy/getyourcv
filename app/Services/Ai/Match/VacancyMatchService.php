<?php

namespace App\Services\Ai\Match;

use App\Services\Ai\ChatPrompt;
use App\Services\Ai\OpenAiChatCompletionsClient;

class VacancyMatchService implements MatchServiceInterface
{
    private const REQUEST_TIMEOUT = 45;

    public function __construct(
        private readonly OpenAiChatCompletionsClient $client,
        private readonly MatchPromptBuilder $builder = new MatchPromptBuilder,
        private readonly MatchResultSchema $schema = new MatchResultSchema,
    ) {}

    public function match(string $resumeSummary, string $vacancyText): array
    {
        $prompt = new ChatPrompt(
            system: $this->builder->build($resumeSummary, $this->schema->toolName()),
            user: $vacancyText,
        );

        $arguments = $this->client->send($prompt, $this->schema, timeout: self::REQUEST_TIMEOUT);

        return $this->schema->parse($arguments);
    }
}
