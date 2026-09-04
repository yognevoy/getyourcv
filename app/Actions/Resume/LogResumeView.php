<?php

namespace App\Actions\Resume;

use App\Http\Security\ViewerHash;
use App\Models\Resume;
use App\Models\ResumeView;
use Illuminate\Http\Request;

/**
 * Logs a public resume view.
 */
class LogResumeView
{
    public function execute(Resume $resume, Request $request): void
    {
        if ($request->user()?->id === $resume->user_id) {
            return;
        }

        ResumeView::create([
            'resume_id' => $resume->id,
            'viewer_hash' => ViewerHash::fromRequest($request),
        ]);
    }
}
