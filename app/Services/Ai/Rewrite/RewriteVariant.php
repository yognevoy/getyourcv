<?php

namespace App\Services\Ai\Rewrite;

enum RewriteVariant: string
{
    case Shorter = 'shorter';
    case Stronger = 'stronger';
    case WithNumbers = 'with_numbers';

    public function description(): string
    {
        return match ($this) {
            self::Shorter => 'A more concise rewrite.',
            self::Stronger => 'A more confident, impactful rewrite.',
            self::WithNumbers => 'A rewrite that foregrounds quantifiable impact.',
        };
    }
}
