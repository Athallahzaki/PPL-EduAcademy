<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isStudent() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'education_level' => ['required', 'string', 'in:elementary,junior_high,senior_high,university,other'],
        ];
    }
}

