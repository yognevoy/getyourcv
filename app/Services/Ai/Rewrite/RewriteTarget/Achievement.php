<?php

namespace App\Services\Ai\Rewrite\RewriteTarget;

use App\Services\Ai\Rewrite\RewriteTarget;

final class Achievement extends RewriteTarget
{
    public const VALUE = 'achievement';

    public function value(): string
    {
        return self::VALUE;
    }

    public function subject(): string
    {
        return 'a single "Achievements" bullet point from a resume - a specific accomplishment or measurable result the person delivered';
    }
}
