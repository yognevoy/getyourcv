<?php

namespace App\Actions\Resume;

use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Models\Resume;
use Illuminate\Support\Facades\DB;

class UpdateResume
{
    use PersistsResumeRelations;

    public function execute(Resume $resume, array $data): Resume
    {
        return DB::transaction(function () use ($resume, $data) {
            $resume->update([
                'title' => $data['title'],
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
