<?php

namespace App\Actions\Resume\Concerns;

use App\Models\Resume;
use Illuminate\Support\Str;

trait GeneratesResumeSlug
{
    private function generateSlug(string $title): string
    {
        $base = Str::substr(Str::slug($title) ?: 'resume', 0, 248);

        do {
            $slug = $base.'-'.Str::lower(Str::random(6));
        } while (Resume::withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }
}
