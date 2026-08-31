<?php

namespace App\Services\Ai\RewriteTarget;

use App\Services\Ai\RewriteTarget;

final class About extends RewriteTarget
{
    public const VALUE = 'about';

    public function value(): string
    {
        return self::VALUE;
    }

    public function subject(): string
    {
        return 'the "About" section of a resume (a short professional summary paragraph)';
    }
}
