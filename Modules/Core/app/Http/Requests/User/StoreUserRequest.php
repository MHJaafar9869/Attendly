<?php

namespace Modules\Core\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'slug_name' => ['nullable', 'string', 'max:255', 'unique:users,slug_name'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'is_verified' => ['required', 'boolean'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'device' => ['nullable', 'string'],
            'token_version' => ['required', 'integer'],
            'otp' => ['nullable', 'string', 'max:255'],
            'otp_expires_at' => ['nullable', 'date'],
            'last_visited_at' => ['nullable', 'date'],
            'email_verified_at' => ['nullable', 'date'],
            'remember_token' => ['nullable', 'string', 'max:255'],
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
