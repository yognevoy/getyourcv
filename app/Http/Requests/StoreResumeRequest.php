<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasResumeRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    use HasResumeRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->resumeRules();
    }
}
