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
        return auth()->check() && sanctumUser()->email_verified_at !== null;
    }

    /**
     * Define validation rules for this request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'max:255', 'exists:users,id'],
            'national_id' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female'],
            'academic_level_id' => ['nullable', 'integer', 'exists:academic_levels,id'],
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
