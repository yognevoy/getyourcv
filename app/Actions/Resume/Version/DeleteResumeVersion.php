<?php

namespace App\Actions\Resume\Version;

use App\Models\ResumeVersion;

class DeleteResumeVersion
{
    public function execute(ResumeVersion $version): void
    {
        abort_if(
            $version->id === $version->resume->current_version_id,
            422,
            'Cannot delete the current version.'
        );

        $version->delete();
    }
}
