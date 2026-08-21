<?php

namespace App\Actions\Resume\Concerns;

use App\Models\Resume;
use Illuminate\Support\Str;

trait GeneratesResumeSlug
{
    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'resume';

        do {
            $slug = $base.'-'.Str::lower(Str::random(6));
        } while (Resume::withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }
}
