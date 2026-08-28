<?php

namespace App\Services\Pdf\Mapper;

use App\Enums\ExperienceBulletType;
use App\Services\Pdf\Formatter\PeriodFormatter;

class ExperienceMapper
{
    public function map(array $data): ?array
    {
        $entries = [];
        $periodFormatter = new PeriodFormatter;

        foreach ($data['experiences'] ?? [] as $experience) {
            $groups = $this->mapBulletGroups($experience['bullets'] ?? []);

            // Skip a row the moment "Add experience" is clicked, before anything is typed into
            // it - otherwise the live preview would show an empty Company/Title block.
            if (empty($experience['company']) && empty($experience['title']) && $groups === []) {
                continue;
            }

            $entries[] = [
                'company' => (string) ($experience['company'] ?? ''),
                'title' => (string) ($experience['title'] ?? ''),
                'period' => $periodFormatter->format(
                    $experience['period_from'] ?? null,
                    $experience['period_to'] ?? null,
                    (bool) ($experience['is_current'] ?? false),
                ),
                'groups' => $groups,
                'bullets' => [],
            ];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Experience', 'entries' => $entries];
    }

    private function mapBulletGroups(array $bullets): array
    {
        $groups = [];

        $byType = fn (string $type) => array_values(array_map(
            fn (array $bullet) => $bullet['text'],
            array_filter(
                $bullets,
                fn (array $bullet) => ($bullet['type'] ?? null) === $type && ! empty($bullet['text']),
            ),
        ));

        $responsibilities = $byType(ExperienceBulletType::Responsibility->value);
        if ($responsibilities !== []) {
            $groups[] = ['label' => 'Responsibilities', 'bullets' => $responsibilities];
        }

        $achievements = $byType(ExperienceBulletType::Achievement->value);
        if ($achievements !== []) {
            $groups[] = ['label' => 'Achievements', 'bullets' => $achievements];
        }

        return $groups;
    }
}
