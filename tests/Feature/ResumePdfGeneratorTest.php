<?php

use App\Services\Pdf\ResumePdfGenerator;
use Illuminate\Support\Facades\Process;

it('bounds resume-gen with an explicit timeout so a stuck binary cannot hold the worker forever', function () {
    Process::fake(function ($process) {
        expect($process->timeout)->toBe(10);
        file_put_contents($process->command[4], 'fake-pdf-bytes');

        return Process::result();
    });

    $pdf = app(ResumePdfGenerator::class)->generate(['full_name' => 'Jane Doe']);

    expect($pdf)->toBe('fake-pdf-bytes');
});

it('cleans up the input temp file even when resume-gen fails', function () {
    $inputPath = null;

    Process::fake(function ($process) use (&$inputPath) {
        $inputPath = $process->command[2];

        return Process::result(errorOutput: 'boom', exitCode: 1);
    });

    expect(fn () => app(ResumePdfGenerator::class)->generate(['full_name' => 'Jane Doe']))
        ->toThrow(RuntimeException::class, 'resume-gen failed: boom');

    expect(file_exists($inputPath))->toBeFalse();
});
