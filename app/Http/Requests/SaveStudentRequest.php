<?php

namespace App\Http\Requests;

use App\Models\laravelstudents;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStudents() ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('student_name')) {
            $merge['student_name'] = trim((string) $this->input('student_name'));
        }

        if ($this->has('email')) {
            $merge['email'] = strtolower(trim((string) $this->input('email')));
        }

        if ($this->has('phone_number')) {
            $merge['phone_number'] = trim((string) $this->input('phone_number'));
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $studentParam = $this->route('student');
        $ignoreId = $studentParam instanceof laravelstudents
            ? $studentParam->id
            : ($studentParam !== null ? (int) $studentParam : null);

        return [
            'student_name' => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'email',
                'max:255',
                Rule::unique('laravel_students', 'email')->ignore($ignoreId),
            ],
            'phone_number' => [
                'required',
                'numeric',
                Rule::unique('laravel_students', 'phone_number')->ignore($ignoreId),
            ],
            'gender'       => ['required', Rule::in(laravelstudents::GENDERS)],
            'course_id'    => ['required', 'integer', Rule::exists('courses', 'id')],
        ];
    }
}

