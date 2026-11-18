<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThreadRequest extends FormRequest
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
            'title.required' => 'Judul topik diskusi wajib diisi.',
            'title.min' => 'Judul topik diskusi minimal 5 karakter.',
            'subject_id.required' => 'Kategori mata pelajaran wajib dipilih.',
            'subject_id.exists' => 'Kategori mata pelajaran tidak valid.',
            'content.required' => 'Isi topik diskusi wajib diisi.',
            'content.min' => 'Isi topik diskusi minimal 10 karakter.',
        ];
    }
}
