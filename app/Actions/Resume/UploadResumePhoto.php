<?php

namespace App\Actions\Resume;

use App\Services\Photo\ResumePhotoStore;
use Illuminate\Http\UploadedFile;

class UploadResumePhoto
{
    public function __construct(private readonly ResumePhotoStore $photoStore) {}

    public function execute(UploadedFile $file): string
    {
        return $this->photoStore->store($file);
    }
}
