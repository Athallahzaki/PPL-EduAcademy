<?php

namespace App\Http\Requests\Forum;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() === true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:3', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $parentId = $this->input('parent_id');
            if ($parentId) {
                $thread = $this->route('thread');
                $parentComment = Comment::find($parentId);

                if ($parentComment && $thread && $parentComment->thread_id !== $thread->id) {
                    $validator->errors()->add('parent_id', 'Komentar yang dibalas harus berasal dari topik diskusi yang sama.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Isi tanggapan tidak boleh kosong.',
            'comment.min' => 'Isi tanggapan minimal 3 karakter.',
            'comment.max' => 'Isi tanggapan maksimal 5000 karakter.',
            'parent_id.exists' => 'Komentar yang ingin Anda balas tidak ditemukan.',
        ];
    }
}
