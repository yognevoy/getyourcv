<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeVacancyMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'resume_version_id',
        'vacancy_title',
        'vacancy_text',
        'vacancy_text_hash',
        'score',
        'matched_skills',
        'missing_skills',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'matched_skills' => 'array',
            'missing_skills' => 'array',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}
