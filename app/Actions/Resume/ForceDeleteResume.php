<?php

namespace App\Actions\Resume;

use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;

class ForceDeleteResume
{
    public function __construct(private readonly ResumePdfStore $pdfStore) {}

    public function execute(Resume $resume): void
    {
        $resume->forceDelete();
        $this->pdfStore->forget($resume);
    }
}
