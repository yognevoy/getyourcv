<?php

namespace App\Actions\Resume;

use App\Actions\Resume\Concerns\GeneratesResumeSlug;
use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Enums\ResumeStatus;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateResume
{
    use GeneratesResumeSlug, PersistsResumeRelations;

    public function execute(?User $user, array $data): Resume
    {
        return DB::transaction(function () use ($user, $data) {
            $resume = Resume::create([
                'user_id' => $user?->id,
                'slug' => $this->generateSlug($data['title']),
                'title' => $data['title'],
                'status' => ResumeStatus::Draft,
                'full_name' => $data['full_name'],
                'position' => $data['position'] ?? null,
                'email' => $data['email'] ?? null,
                'about' => $data['about'] ?? null,
            ]);

            $this->persistRelations($resume, $data);

            return $resume->fresh(self::RESUME_RELATIONS);
        });
    }
}
