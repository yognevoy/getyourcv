<?php

namespace App\Services\Pdf;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Shells out to the resume-gen CLI (github.com/yognevoy/resume-gen) to render a resume.
 * Kept behind PdfGeneratorInterface so it can be mocked in tests without spawning a process.
 */
class ResumePdfGenerator implements PdfGeneratorInterface
{
    public function generate(array $payload): string
    {
        $inputPath = sys_get_temp_dir().'/'.Str::uuid().'.json';
        $outputPath = sys_get_temp_dir().'/'.Str::uuid().'.pdf';

        file_put_contents($inputPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        try {
            $result = Process::run([
                config('services.resume_gen.binary'),
                '-i', $inputPath,
                '-o', $outputPath,
            ]);

            if ($result->failed()) {
                throw new RuntimeException('resume-gen failed: '.$result->errorOutput());
            }

            return file_get_contents($outputPath);
        } finally {
            @unlink($inputPath);
            @unlink($outputPath);
        }
    }
}
