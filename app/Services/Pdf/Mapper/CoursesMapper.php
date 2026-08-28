<?php

namespace App\Services\Pdf\Mapper;

class CoursesMapper
{
    public function map(array $data): ?array
    {
        $entries = [];

        foreach ($data['courses'] ?? [] as $course) {
            if (empty($course['title'])) {
                continue;
            }

            $entries[] = ['title' => $course['title'], 'subtitle' => (string) ($course['provider'] ?? '')];
        }

        if ($entries === []) {
            return null;
        }

        return ['title' => 'Courses', 'entries' => $entries];
    }
}
