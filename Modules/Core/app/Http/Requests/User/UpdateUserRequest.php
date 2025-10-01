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
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug_name' => ['nullable', 'sometimes', 'string', 'max:255', 'unique:users,slug_name,'.$this->route('user').',id'],
            'email' => ['sometimes', 'required', 'string', 'max:255', 'unique:users,email,'.$this->route('user').',id'],
            'phone' => ['nullable', 'sometimes', 'string', 'max:255', 'unique:users,phone,'.$this->route('user').',id'],
            'password' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['nullable', 'sometimes', 'string'],
            'city' => ['nullable', 'sometimes', 'string', 'max:255'],
            'country' => ['nullable', 'sometimes', 'string', 'max:255'],
            'is_verified' => ['sometimes', 'required', 'boolean'],
            'role_id' => ['nullable', 'sometimes', 'integer', 'exists:roles,id'],
            'status_id' => ['sometimes', 'required', 'integer', 'exists:statuses,id'],
            'device' => ['nullable', 'sometimes', 'string'],
            'token_version' => ['sometimes', 'required', 'integer'],
            'otp' => ['nullable', 'sometimes', 'string', 'max:255'],
            'otp_expires_at' => ['nullable', 'sometimes', 'date'],
            'last_visited_at' => ['nullable', 'sometimes', 'date'],
            'email_verified_at' => ['nullable', 'sometimes', 'date'],
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
