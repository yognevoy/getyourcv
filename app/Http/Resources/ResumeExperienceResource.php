<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'company' => $this->company,
            'title' => $this->title,
            'period_from' => $this->period_from?->toDateString(),
            'period_to' => $this->period_to?->toDateString(),
            'is_current' => $this->is_current,
            'bullets' => ResumeExperienceBulletResource::collection($this->bullets),
        ];
    }
}
