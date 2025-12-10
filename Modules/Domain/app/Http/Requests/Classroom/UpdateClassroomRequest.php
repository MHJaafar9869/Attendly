<?php

namespace Modules\Domain\Http\Requests\Classroom;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClassroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for this request.
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['sometimes', 'required', 'string', 'max:255', 'exists:teachers,id'],
            'subject_id' => ['sometimes', 'required', 'integer', 'exists:subjects,id'],
            'start_at' => ['sometimes', 'required', 'date'],
            'end_at' => ['sometimes', 'required', 'date'],
            'lat' => ['sometimes', 'required', 'numeric'],
            'lng' => ['sometimes', 'required', 'numeric'],
            'radius' => ['sometimes', 'required', 'boolean'],
            'created_by' => ['nullable', 'sometimes', 'string', 'max:255'],
            'updated_by' => ['nullable', 'sometimes', 'string', 'max:255'],
            'deleted_by' => ['nullable', 'sometimes', 'string', 'max:255'],
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
