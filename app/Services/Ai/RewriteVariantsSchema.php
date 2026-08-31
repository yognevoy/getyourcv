<?php

namespace App\Services\Ai;

use App\Services\Ai\Exceptions\AiServiceException;

class RewriteVariantsSchema
{
    private const TOOL_NAME = 'submit_rewrite_variants';

    public function toolName(): string
    {
        return self::TOOL_NAME;
    }

    public function toolDefinition(): array
    {
        $properties = [];

        foreach (RewriteVariant::cases() as $variant) {
            $properties[$variant->value] = ['type' => 'string', 'description' => $variant->description()];
        }

        return [
            'type' => 'function',
            'function' => [
                'name' => self::TOOL_NAME,
                'description' => 'Submit three rewritten variants of the given resume text.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => array_column(RewriteVariant::cases(), 'value'),
                ],
            ],
        ];
    }

    /**
     * @return array{shorter: string, stronger: string, with_numbers: string}
     */
    public function parse(mixed $rawArguments): array
    {
        if (! is_string($rawArguments)) {
            throw new AiServiceException('AI service returned an unexpected response.');
        }

        $variants = json_decode($rawArguments, true);

        if (! is_array($variants)) {
            throw new AiServiceException('AI service returned an unexpected response.');
        }

        $result = [];

        foreach (RewriteVariant::cases() as $variant) {
            if (! array_key_exists($variant->value, $variants)) {
                throw new AiServiceException('AI service returned an unexpected response.');
            }

            $result[$variant->value] = (string) $variants[$variant->value];
        }

        return $result;
    }
}
