<?php

namespace App\Http\Controllers;

use App\Actions\Resume\Match\MatchResumeToVacancy;
use App\Http\Requests\MatchVacancyRequest;
use App\Models\Resume;
use App\Services\Ai\Exceptions\AiServiceException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ResumeVacancyMatchController extends Controller
{
    public function index(Resume $resume): Response
    {
        $this->authorize('view', $resume);

        return Inertia::render('Resume/Match', [
            'resume' => $resume->only(['id', 'title', 'full_name', 'position']),
            'matches' => $resume->vacancyMatches()->get([
                'id', 'vacancy_title', 'score', 'matched_skills', 'missing_skills', 'summary', 'created_at',
            ]),
        ]);
    }

    public function store(MatchVacancyRequest $request, Resume $resume, MatchResumeToVacancy $action): RedirectResponse
    {
        $this->authorize('view', $resume);

        try {
            $action->execute(
                $resume,
                $request->validated('vacancy_title'),
                $request->validated('vacancy_text'),
            );
        } catch (AiServiceException $e) {
            return back()->withErrors(['vacancy_text' => $e->getMessage()]);
        }

        return back();
    }
}
