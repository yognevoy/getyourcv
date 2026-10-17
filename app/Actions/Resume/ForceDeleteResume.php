<?php

namespace App\Actions\Resume;

use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use App\Services\Photo\ResumePhotoStore;

class ForceDeleteResume
{
    public function __construct(
        private readonly ResumePdfStore $pdfStore,
        private readonly ResumePhotoStore $photoStore,
    ) {}

    public function execute(Resume $resume): void
    {
        $photoPath = $resume->photo_path;

        $resume->forceDelete();
        $this->pdfStore->forget($resume);
        $this->photoStore->delete($photoPath);
    }
}
