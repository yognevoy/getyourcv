<?php

namespace App\Actions\Resume;

use App\Actions\Resume\Concerns\GeneratesResumeSlug;
use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Enums\ResumeStatus;
use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use Illuminate\Support\Facades\DB;

class DuplicateResume
{
    use GeneratesResumeSlug, PersistsResumeRelations;

    public function __construct(private readonly ResumePdfStore $pdfStore) {}

    public function execute(Resume $resume, ?string $title = null): Resume
    {
        $resume->loadMissing(self::RESUME_RELATIONS);

        $clone = DB::transaction(function () use ($resume, $title) {
            $clone = Resume::create([
                'user_id' => $resume->user_id,
                'slug' => $this->generateSlug($resume->title),
                'title' => $title ?? "Copy of {$resume->title}",
                'status' => ResumeStatus::Draft,
                'photo_path' => $resume->photo_path,
                'full_name' => $resume->full_name,
                'position' => $resume->position,
                'email' => $resume->email,
                'about' => $resume->about,
            ]);

            $this->persistRelations($clone, [
                'links' => $resume->links->map(fn ($link) => [
                    'label' => $link->label,
                    'url' => $link->url,
                ])->all(),
                'skill_groups' => $resume->skillGroups->map(fn ($group) => [
                    'label' => $group->label,
                    'skills' => $group->skills->map(fn ($skill) => [
                        'value' => $skill->value,
                    ])->all(),
                ])->all(),
                'experiences' => $resume->experiences->map(fn ($experience) => [
                    'company' => $experience->company,
                    'title' => $experience->title,
                    'period_from' => $experience->period_from,
                    'period_to' => $experience->period_to,
                    'is_current' => $experience->is_current,
                    'bullets' => $experience->bullets->map(fn ($bullet) => [
                        'type' => $bullet->type->value,
                        'text' => $bullet->text,
                    ])->all(),
                ])->all(),
                'educations' => $resume->educations->map(fn ($education) => [
                    'institution' => $education->institution,
                    'field' => $education->field,
                    'period_from' => $education->period_from,
                    'period_to' => $education->period_to,
                ])->all(),
                'courses' => $resume->courses->map(fn ($course) => [
                    'title' => $course->title,
                    'provider' => $course->provider,
                ])->all(),
                'certifications' => $resume->certifications->map(fn ($certification) => [
                    'title' => $certification->title,
                    'provider' => $certification->provider,
                ])->all(),
            ]);

            return $clone->fresh(self::RESUME_RELATIONS);
        });

        $this->pdfStore->store($clone);

        return $clone;
    }
}
