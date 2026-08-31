<?php

namespace App\Http\Requests;

use App\Services\Ai\RewriteTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RewriteTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:2000'],
            'target' => ['required', Rule::in(RewriteTarget::values())],
        ];
    }
}
