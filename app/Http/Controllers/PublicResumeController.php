<?php

namespace App\Http\Controllers;

use App\Actions\Resume\LogResumeView;
use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicResumeController extends Controller
{
    public function show(Request $request, Resume $resume, LogResumeView $action): \Inertia\Response
    {
        if (!$resume->isAvailable()) {
            return Inertia::render('Public/Resume', [
                'available' => false,
            ]);
        }

        $action->execute($resume, $request);

        return Inertia::render('Public/Resume', [
            'available' => true,
            'resume' => [
                'slug' => $resume->slug,
                'full_name' => $resume->full_name,
            ],
        ]);
    }

    public function file(Resume $resume, ResumePdfStore $pdfStore): StreamedResponse
    {
        abort_unless($resume->isAvailable(), 404);

        return Storage::disk('local')->response($pdfStore->ensure($resume), "{$resume->slug}.pdf");
    }
}
