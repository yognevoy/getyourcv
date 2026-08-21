<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResumeExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'company',
        'title',
        'period_from',
        'period_to',
        'is_current',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function bullets(): HasMany
    {
        return $this->hasMany(ResumeExperienceBullet::class)->orderBy('position');
    }
}
