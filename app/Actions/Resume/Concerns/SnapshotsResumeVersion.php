<?php

namespace App\Actions\Resume\Concerns;

use App\Models\Resume;
use App\Models\ResumeVersion;

/**
 * Records the resume's current form data as a new version and makes it current.
 */
trait SnapshotsResumeVersion
{
    private function recordVersion(Resume $resume, array $data): ResumeVersion
    {
        $version = $resume->versions()->create([
            'snapshot' => $data,
        ]);

        $resume->update(['current_version_id' => $version->id]);

        return $version;
    }
}
