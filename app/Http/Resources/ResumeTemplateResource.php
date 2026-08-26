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
            'full_name' => $this->full_name,
            'position' => $this->position,
            'email' => $this->email,
            'about' => $this->about,
            'links' => ResumeLinkResource::collection($this->links),
            'skill_groups' => ResumeSkillGroupResource::collection($this->skillGroups),
            'experiences' => ResumeExperienceResource::collection($this->experiences),
        ];
    }
}
