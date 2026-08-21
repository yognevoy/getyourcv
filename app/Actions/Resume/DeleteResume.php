<?php

namespace App\Actions\Resume;

use App\Models\Resume;

class DeleteResume
{
    public function execute(Resume $resume): void
    {
        $resume->delete();
    }
}
