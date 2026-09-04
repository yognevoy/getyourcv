<?php

namespace App\Models;

use App\Enums\ResumeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resume extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'status',
        'archived_at',
        'photo_path',
        'full_name',
        'position',
        'email',
        'about',
        'current_version_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => ResumeStatus::class,
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ResumeLink::class)->orderBy('position');
    }

    public function skillGroups(): HasMany
    {
        return $this->hasMany(ResumeSkillGroup::class)->orderBy('position');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(ResumeExperience::class)->orderBy('position');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(ResumeEducation::class)->orderBy('position');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(ResumeCourse::class)->orderBy('position');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(ResumeCertification::class)->orderBy('position');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ResumeVersion::class);
    }

    public function vacancyMatches(): HasMany
    {
        return $this->hasMany(ResumeVacancyMatch::class)->latest();
    }

    public function views(): HasMany
    {
        return $this->hasMany(ResumeView::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class, 'current_version_id');
    }

    public function isAvailable(): bool
    {
        return ! $this->trashed()
            && $this->archived_at === null
            && $this->status === ResumeStatus::Published;
    }
}
