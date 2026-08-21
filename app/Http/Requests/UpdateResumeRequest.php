<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasResumeRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeRequest extends FormRequest
{
    use HasResumeRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('resume'));
    }

    public function rules(): array
    {
        return $this->resumeRules();
    }
}
