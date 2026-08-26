<?php

namespace App\Http\Requests;

use App\Enums\ResumeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResumeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('resume'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ResumeStatus::class)],
        ];
    }
}
