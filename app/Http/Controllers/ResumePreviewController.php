<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewResumeRequest;
use App\Services\Pdf\Mapper\ResumeMapper;
use App\Services\Pdf\PdfGeneratorInterface;
use Illuminate\Http\Response;

/**
 * Renders a PDF from in-progress form data for the live editor preview.
 */
class ResumePreviewController extends Controller
{
    public function __invoke(PreviewResumeRequest $request, ResumeMapper $mapper, PdfGeneratorInterface $pdf): Response
    {
        $payload = $mapper->mapFromArray($request->validated());

        return response($pdf->generate($payload), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }
}
