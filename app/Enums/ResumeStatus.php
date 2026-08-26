<?php

namespace App\Enums;

enum ResumeStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';
    case Archived = 'archived';

    public function isPublic(): bool
    {
        return match ($this) {
            self::Draft, self::Published => true,
            self::Hidden, self::Archived => false,
        };
    }
}
