<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeLanguageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'language' => $this->language,
            'level' => $this->level,
        ];
    }
}
