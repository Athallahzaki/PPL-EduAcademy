<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'content' => ['required', 'string', 'min:10', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The topic title is required.',
            'title.min' => 'The topic title must be at least 5 characters.',
            'subject_id.required' => 'Please select a subject category.',
            'subject_id.exists' => 'The selected subject category is invalid.',
            'content.required' => 'The topic content is required.',
            'content.min' => 'The topic content must be at least 10 characters.',
        ];
    }
}
