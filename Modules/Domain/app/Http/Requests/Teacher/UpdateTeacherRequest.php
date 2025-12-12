<?php

namespace Modules\Domain\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final readonly class UpdateTeacherRequest extends FormRequest
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
            'user_id' => ['sometimes', 'nullable', 'string', 'max:255', 'exists:users,id'],
            'teacher_code' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:teachers,teacher_code,' . $this->route('teacher') . ',id'],
            'teacher_type_id' => ['sometimes', 'nullable', 'integer'],
            'status_id' => ['sometimes', 'nullable', 'integer', 'exists:statuses,id'],
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
