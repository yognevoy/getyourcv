<?php

namespace App\Services\Ai\Match;

use App\Services\Ai\Exceptions\AiServiceException;

interface MatchServiceInterface
{
    /**
     * Matches a resume summary against a vacancy.
     *
     * @return array{score: int, matched_skills: list<string>, missing_skills: list<string>, summary: string}
     *
     * @throws AiServiceException
     */
    public function match(string $resumeSummary, string $vacancyText): array;
}
