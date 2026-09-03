<?php

namespace App\Services\Ai\Match;

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\ToolSchema;

class MatchResultSchema implements ToolSchema
{
    private const TOOL_NAME = 'submit_vacancy_match';

    public function toolName(): string
    {
        return self::TOOL_NAME;
    }

    public function toolDefinition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => self::TOOL_NAME,
                'description' => 'Submit the match assessment between a resume and a vacancy.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'score' => [
                            'type' => 'integer',
                            'description' => 'Overall match score from 0 to 100.',
                        ],
                        'matched_skills' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Key skills the vacancy asks for that the resume already shows.',
                        ],
                        'missing_skills' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Key skills the vacancy asks for that the resume does not show.',
                        ],
                        'summary' => [
                            'type' => 'string',
                            'description' => 'A one to two sentence assessment of the fit.',
                        ],
                    ],
                    'required' => ['score', 'matched_skills', 'missing_skills', 'summary'],
                ],
            ],
        ];
    }

    /**
     * @return array{score: int, matched_skills: list<string>, missing_skills: list<string>, summary: string}
     */
    public function parse(mixed $rawArguments): array
    {
        if (! is_string($rawArguments)) {
            throw new AiServiceException('AI service returned an unexpected response.');
        }

        $data = json_decode($rawArguments, true);

        if (
            ! is_array($data)
            || ! array_key_exists('score', $data)
            || ! array_key_exists('summary', $data)
            || ! is_array($data['matched_skills'] ?? null)
            || ! is_array($data['missing_skills'] ?? null)
        ) {
            throw new AiServiceException('AI service returned an unexpected response.');
        }

        return [
            'score' => max(0, min(100, (int) $data['score'])),
            'matched_skills' => array_values(array_map('strval', $data['matched_skills'])),
            'missing_skills' => array_values(array_map('strval', $data['missing_skills'])),
            'summary' => (string) $data['summary'],
        ];
    }
}
