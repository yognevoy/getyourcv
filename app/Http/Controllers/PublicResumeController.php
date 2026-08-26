<?php

namespace App\Http\Controllers;

use App\Actions\Resume\UpdateResume;
use App\Http\Resources\ResumeTemplateResource;
use App\Models\Resume;
use Inertia\Inertia;
use Inertia\Response;

class PublicResumeController extends Controller
{
    public function show(Resume $resume): Response
    {
        if ($resume->trashed() || ! $resume->status->isPublic()) {
            return Inertia::render('Public/Resume', [
                'available' => false,
            ]);
        }

        $resume->load(UpdateResume::RESUME_RELATIONS);

        return Inertia::render('Public/Resume', [
            'available' => true,
            'resume' => new ResumeTemplateResource($resume),
        ]);
    }
}
