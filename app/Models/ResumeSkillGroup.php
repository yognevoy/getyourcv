<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResumeSkillGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'label',
        'position',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(ResumeSkill::class)->orderBy('position');
    }
}
