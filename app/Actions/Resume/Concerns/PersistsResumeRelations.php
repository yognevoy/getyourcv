<?php

namespace App\Actions\Resume\Concerns;

use App\Models\Resume;

/**
 * Full replace strategy: existing rows are deleted and rebuilt from the
 * submitted arrays. Simpler than diffing and fine for resume-sized data.
 */
trait PersistsResumeRelations
{
    private function persistRelations(Resume $resume, array $data): void
    {
        $resume->links()->delete();
        foreach ($data['links'] ?? [] as $i => $link) {
            $resume->links()->create([
                'label' => $link['label'],
                'url' => $link['url'],
                'position' => $i,
            ]);
        }

        $resume->skillGroups()->delete();
        foreach ($data['skill_groups'] ?? [] as $i => $group) {
            $skillGroup = $resume->skillGroups()->create([
                'label' => $group['label'],
                'position' => $i,
            ]);

            foreach ($group['skills'] ?? [] as $j => $skill) {
                $skillGroup->skills()->create([
                    'value' => $skill['value'],
                    'position' => $j,
                ]);
            }
        }

        $resume->experiences()->delete();
        foreach ($data['experiences'] ?? [] as $i => $experience) {
            $resumeExperience = $resume->experiences()->create([
                'company' => $experience['company'],
                'title' => $experience['title'],
                'period_from' => $experience['period_from'] ?? null,
                'period_to' => $experience['period_to'] ?? null,
                'is_current' => $experience['is_current'] ?? false,
                'position' => $i,
            ]);

            foreach ($experience['bullets'] ?? [] as $j => $bullet) {
                $resumeExperience->bullets()->create([
                    'type' => $bullet['type'],
                    'text' => $bullet['text'],
                    'position' => $j,
                ]);
            }
        }

        $resume->educations()->delete();
        foreach ($data['educations'] ?? [] as $i => $education) {
            $resume->educations()->create([
                'institution' => $education['institution'],
                'field' => $education['field'] ?? null,
                'period_from' => $education['period_from'] ?? null,
                'period_to' => $education['period_to'] ?? null,
                'position' => $i,
            ]);
        }

        $resume->courses()->delete();
        foreach ($data['courses'] ?? [] as $i => $course) {
            $resume->courses()->create([
                'title' => $course['title'],
                'provider' => $course['provider'] ?? null,
                'position' => $i,
            ]);
        }

        $resume->certifications()->delete();
        foreach ($data['certifications'] ?? [] as $i => $certification) {
            $resume->certifications()->create([
                'title' => $certification['title'],
                'provider' => $certification['provider'] ?? null,
                'position' => $i,
            ]);
        }
    }

    public const RESUME_RELATIONS = [
        'links',
        'skillGroups.skills',
        'experiences.bullets',
        'educations',
        'courses',
        'certifications',
    ];
}
