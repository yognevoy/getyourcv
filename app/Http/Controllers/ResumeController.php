<?php

namespace App\Http\Controllers;

use App\Actions\Resume\CreateResume;
use App\Actions\Resume\DeleteResume;
use App\Actions\Resume\DuplicateResume;
use App\Actions\Resume\ForceDeleteResume;
use App\Actions\Resume\RestoreResume;
use App\Actions\Resume\UpdateResume;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeTemplateResource;
use App\Models\Resume;
use App\Services\Pdf\ResumePdfStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeController extends Controller
{
    public function index(): Response
    {
        $resumes = Auth::user()
            ->resumes()
            ->withCount('views')
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'resumes' => $resumes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resume/Create');
    }

    public function trash(): Response
    {
        $resumes = Auth::user()
            ->resumes()
            ->onlyTrashed()
            ->latest('deleted_at')
            ->get();

        return Inertia::render('Resume/Trash', [
            'resumes' => $resumes,
        ]);
    }

    public function store(StoreResumeRequest $request, CreateResume $action): RedirectResponse
    {
        $resume = $action->execute(Auth::user(), $request->validated());

        return redirect()
            ->route('dashboard')
            ->with('resume', $resume);
    }

    public function edit(Resume $resume): Response
    {
        $this->authorize('update', $resume);

        $resume->load(UpdateResume::RESUME_RELATIONS);

        return Inertia::render('Resume/Edit', [
            'resume' => new ResumeTemplateResource($resume),
        ]);
    }

    public function update(UpdateResumeRequest $request, Resume $resume, UpdateResume $action): RedirectResponse
    {
        $action->execute($resume, $request->validated());

        return redirect()->route('dashboard');
    }

    public function pdf(Resume $resume, ResumePdfStore $pdfStore): StreamedResponse
    {
        $this->authorize('view', $resume);

        return Storage::disk('local')->download($pdfStore->ensure($resume), "{$resume->slug}.pdf");
    }

    public function destroy(Resume $resume, DeleteResume $action): RedirectResponse
    {
        $this->authorize('delete', $resume);

        $action->execute($resume);

        return back();
    }

    public function restore(Resume $resume, RestoreResume $action): RedirectResponse
    {
        $this->authorize('restore', $resume);

        $action->execute($resume);

        return back();
    }

    public function forceDestroy(Resume $resume, ForceDeleteResume $action): RedirectResponse
    {
        $this->authorize('forceDelete', $resume);

        $action->execute($resume);

        return back();
    }

    public function duplicate(Resume $resume, DuplicateResume $action): RedirectResponse
    {
        $this->authorize('view', $resume);

        $clone = $action->execute($resume);

        return redirect()->route('resumes.edit', $clone);
    }

    public function archive(Resume $resume): RedirectResponse
    {
        $this->authorize('update', $resume);

        $resume->update(['archived_at' => now()]);

        return back();
    }

    public function unarchive(Resume $resume): RedirectResponse
    {
        $this->authorize('update', $resume);

        $resume->update(['archived_at' => null]);

        return back();
    }
}
