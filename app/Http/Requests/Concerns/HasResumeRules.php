<?php

namespace App\Http\Requests\Concerns;

trait HasResumeRules
{
    private function resumeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'about' => ['nullable', 'string'],

            'links' => ['array'],
            'links.*.label' => ['required', 'string', 'max:255'],
            'links.*.url' => ['required', 'url', 'max:2048'],

            'skill_groups' => ['array'],
            'skill_groups.*.label' => ['required', 'string', 'max:255'],
            'skill_groups.*.skills' => ['array'],
            'skill_groups.*.skills.*.value' => ['required', 'string', 'max:255'],

            'experiences' => ['array'],
            'experiences.*.company' => ['required', 'string', 'max:255'],
            'experiences.*.title' => ['required', 'string', 'max:255'],
            'experiences.*.period_from' => ['nullable', 'date'],
            'experiences.*.period_to' => ['nullable', 'date'],
            'experiences.*.is_current' => ['boolean'],
            'experiences.*.bullets' => ['array'],
            'experiences.*.bullets.*.type' => ['required', 'in:responsibility,achievement'],
            'experiences.*.bullets.*.text' => ['required', 'string'],

            'educations' => ['array'],
            'educations.*.institution' => ['required', 'string', 'max:255'],
            'educations.*.field' => ['nullable', 'string', 'max:255'],
            'educations.*.period_from' => ['nullable', 'date'],
            'educations.*.period_to' => ['nullable', 'date'],

            'courses' => ['array'],
            'courses.*.title' => ['required', 'string', 'max:255'],
            'courses.*.provider' => ['nullable', 'string', 'max:255'],

            'certifications' => ['array'],
            'certifications.*.title' => ['required', 'string', 'max:255'],
            'certifications.*.provider' => ['nullable', 'string', 'max:255'],
        ];
    }
}
