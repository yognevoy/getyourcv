<?php

namespace App\Services\Pdf\Mapper;

class SkillsMapper
{
    public function map(array $data): ?array
    {
        $rows = [];

        foreach ($data['skill_groups'] ?? [] as $group) {
            $values = collect($group['skills'] ?? [])->pluck('value')->filter()->implode(', ');

            if (empty($group['label']) && $values === '') {
                continue;
            }

            $rows[] = ['label' => (string) ($group['label'] ?? ''), 'value' => $values];
        }

        if ($rows === []) {
            return null;
        }

        return ['title' => 'Skills', 'skills' => $rows];
    }
}
