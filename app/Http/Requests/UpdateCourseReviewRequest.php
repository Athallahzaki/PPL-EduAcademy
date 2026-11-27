<?php

namespace App\Http\Requests;

use App\Models\CourseReview;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');

        return $review instanceof CourseReview
            && $this->user()?->can('update', $review) === true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
