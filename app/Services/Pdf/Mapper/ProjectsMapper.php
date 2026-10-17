<?php

namespace App\Services\Pdf\Mapper;

class ProjectsMapper
{
    public function map(array $data): ?array
    {
        $entries = [];

        foreach ($data['projects'] ?? [] as $project) {
            if (empty($project['title'])) {
                continue;
            }

            $entries[] = ['title' => $project['title'], 'description' => (string) ($project['description'] ?? '')];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Projects & Contributions', 'entries' => $entries];
    }
}
