<?php

namespace App\Services\Ai\Rewrite;

use App\Services\Ai\Exceptions\AiServiceException;

interface RewriteServiceInterface
{
    /**
     * Rewrites the given resume text into three variants.
     *
     * @return array{shorter: string, stronger: string, with_numbers: string}
     *
     * @throws AiServiceException
     */
    public function rewrite(string $text, RewriteTarget $target): array;
}
