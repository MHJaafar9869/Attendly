<?php

namespace Modules\Domain\Http\Requests\Classroom;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClassroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return sanctumUser()?->hasAnyRole(['super_admin', 'admin']) || sanctumUser()?->hasPermission('create_classrooms');
    }

    /**
     * Define validation rules for this request.
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'string', 'max:255', 'exists:teachers,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius' => ['required', 'integer', 'min:0', 'max:255'],
            'students_ids' => ['required', 'array'],
            'students_ids.*' => ['required', 'integer', 'exists:students,id'],
        ];
    }

    /**
     * Customize validation failure response.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
