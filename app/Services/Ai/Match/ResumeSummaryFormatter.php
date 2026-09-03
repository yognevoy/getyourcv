<?php

namespace App\Services\Ai\Match;

use App\Models\Resume;

class ResumeSummaryFormatter
{
    public function format(Resume $resume): string
    {
        $resume->loadMissing(['skillGroups.skills', 'experiences.bullets']);

        $lines = [];

        if ($resume->position) {
            $lines[] = "Target role: {$resume->position}";
        }

        if ($resume->about) {
            $lines[] = "About: {$resume->about}";
        }

        $skills = $resume->skillGroups
            ->flatMap(fn ($group) => $group->skills->pluck('value'))
            ->filter();

        if ($skills->isNotEmpty()) {
            $lines[] = 'Skills: '.$skills->implode(', ');
        }

        foreach ($resume->experiences as $experience) {
            $bullets = $experience->bullets->pluck('text')->filter()->implode('; ');

            if ($bullets !== '') {
                $lines[] = trim("{$experience->title} at {$experience->company}: {$bullets}");
            }
        }

        return implode("\n", $lines);
    }
}
