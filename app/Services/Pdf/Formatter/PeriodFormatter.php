<?php

namespace App\Services\Pdf\Formatter;

use Carbon\Carbon;

/**
 * Formats a date range the way resume-gen expects, e.g. "March 2023 — Present".
 */
class PeriodFormatter
{
    public function format(?string $from, ?string $to, bool $isCurrent): string
    {
        $fromLabel = $from ? Carbon::parse($from)->format('F Y') : null;
        $toLabel = $isCurrent ? 'Present' : ($to ? Carbon::parse($to)->format('F Y') : null);

        return implode(' — ', array_filter([$fromLabel, $toLabel]));
    }
}
