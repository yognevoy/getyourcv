<?php

namespace App\Services\Ai\RewriteTarget;

use App\Services\Ai\RewriteTarget;

final class Responsibility extends RewriteTarget
{
    public const VALUE = 'responsibility';

    public function value(): string
    {
        return self::VALUE;
    }

    public function subject(): string
    {
        return 'a single "Responsibilities" bullet point from a resume - an ongoing duty or task the person performed';
    }
}
