<?php

namespace App\Actions\Resume;

use App\Models\Resume;

class ForceDeleteResume
{
    public function execute(Resume $resume): void
    {
        $resume->forceDelete();
    }
}
