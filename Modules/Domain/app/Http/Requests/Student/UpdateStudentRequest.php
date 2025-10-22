<?php

namespace Modules\Domain\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateStudentRequest extends FormRequest
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
            'user_id' => ['sometimes', 'required', 'string', 'max:255', 'exists:users,id'],
            'student_code' => ['sometimes', 'required', 'string', 'max:255', 'unique:students,student_code,'.$this->route('student').',id'],
            'hashed_national_id' => ['sometimes', 'required', 'string', 'max:255'],
            'gender' => ['sometimes', 'required', 'string'],
            'academic_year' => ['sometimes', 'required', 'string', 'max:255'],
            'section' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'sometimes', 'string', 'max:255', 'unique:students,phone,'.$this->route('student').',id'],
            'secondary_phone' => ['nullable', 'sometimes', 'string', 'max:255', 'unique:students,secondary_phone,'.$this->route('student').',id'],
            'address' => ['nullable', 'sometimes', 'string'],
            'city' => ['nullable', 'sometimes', 'string', 'max:255'],
            'governorate_id' => ['nullable', 'sometimes', 'integer', 'exists:governorates,id'],
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
            'errors' => $validator->errors()
        ], 422));
    }
}
