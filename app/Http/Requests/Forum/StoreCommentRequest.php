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
                    $validator->errors()->add('parent_id', 'The replied comment must belong to the same discussion topic.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Comment cannot be empty.',
            'comment.min' => 'Comment must be at least 3 characters.',
            'comment.max' => 'Comment must not exceed 5000 characters.',
            'parent_id.exists' => 'The comment you are replying to could not be found.',
        ];
    }
}
