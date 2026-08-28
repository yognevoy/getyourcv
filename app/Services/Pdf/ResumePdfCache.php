<?php

namespace App\Services\Pdf;

use App\Actions\Resume\UpdateResume;
use App\Models\Resume;
use App\Services\Pdf\Mapper\ResumeMapper;
use Illuminate\Support\Facades\Storage;

/**
 * Persists a resume's rendered PDF on disk so the public page and downloads
 * reuse it instead of shelling out to resume-gen on every request - it's
 * only regenerated when the resume is saved (see ResumeController).
 */
class ResumePdfCache
{
    public function __construct(
        private readonly ResumeMapper $mapper,
        private readonly PdfGeneratorInterface $pdf,
    ) {}

    public function store(Resume $resume): void
    {
        $resume->loadMissing(UpdateResume::RESUME_RELATIONS);

        Storage::disk('local')->put(
            $this->path($resume),
            $this->pdf->generate($this->mapper->mapFromResume($resume)),
        );
    }

    public function forget(Resume $resume): void
    {
        Storage::disk('local')->delete($this->path($resume));
    }

    /** Returns the storage path, generating the PDF first if it isn't cached yet. */
    public function ensure(Resume $resume): string
    {
        $path = $this->path($resume);

        if (! Storage::disk('local')->exists($path)) {
            $this->store($resume);
        }

        return $path;
    }

    private function path(Resume $resume): string
    {
        return "resumes/{$resume->slug}.pdf";
    }
}
