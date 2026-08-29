<?php

namespace App\Actions\Resume;

use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Actions\Resume\Concerns\SnapshotsResumeVersion;
use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use Illuminate\Support\Facades\DB;

class UpdateResume
{
    use PersistsResumeRelations, SnapshotsResumeVersion;

    public function __construct(private readonly ResumePdfStore $pdfStore) {}

    public function execute(Resume $resume, array $data): Resume
    {
        $resume = DB::transaction(function () use ($resume, $data) {
            $resume->update([
                'title' => $data['title'],
                'full_name' => $data['full_name'],
                'position' => $data['position'] ?? null,
                'email' => $data['email'] ?? null,
                'about' => $data['about'] ?? null,
            ]);

            $this->persistRelations($resume, $data);
            $this->recordVersion($resume, $data);

            return $resume->fresh(self::RESUME_RELATIONS);
        });

        $this->pdfStore->store($resume);

        return $resume;
    }
}
