<?php

namespace App\Services\Pdf;

interface PdfGeneratorInterface
{
    /**
     * Generates a PDF from the resume's JSON data.
     */
    public function generate(array $payload): string;
}
