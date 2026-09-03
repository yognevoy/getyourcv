<?php

namespace App\Actions\Resume\Match;

use App\Models\Resume;
use App\Models\ResumeVacancyMatch;
use App\Services\Ai\Match\MatchServiceInterface;
use App\Services\Ai\Match\ResumeSummaryFormatter;

/**
 * Matches a resume's current data against a vacancy.
 */
class MatchResumeToVacancy
{
    public function __construct(
        private readonly MatchServiceInterface $ai,
        private readonly ResumeSummaryFormatter $formatter = new ResumeSummaryFormatter,
    ) {}

    public function execute(Resume $resume, ?string $vacancyTitle, string $vacancyText): ResumeVacancyMatch
    {
        $hash = hash('sha256', $vacancyText);

        $existing = $resume->vacancyMatches()
            ->where('resume_version_id', $resume->current_version_id)
            ->where('vacancy_text_hash', $hash)
            ->first();

        if ($existing) {
            return $existing;
        }

        $result = $this->ai->match(
            $this->formatter->format($resume),
            $vacancyText,
        );

        return $resume->vacancyMatches()->create([
            'resume_version_id' => $resume->current_version_id,
            'vacancy_title' => $vacancyTitle,
            'vacancy_text' => $vacancyText,
            'vacancy_text_hash' => $hash,
            'score' => $result['score'],
            'matched_skills' => $result['matched_skills'],
            'missing_skills' => $result['missing_skills'],
            'summary' => $result['summary'],
        ]);
    }
}
