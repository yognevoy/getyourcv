<?php

namespace App\Services\Pdf;

use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Shells out to the resume-gen CLI (github.com/yognevoy/resume-gen) to render a resume.
 * Kept behind PdfGeneratorInterface so it can be mocked in tests without spawning a process.
 */
class ResumePdfGenerator implements PdfGeneratorInterface
{
    private const TIMEOUT_SECONDS = 10;

    public function generate(array $payload): string
    {
        $inputPath = sys_get_temp_dir().'/'.Str::uuid().'.json';
        $outputPath = sys_get_temp_dir().'/'.Str::uuid().'.pdf';

        file_put_contents($inputPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        try {
            try {
                $result = Process::timeout(self::TIMEOUT_SECONDS)->run([
                    config('services.resume_gen.binary'),
                    '-i', $inputPath,
                    '-o', $outputPath,
                ]);
            } catch (ProcessTimedOutException $e) {
                throw new RuntimeException('resume-gen timed out after '.self::TIMEOUT_SECONDS.'s', previous: $e);
            }

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
