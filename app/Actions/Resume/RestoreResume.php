<?php

namespace App\Actions\Resume;

use App\Models\Resume;

class RestoreResume
{
    public function execute(Resume $resume): void
    {
        $resume->restore();
    }
}
