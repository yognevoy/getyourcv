<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],

            'links' => ['array'],
            'links.*.label' => ['nullable', 'string', 'max:255'],
            'links.*.url' => ['nullable', 'string', 'max:2048'],

            'skill_groups' => ['array'],
            'skill_groups.*.label' => ['nullable', 'string', 'max:255'],
            'skill_groups.*.skills' => ['array'],
            'skill_groups.*.skills.*.value' => ['nullable', 'string', 'max:255'],

            'experiences' => ['array'],
            'experiences.*.company' => ['nullable', 'string', 'max:255'],
            'experiences.*.title' => ['nullable', 'string', 'max:255'],
            'experiences.*.period_from' => ['nullable', 'date'],
            'experiences.*.period_to' => ['nullable', 'date'],
            'experiences.*.is_current' => ['boolean'],
            'experiences.*.bullets' => ['array'],
            'experiences.*.bullets.*.type' => ['nullable', 'in:responsibility,achievement'],
            'experiences.*.bullets.*.text' => ['nullable', 'string'],

            'educations' => ['array'],
            'educations.*.institution' => ['nullable', 'string', 'max:255'],
            'educations.*.field' => ['nullable', 'string', 'max:255'],
            'educations.*.period_from' => ['nullable', 'date'],
            'educations.*.period_to' => ['nullable', 'date'],

            'courses' => ['array'],
            'courses.*.title' => ['nullable', 'string', 'max:255'],
            'courses.*.provider' => ['nullable', 'string', 'max:255'],

            'certifications' => ['array'],
            'certifications.*.title' => ['nullable', 'string', 'max:255'],
            'certifications.*.provider' => ['nullable', 'string', 'max:255'],
        ];
    }
}
