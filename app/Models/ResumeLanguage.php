<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'language',
        'level',
        'position',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
