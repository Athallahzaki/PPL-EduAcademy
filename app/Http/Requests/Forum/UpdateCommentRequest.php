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
            'comment.required' => 'Isi tanggapan tidak boleh kosong.',
            'comment.min' => 'Isi tanggapan minimal 3 karakter.',
            'comment.max' => 'Isi tanggapan maksimal 5000 karakter.',
        ];
    }
}
