<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeEducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'institution' => $this->institution,
            'field' => $this->field,
            'period_from' => $this->period_from?->toDateString(),
            'period_to' => $this->period_to?->toDateString(),
            'is_current' => $this->is_current,
        ];
    }
}
