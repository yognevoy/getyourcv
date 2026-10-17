<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes a Resume into the structure expected by resources/js/Shared/ResumeTemplate.vue.
 */
class ResumeTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'archived' => $this->archived_at !== null,
            'full_name' => $this->full_name,
            'position' => $this->position,
            'location' => $this->location,
            'email' => $this->email,
            'about' => $this->about,
            'photo_path' => $this->photo_path,
            'links' => ResumeLinkResource::collection($this->links),
            'skill_groups' => ResumeSkillGroupResource::collection($this->skillGroups),
            'experiences' => ResumeExperienceResource::collection($this->experiences),
            'projects' => ResumeProjectResource::collection($this->projects),
            'educations' => ResumeEducationResource::collection($this->educations),
            'courses' => ResumeCourseResource::collection($this->courses),
            'certifications' => ResumeCertificationResource::collection($this->certifications),
        ];
    }
}
