<?php

namespace App\Rules;

use App\Services\Photo\ResumePhotoStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ResumePhotoRule implements ValidationRule
{
    public function __construct(private readonly ResumePhotoStore $photoStore = new ResumePhotoStore) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^resume-photos\/[0-9a-f-]{36}\.(jpe?g|png|webp)$/i', $value)) {
            $fail('The :attribute is invalid.');

            return;
        }

        if (! $this->photoStore->exists($value)) {
            $fail('The selected photo could not be found.');
        }
    }
}
