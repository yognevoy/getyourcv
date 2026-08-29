<?php

namespace App\Http\Controllers;

use App\Actions\Resume\Version\DeleteResumeVersion;
use App\Actions\Resume\Version\RestoreResumeVersion;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Services\Pdf\Mapper\ResumeMapper;
use App\Services\Pdf\PdfGeneratorInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ResumeVersionController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->authorize('view', $resume);

        $versions = $resume->versions()
            ->orderByRaw('(id = ?) DESC', [$resume->current_version_id])
            ->orderByDesc('id')
            ->get(['id', 'created_at']);

        return response()->json([
            'versions' => $versions->map(fn (ResumeVersion $version) => [
                'id' => $version->id,
                'created_at' => $version->created_at,
                'is_current' => $version->id === $resume->current_version_id,
            ]),
        ]);
    }

    public function pdf(Resume $resume, ResumeVersion $version, ResumeMapper $mapper, PdfGeneratorInterface $pdf): Response
    {
        $this->authorize('view', $resume);
        abort_unless($version->resume_id === $resume->id, 404);

        $payload = $mapper->mapFromArray($version->snapshot);

        return response($pdf->generate($payload), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="version.pdf"',
        ]);
    }

    public function restore(Resume $resume, ResumeVersion $version, RestoreResumeVersion $action): RedirectResponse
    {
        $this->authorize('update', $resume);
        abort_unless($version->resume_id === $resume->id, 404);

        $action->execute($resume, $version);

        return back();
    }

    public function destroy(Resume $resume, ResumeVersion $version, DeleteResumeVersion $action): RedirectResponse
    {
        $this->authorize('update', $resume);
        abort_unless($version->resume_id === $resume->id, 404);

        $action->execute($version);

        return back();
    }
}
