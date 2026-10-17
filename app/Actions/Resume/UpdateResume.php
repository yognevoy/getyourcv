<?php

namespace App\Actions\Resume;

use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Actions\Resume\Concerns\SnapshotsResumeVersion;
use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use App\Services\Photo\ResumePhotoStore;
use Illuminate\Support\Facades\DB;

class UpdateResume
{
    use PersistsResumeRelations, SnapshotsResumeVersion;

    public function __construct(
        private readonly ResumePdfStore $pdfStore,
        private readonly ResumePhotoStore $photoStore,
    ) {}

    public function execute(Resume $resume, array $data): Resume
    {
        $previousPhotoPath = $resume->photo_path;

        $resume = DB::transaction(function () use ($resume, $data) {
            $resume->update([
                'title' => $data['title'],
                'status' => $data['status'],
                'full_name' => $data['full_name'],
                'position' => $data['position'] ?? null,
                'location' => $data['location'] ?? null,
                'email' => $data['email'] ?? null,
                'about' => $data['about'] ?? null,
                'photo_path' => $data['photo_path'] ?? null,
            ]);

            $this->persistRelations($resume, $data);
            $this->recordVersion($resume, $data);

            return $resume->fresh(self::RESUME_RELATIONS);
        });

        if ($previousPhotoPath && $previousPhotoPath !== $resume->photo_path) {
            $this->photoStore->delete($previousPhotoPath);
        }

        $this->pdfStore->store($resume);

        return $resume;
    }
}
