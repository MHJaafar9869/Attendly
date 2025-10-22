<?php

namespace Modules\Domain\Http\Requests\Student;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStudentRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'max:255', 'exists:users,id'],
            'student_code' => ['required', 'string', 'max:255', 'unique:students,student_code'],
            'hashed_national_id' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'academic_year' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:students,phone'],
            'secondary_phone' => ['nullable', 'string', 'max:255', 'unique:students,secondary_phone'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
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
