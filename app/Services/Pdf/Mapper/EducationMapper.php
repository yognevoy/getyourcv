<?php

namespace App\Services\Pdf\Mapper;

use App\Services\Pdf\Formatter\PeriodFormatter;

class EducationMapper
{
    public function map(array $data): ?array
    {
        $entries = [];
        $periodFormatter = new PeriodFormatter;

        foreach ($data['educations'] ?? [] as $education) {
            if (empty($education['institution'])) {
                continue;
            }

            $entries[] = [
                'title' => $education['institution'],
                'period' => $periodFormatter->format($education['period_from'] ?? null, $education['period_to'] ?? null, false),
                'lines' => ! empty($education['field']) ? [$education['field']] : [],
            ];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Education', 'entries' => $entries];
    }
}
