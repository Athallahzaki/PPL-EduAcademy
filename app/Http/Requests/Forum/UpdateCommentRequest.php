<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() === true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:3', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Comment cannot be empty.',
            'comment.min' => 'Comment must be at least 3 characters.',
            'comment.max' => 'Comment must not exceed 5000 characters.',
        ];
    }
}
