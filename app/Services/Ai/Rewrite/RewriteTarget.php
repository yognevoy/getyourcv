<?php

namespace App\Services\Ai\Rewrite;

use App\Services\Ai\Rewrite\RewriteTarget\About;
use App\Services\Ai\Rewrite\RewriteTarget\Achievement;
use App\Services\Ai\Rewrite\RewriteTarget\Responsibility;
use InvalidArgumentException;

abstract class RewriteTarget
{
    abstract public function value(): string;

    abstract public function subject(): string;

    public static function fromValue(string $value): self
    {
        return match ($value) {
            About::VALUE => new About,
            Responsibility::VALUE => new Responsibility,
            Achievement::VALUE => new Achievement,
            default => throw new InvalidArgumentException("Unknown rewrite target: {$value}"),
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [About::VALUE, Responsibility::VALUE, Achievement::VALUE];
    }
}
