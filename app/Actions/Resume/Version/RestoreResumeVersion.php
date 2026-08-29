<?php

namespace App\Actions\Resume\Version;

use App\Actions\Resume\Concerns\PersistsResumeRelations;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Services\Pdf\ResumePdfStore;
use Illuminate\Support\Facades\DB;

/**
 * Makes a past version current.
 */
class RestoreResumeVersion
{
    use PersistsResumeRelations;

    public function __construct(private readonly ResumePdfStore $pdfStore) {}

    public function execute(Resume $resume, ResumeVersion $version): Resume
    {
        $data = $version->snapshot;

        $resume = DB::transaction(function () use ($resume, $version, $data) {
            $resume->update([
                'title' => $data['title'],
                'full_name' => $data['full_name'],
                'position' => $data['position'] ?? null,
                'email' => $data['email'] ?? null,
                'about' => $data['about'] ?? null,
                'current_version_id' => $version->id,
            ]);

            $this->persistRelations($resume, $data);

            return $resume->fresh(self::RESUME_RELATIONS);
        });

        $this->pdfStore->store($resume);

        return $resume;
    }
}
