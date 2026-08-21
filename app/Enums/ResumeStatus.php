<?php

namespace App\Enums;

enum ResumeStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';
    case Archived = 'archived';
}
