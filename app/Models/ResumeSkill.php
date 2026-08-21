<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_skill_group_id',
        'value',
        'position',
    ];

    public function skillGroup(): BelongsTo
    {
        return $this->belongsTo(ResumeSkillGroup::class, 'resume_skill_group_id');
    }
}
