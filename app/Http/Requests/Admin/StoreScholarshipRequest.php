<?php

namespace App\Http\Requests\Admin;

use App\Enums\ScholarshipStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScholarshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'requirements' => ['required', 'string', 'max:65535'],
            'quota' => ['required', 'integer', 'min:1', 'max:1000000'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::enum(ScholarshipStatus::class)],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul beasiswa',
            'description' => 'deskripsi beasiswa',
            'requirements' => 'persyaratan beasiswa',
            'quota' => 'kuota beasiswa',
            'start_date' => 'tanggal mulai pendaftaran',
            'deadline' => 'tenggat waktu pendaftaran',
            'status' => 'status publikasi',
        ];
    }
}
