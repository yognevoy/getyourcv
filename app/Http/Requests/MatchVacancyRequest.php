<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatchVacancyRequest extends FormRequest
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
            'vacancy_title' => ['nullable', 'string', 'max:255'],
            'vacancy_text' => ['required', 'string', 'min:30', 'max:20000'],
        ];
    }
}
