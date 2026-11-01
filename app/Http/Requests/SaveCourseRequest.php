<?php

namespace App\Http\Requests;

use App\Models\courses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageCourses() ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('course_name')) {
            $this->merge([
                'course_name' => trim((string) $this->input('course_name')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $courseParam = $this->route('course');
        $ignoreId = $courseParam instanceof courses
            ? $courseParam->id
            : ($courseParam !== null ? (int) $courseParam : null);

        return [
            'course_name'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'course_name')->ignore($ignoreId),
            ],
            'description'    => ['nullable', 'string'],
            'duration_hours' => ['nullable', 'string', 'max:100'],
            'course_fee'     => ['nullable', 'string', 'max:100'],
        ];
    }
}

