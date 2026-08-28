<?php

namespace App\Services\Pdf\Mapper;

use App\Models\Resume;

/**
 * Assembles mappers into the JSON payload resume-gen expects.
 */
class ResumeMapper
{
    public function __construct(
        private readonly ContactsMapper $contacts = new ContactsMapper,
        private readonly AboutMapper $about = new AboutMapper,
        private readonly SkillsMapper $skills = new SkillsMapper,
        private readonly ExperienceMapper $experience = new ExperienceMapper,
        private readonly EducationMapper $education = new EducationMapper,
        private readonly CoursesMapper $courses = new CoursesMapper,
        private readonly CertificationsMapper $certifications = new CertificationsMapper,
    ) {}

    public function mapFromResume(Resume $resume): array
    {
        $links = $resume->links->map(fn ($link) => [
            'label' => $link->label,
            'url' => $link->url,
        ])->all();

        $skillGroups = $resume->skillGroups->map(fn ($group) => [
            'label' => $group->label,
            'skills' => $group->skills->map(fn ($skill) => ['value' => $skill->value])->all(),
        ])->all();

        $experiences = $resume->experiences->map(fn ($experience) => [
            'company' => $experience->company,
            'title' => $experience->title,
            'period_from' => $experience->period_from?->toDateString(),
            'period_to' => $experience->period_to?->toDateString(),
            'is_current' => $experience->is_current,
            'bullets' => $experience->bullets->map(fn ($bullet) => [
                'type' => $bullet->type->value,
                'text' => $bullet->text,
            ])->all(),
        ])->all();

        $educations = $resume->educations->map(fn ($education) => [
            'institution' => $education->institution,
            'field' => $education->field,
            'period_from' => $education->period_from?->toDateString(),
            'period_to' => $education->period_to?->toDateString(),
        ])->all();

        $courses = $resume->courses->map(fn ($course) => [
            'title' => $course->title,
            'provider' => $course->provider,
        ])->all();

        $certifications = $resume->certifications->map(fn ($certification) => [
            'title' => $certification->title,
            'provider' => $certification->provider,
        ])->all();

        return $this->mapFromArray([
            'full_name' => $resume->full_name,
            'position' => $resume->position,
            'email' => $resume->email,
            'about' => $resume->about,
            'links' => $links,
            'skill_groups' => $skillGroups,
            'experiences' => $experiences,
            'educations' => $educations,
            'courses' => $courses,
            'certifications' => $certifications,
        ]);
    }

    public function mapFromArray(array $data): array
    {
        return [
            'name' => (string) ($data['full_name'] ?? ''),
            'position' => (string) ($data['position'] ?? ''),
            'contacts' => $this->contacts->map($data),
            'sections' => (object) array_filter([
                'about' => $this->about->map($data),
                'skills' => $this->skills->map($data),
                'experience' => $this->experience->map($data),
                'education' => $this->education->map($data),
                'courses' => $this->courses->map($data),
                'certifications' => $this->certifications->map($data),
            ]),
        ];
    }
}
