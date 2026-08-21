<?php

namespace App\Models;

use App\Enums\ExperienceBulletType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeExperienceBullet extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_experience_id',
        'type',
        'text',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExperienceBulletType::class,
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(ResumeExperience::class, 'resume_experience_id');
    }
}
