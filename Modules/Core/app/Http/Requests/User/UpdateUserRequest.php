<?php

namespace Modules\Core\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'max:255', 'unique:users,email,'.$this->route('user').',id'],
            'email_verified_at' => ['nullable', 'sometimes', 'date'],
            'phone' => ['nullable', 'sometimes', 'string', 'max:255', 'unique:users,phone,'.$this->route('user').',id'],
            'address' => ['nullable', 'sometimes', 'string'],
            'password' => ['sometimes', 'required', 'string', 'max:255'],
            'role_id' => ['nullable', 'sometimes', 'integer', 'exists:roles,id'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'type_id' => ['nullable', 'sometimes', 'integer', 'exists:types,id'],
            'status_id' => ['sometimes', 'required', 'integer', 'exists:statuses,id'],
            'remember_token' => ['nullable', 'sometimes', 'string', 'max:255'],
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
