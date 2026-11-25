<?php

namespace App\Http\Requests\Instructor;

use App\Enums\ChapterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', $this->route('course'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(ChapterType::class)],
            'body' => ['required_if:type,' . ChapterType::Text->value, 'nullable', 'string', 'max:50000'],
            'video_url' => ['required_if:type,' . ChapterType::Video->value, 'nullable', 'url:http,https', 'max:500'],
        ];
    }
}
