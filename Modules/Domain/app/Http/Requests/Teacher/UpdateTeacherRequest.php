<?php

namespace Modules\Domain\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTeacherRequest extends FormRequest
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
            'teacher_code' => ['sometimes', 'required', 'string', 'max:255', 'unique:teachers,teacher_code,' . $this->route('teacher') . ',id'],
            'teacher_type_id' => ['sometimes', 'required', 'integer'],
            'status_id' => ['sometimes', 'required', 'integer', 'exists:statuses,id'],
            'approved_by' => ['nullable', 'sometimes', 'string', 'max:255'],
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
