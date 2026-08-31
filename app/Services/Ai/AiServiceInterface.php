<?php

namespace App\Services\Ai;

use App\Services\Ai\Exceptions\AiServiceException;

interface AiServiceInterface
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
