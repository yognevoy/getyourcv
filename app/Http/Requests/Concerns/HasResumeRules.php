<?php

namespace App\Http\Requests\Concerns;

use App\Enums\ResumeStatus;
use App\Rules\ResumePhotoRule;
use Illuminate\Validation\Rule;

trait HasResumeRules
{
    private function resumeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ResumeStatus::class)],
            'full_name' => ['required', 'string', 'max:28'],
            'position' => ['nullable', 'string', 'max:28'],
            'location' => ['nullable', 'string', 'max:28'],
            'email' => ['nullable', 'email', 'max:36'],
            'about' => ['nullable', 'string', 'max:2000'],
            'photo_path' => ['nullable', 'string', new ResumePhotoRule],

            'links' => ['array', 'max:3'],
            'links.*.label' => ['required', 'string', 'max:20'],
            'links.*.url' => ['required', 'url', 'max:255'],

            'skill_groups' => ['array'],
            'skill_groups.*.label' => ['required', 'string', 'max:50'],
            'skill_groups.*.skills' => ['array'],
            'skill_groups.*.skills.*.value' => ['required', 'string', 'max:50'],

            'experiences' => ['array'],
            'experiences.*.company' => ['required', 'string', 'max:50'],
            'experiences.*.title' => ['required', 'string', 'max:50'],
            'experiences.*.period_from' => ['nullable', 'date'],
            'experiences.*.period_to' => ['nullable', 'date'],
            'experiences.*.is_current' => ['boolean'],
            'experiences.*.bullets' => ['array'],
            'experiences.*.bullets.*.type' => ['required', 'in:responsibility,achievement'],
            'experiences.*.bullets.*.text' => ['required', 'string', 'max:500'],

            'projects' => ['array'],
            'projects.*.title' => ['required', 'string', 'max:50'],
            'projects.*.description' => ['nullable', 'string', 'max:300'],

            'educations' => ['array'],
            'educations.*.institution' => ['required', 'string', 'max:50'],
            'educations.*.field' => ['nullable', 'string', 'max:70'],
            'educations.*.period_from' => ['nullable', 'date'],
            'educations.*.period_to' => ['nullable', 'date'],
            'educations.*.is_current' => ['boolean'],

            'courses' => ['array'],
            'courses.*.title' => ['required', 'string', 'max:50'],
            'courses.*.provider' => ['nullable', 'string', 'max:70'],

            'certifications' => ['array'],
            'certifications.*.title' => ['required', 'string', 'max:50'],
            'certifications.*.provider' => ['nullable', 'string', 'max:70'],

            'languages' => ['array'],
            'languages.*.language' => ['required', 'string', 'max:50'],
            'languages.*.level' => ['nullable', 'string', 'max:50'],
        ];
    }
}
