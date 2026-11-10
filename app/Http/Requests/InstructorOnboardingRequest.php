<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isInstructor() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio' => ['required', 'string', 'max:1000'],
            'expertise' => ['required', 'string', 'max:255'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}

